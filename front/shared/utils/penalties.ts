import type { IsoDate } from '../types'

/**
 * Taux du 2d semestre 2026. À mettre à jour chaque 1er janvier et 1er juillet.
 * Sources : Code de commerce art. L441-10 et D441-5, Banque de France, arrêté du 26 juin 2026.
 */
export const PENALTY_RATES = {
  period: '2d semestre 2026',
  /** Taux par défaut entre professionnels : taux BCE (2,40 %) + 10 points */
  defaultRate: 12.4,
  /** Minimum légal : 3 × le taux de l'intérêt légal (2,75 %) */
  minimumRate: 8.25,
  /** Client particulier : taux de l'intérêt légal (créancier professionnel) */
  consumerRate: 2.75,
  /** Indemnité forfaitaire pour frais de recouvrement, par facture */
  indemnityCents: 4000,
} as const

export type ClientType = 'professional' | 'consumer'

export interface PenaltyInput {
  /** Reste dû TTC, en centimes */
  amountCents: number
  dueAt: IsoDate
  /** Date du paiement, ou d'aujourd'hui si la facture n'est pas payée */
  paidAt: IsoDate
  clientType: ClientType
  /** Taux des conditions générales, en %. Absent : taux par défaut */
  contractRate?: number
  /** Particulier : date de la mise en demeure, point de départ des intérêts */
  formalNoticeAt?: IsoDate
}

export interface PenaltyResult {
  /** Jours pris en compte dans le calcul */
  days: number
  /** Taux annuel appliqué, en % */
  rate: number
  penaltyCents: number
  indemnityCents: number
  totalCents: number
  /** Le taux du contrat était sous le minimum légal : le minimum a été appliqué */
  belowMinimum: boolean
}

/** Nombre de jours entre deux dates (négatif si `to` est avant `from`) */
export function daysBetween(from: IsoDate, to: IsoDate): number {
  const toUtc = (date: IsoDate) => {
    const [year = 0, month = 1, day = 1] = date.split('-').map(Number)
    return Date.UTC(year, month - 1, day)
  }
  return Math.round((toUtc(to) - toUtc(from)) / 86_400_000)
}

export function computeLatePenalties(input: PenaltyInput): PenaltyResult {
  const { amountCents, dueAt, paidAt, clientType, contractRate, formalNoticeAt } = input
  let days = 0
  let rate: number
  let indemnityCents = 0
  let belowMinimum = false

  if (clientType === 'professional') {
    days = Math.max(0, daysBetween(dueAt, paidAt))
    rate = contractRate ?? PENALTY_RATES.defaultRate
    if (rate < PENALTY_RATES.minimumRate) {
      belowMinimum = true
      rate = PENALTY_RATES.minimumRate
    }
    indemnityCents = days > 0 ? PENALTY_RATES.indemnityCents : 0
  }
  else {
    rate = PENALTY_RATES.consumerRate
    if (formalNoticeAt) {
      // Les intérêts courent à partir de la mise en demeure, jamais avant l'échéance
      const start = daysBetween(dueAt, formalNoticeAt) > 0 ? formalNoticeAt : dueAt
      days = Math.max(0, daysBetween(start, paidAt))
    }
  }

  // Tout en centimes entiers ; un seul arrondi, à la fin
  const penaltyCents = Math.round((amountCents * rate * days) / 36_500)

  return {
    days,
    rate,
    penaltyCents,
    indemnityCents,
    totalCents: amountCents + penaltyCents + indemnityCents,
    belowMinimum,
  }
}
