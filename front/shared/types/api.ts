/** Une IRI API Platform, ex. "/api/clients/12" */
export type Iri = string

/** Champs ajoutés par API Platform sur chaque ressource (JSON-LD) */
export interface JsonLdResource {
  '@id': Iri
  '@type': string
}

/** Liste paginée renvoyée par un GetCollection (API Platform 5, sans préfixe hydra:) */
export interface Paginated<T> {
  member: T[]
  totalItems: number
  view?: {
    '@id': Iri
    first?: Iri
    last?: Iri
    previous?: Iri
    next?: Iri
  }
}

/** Erreur de validation (HTTP 422) */
export interface ApiViolationList {
  title: string
  detail: string
  violations: { propertyPath: string; message: string }[]
}

/** Date au format renvoyé par l'API : "2026-11-19" */
export type IsoDate = string