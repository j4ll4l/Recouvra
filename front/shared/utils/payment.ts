import type { Iri, PaymentMethod } from '../types'

/** Record<PaymentMethod, …> : si un moyen est ajouté au type, TypeScript exige son libellé */
export const PAYMENT_METHOD_LABELS: Record<PaymentMethod, string> = {
  transfer: 'Virement',
  check: 'Chèque',
  card: 'Carte bancaire',
  cash: 'Espèces',
}

/** "/api/clients/12" → 12 */
export function idFromIri(iri: Iri): number {
  return Number(iri.split('/').pop())
}

/** Part payée, en %, bornée entre 0 et 100 */
export function paidPercent(paidCents: number, totalCents: number): number {
  if (totalCents <= 0) return 0
  return Math.min(100, Math.max(0, Math.round((paidCents / totalCents) * 100)))
}
