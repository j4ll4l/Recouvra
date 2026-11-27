import { describe, expect, it } from 'vitest'
import { dueDateFor, endOfMonth } from '../../shared/utils/date'
import { ttcFromHt } from '../../shared/utils/vat'

describe('endOfMonth', () => {
  it('donne le dernier jour du mois', () => {
    expect(endOfMonth('2026-11-27')).toBe('2026-11-30')
  })

  it('gère février en année non bissextile', () => {
    expect(endOfMonth('2027-02-10')).toBe('2027-02-28')
  })
})

describe('dueDateFor', () => {
  it('30 jours après l’émission', () => {
    expect(dueDateFor('2026-11-27', '30')).toBe('2026-12-27')
  })

  it('45 jours fin de mois : +45 jours, puis fin du mois', () => {
    expect(dueDateFor('2026-11-27', '45-eom')).toBe('2027-01-31')
  })

  it('60 jours après l’émission', () => {
    expect(dueDateFor('2026-11-27', '60')).toBe('2027-01-26')
  })
})

describe('ttcFromHt', () => {
  it('applique 20 % de TVA', () => {
    expect(ttcFromHt(100_000, 20)).toBe(120_000)
  })

  it('arrondit au centime avec 5,5 %', () => {
    expect(ttcFromHt(123_45, 5.5)).toBe(130_24)
  })

  it('garde le HT en autoliquidation (0 %)', () => {
    expect(ttcFromHt(100_000, 0)).toBe(100_000)
  })
})
