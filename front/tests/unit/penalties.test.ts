import { describe, expect, it } from 'vitest'
import { computeLatePenalties, daysBetween, PENALTY_RATES } from '../../shared/utils/penalties'

describe('daysBetween', () => {
  it('compte les jours entre deux dates', () => {
    expect(daysBetween('2026-10-13', '2026-11-24')).toBe(42)
  })

  it('est négatif si la seconde date est avant la première', () => {
    expect(daysBetween('2026-11-24', '2026-11-20')).toBe(-4)
  })

  it('ignore le passage à l’heure d’hiver', () => {
    expect(daysBetween('2026-10-24', '2026-10-26')).toBe(2)
  })
})

describe('computeLatePenalties — client professionnel', () => {
  it('calcule l’exemple de la maquette : 7 200 €, 42 jours au taux par défaut', () => {
    const result = computeLatePenalties({
      amountCents: 720_000,
      dueAt: '2026-10-13',
      paidAt: '2026-11-24',
      clientType: 'professional',
    })

    expect(result).toEqual({
      days: 42,
      rate: 12.4,
      penaltyCents: 10_273,
      indemnityCents: 4_000,
      totalCents: 734_273,
      belowMinimum: false,
    })
  })

  it('applique le taux du contrat s’il est au-dessus du minimum', () => {
    const result = computeLatePenalties({
      amountCents: 720_000,
      dueAt: '2026-10-13',
      paidAt: '2026-11-24',
      clientType: 'professional',
      contractRate: 15,
    })

    expect(result.rate).toBe(15)
    expect(result.penaltyCents).toBe(12_427)
  })

  it('remplace un taux du contrat trop bas par le minimum légal', () => {
    const result = computeLatePenalties({
      amountCents: 720_000,
      dueAt: '2026-10-13',
      paidAt: '2026-11-24',
      clientType: 'professional',
      contractRate: 5,
    })

    expect(result.rate).toBe(PENALTY_RATES.minimumRate)
    expect(result.belowMinimum).toBe(true)
    expect(result.penaltyCents).toBe(6_835)
  })

  it('ne réclame rien si la facture est payée avant l’échéance', () => {
    const result = computeLatePenalties({
      amountCents: 720_000,
      dueAt: '2026-10-13',
      paidAt: '2026-10-01',
      clientType: 'professional',
    })

    expect(result.days).toBe(0)
    expect(result.penaltyCents).toBe(0)
    expect(result.indemnityCents).toBe(0)
    expect(result.totalCents).toBe(720_000)
  })
})

describe('computeLatePenalties — client particulier', () => {
  it('compte les intérêts à partir de la mise en demeure, sans indemnité de 40 €', () => {
    const result = computeLatePenalties({
      amountCents: 720_000,
      dueAt: '2026-10-13',
      paidAt: '2026-11-24',
      clientType: 'consumer',
      formalNoticeAt: '2026-11-10',
    })

    expect(result.days).toBe(14)
    expect(result.rate).toBe(2.75)
    expect(result.penaltyCents).toBe(759)
    expect(result.indemnityCents).toBe(0)
  })

  it('ne compte rien tant qu’aucune mise en demeure n’a été envoyée', () => {
    const result = computeLatePenalties({
      amountCents: 720_000,
      dueAt: '2026-10-13',
      paidAt: '2026-11-24',
      clientType: 'consumer',
    })

    expect(result.days).toBe(0)
    expect(result.totalCents).toBe(720_000)
  })
})
