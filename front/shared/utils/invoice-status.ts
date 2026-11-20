import type { InvoiceStatus } from '../types'

/** Record<InvoiceStatus, …> : si un statut est ajouté au type, TypeScript exige son libellé ici */
export const INVOICE_STATUS_LABELS: Record<InvoiceStatus, string> = {
  draft: 'Brouillon',
  issued: 'Émise',
  overdue: 'Échue',
  reminded: 'Relancée',
  paid: 'Payée',
  disputed: 'Litige',
  written_off: 'Irrécouvrable',
}

export function isInvoiceStatus(value: unknown): value is InvoiceStatus {
  return typeof value === 'string' && value in INVOICE_STATUS_LABELS
}
