import type { Client, ClientInput, Paginated } from '#shared/types'

export interface ClientFilters {
  page?: number
  /** 100 au maximum (pagination_maximum_items_per_page) */
  itemsPerPage?: number
  name?: string
  email?: string
}

export function useClients() {
  const api = useApi()

  return {
    list: (filters: ClientFilters = {}) =>
      api<Paginated<Client>>('/clients', { query: filters }),

    get: (id: number) =>
      api<Client>(`/clients/${id}`),

    create: (input: ClientInput) =>
      api<Client>('/clients', { method: 'POST', body: input }),

    update: (id: number, input: Partial<ClientInput>) =>
      api<Client>(`/clients/${id}`, {
        method: 'PATCH',
        body: input,
        headers: { 'Content-Type': 'application/merge-patch+json' },
      }),

    remove: (id: number) =>
      api(`/clients/${id}`, { method: 'DELETE' }),
  }
}
