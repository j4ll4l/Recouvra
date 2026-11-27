import { describe, expect, it } from 'vitest'
import { idFromIri, paidPercent } from '../../shared/utils/payment'

describe('idFromIri', () => {
  it('extrait l’identifiant numérique d’une IRI', () => {
    expect(idFromIri('/api/clients/12')).toBe(12)
  })
})

describe('paidPercent', () => {
  it('calcule la part payée', () => {
    expect(paidPercent(150_000, 600_000)).toBe(25)
  })

  it('ne dépasse jamais 100 %', () => {
    expect(paidPercent(700_000, 600_000)).toBe(100)
  })

  it('renvoie 0 pour une facture à 0 €', () => {
    expect(paidPercent(0, 0)).toBe(0)
  })
})
