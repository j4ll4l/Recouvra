import type { Invoice, InvoiceInput, InvoiceStatus, Iri, Paginated } from '#shared/types'

export interface InvoiceFilters {
  page?: number
  status?: InvoiceStatus
  client?: Iri
  number?: string
}

export function useInvoices() {
  const api = useApi()

  return {
    list: (filters: InvoiceFilters = {}) =>
      api<Paginated<Invoice>>('/invoices', { query: filters }),

    get: (id: number) =>
      api<Invoice>(`/invoices/${id}`),

    create: (input: InvoiceInput) =>
      api<Invoice>('/invoices', { method: 'POST', body: input }),

    // API Platform demande merge-patch+json pour les PATCH
    update: (id: number, input: Partial<InvoiceInput>) =>
      api<Invoice>(`/invoices/${id}`, {
        method: 'PATCH',
        body: input,
        headers: { 'Content-Type': 'application/merge-patch+json' },
      }),

    remove: (id: number) =>
      api(`/invoices/${id}`, { method: 'DELETE' }),
  }
}