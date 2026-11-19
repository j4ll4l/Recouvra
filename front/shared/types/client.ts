import type { JsonLdResource } from './api';export interface Client extends JsonLdResource {
    id: number
    name: string
    email: string
    phone: string | null
    siret: string | null
    address: string | null
  }

  /** Corps d'un POST / PATCH : uniquement le groupe client:write */
  export type ClientInput = Pick<Client, 'name' | 'email' | 'phone' | 'siret' | 'address'>