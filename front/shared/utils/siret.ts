/** Retire espaces et points : « 812 345 678 00019 » → « 81234567800019 » */
export function normalizeSiret(value: string): string {
  return value.replace(/[\s.]/g, '')
}

/** Affiche un SIRET par groupes : 812 345 678 00019 */
export function formatSiret(siret: string): string {
  return /^\d{14}$/.test(siret) ? siret.replace(/^(\d{3})(\d{3})(\d{3})(\d{5})$/, '$1 $2 $3 $4') : siret
}
