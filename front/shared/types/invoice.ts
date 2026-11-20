import type { Iri, IsoDate, JsonLdResource } from './api'
import type { Client } from './client'

/** Statuts présents dans l'API (voir les fixtures) */
export type InvoiceStatus
  = | 'draft'
    | 'issued'
    | 'overdue'
    | 'reminded'
    | 'paid'
    | 'disputed'
    | 'written_off'

export interface Invoice extends JsonLdResource {
  id: number
  number: string
  /** Client imbriqué : seul `name` est dans le groupe invoice:read */
  client: JsonLdResource & Pick<Client, 'name'>
  amountHtCents: number
  amountTtcCents: number
  status: InvoiceStatus
  issuedAt: IsoDate
  dueAt: IsoDate
  /** Paiements renvoyés sous forme d'IRI */
  payments: Iri[]
}

/** Corps d'un POST / PATCH (groupe invoice:write). Le client est envoyé en IRI. */
export interface InvoiceInput {
  client: Iri
  number: string
  amountHtCents: number
  amountTtcCents: number
  issuedAt: IsoDate
  dueAt: IsoDate
}
