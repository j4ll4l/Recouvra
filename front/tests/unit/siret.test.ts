import { describe, expect, it } from 'vitest'
import { formatSiret, normalizeSiret } from '../../shared/utils/siret'

describe('normalizeSiret', () => {
  it('retire les espaces et les points', () => {
    expect(normalizeSiret('812 345 678 00019')).toBe('81234567800019')
    expect(normalizeSiret('812.345.678.00019')).toBe('81234567800019')
  })
})

describe('formatSiret', () => {
  it('groupe un SIRET valide en 3-3-3-5', () => {
    expect(formatSiret('81234567800019')).toBe('812 345 678 00019')
  })

  it('laisse tel quel un SIRET invalide', () => {
    expect(formatSiret('1234')).toBe('1234')
  })
})
