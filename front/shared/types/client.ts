import type { JsonLdResource } from './api'

export interface Client extends JsonLdResource {
  id: number
  name: string
  email: string
  phone?: string
  siret?: string
  address?: string
}

/** Corps d'un POST / PATCH : uniquement le groupe client:write */
export type ClientInput = Pick<Client,
  'name' | 'email' | 'phone' | 'siret' | 'address'
>
