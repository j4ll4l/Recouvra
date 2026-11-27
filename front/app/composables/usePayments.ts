import type { Iri, Paginated, Payment, PaymentInput } from '#shared/types'

export function usePayments() {
  const api = useApi()

  return {
    /** Tous les paiements d'une facture (100 au maximum, largement assez pour une facture) */
    listForInvoice: (invoice: Iri) =>
      api<Paginated<Payment>>('/payments', { query: { invoice, itemsPerPage: 100 } }),

    create: (input: PaymentInput) =>
      api<Payment>('/payments', { method: 'POST', body: input }),
  }
}
