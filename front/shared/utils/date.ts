import type { IsoDate } from '../types'

export function todayIso(): IsoDate {
  const now = new Date()
  const pad = (n: number) => String(n).padStart(2, '0')
  return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`
}

export function addDays(isoDate: IsoDate, days: number): IsoDate {
  const [year = 0, month = 1, day = 1] = isoDate.split('-').map(Number)
  return new Date(Date.UTC(year, month - 1, day + days)).toISOString().slice(0, 10)
}

/** Dernier jour du mois de la date donnée */
export function endOfMonth(isoDate: IsoDate): IsoDate {
  const [year = 0, month = 1] = isoDate.split('-').map(Number)
  // Le jour 0 du mois suivant = le dernier jour du mois courant
  return new Date(Date.UTC(year, month, 0)).toISOString().slice(0, 10)
}

/** Délais de paiement courants (art. L441-10 du Code de commerce) */
export type PaymentTerm = '30' | '45-eom' | '60'

export const PAYMENT_TERM_LABELS: Record<PaymentTerm, string> = {
  '30': '30 jours',
  '45-eom': '45 jours fin de mois',
  '60': '60 jours',
}

export function dueDateFor(issuedAt: IsoDate, term: PaymentTerm): IsoDate {
  if (term === '45-eom') return endOfMonth(addDays(issuedAt, 45))
  return addDays(issuedAt, Number(term))
}
