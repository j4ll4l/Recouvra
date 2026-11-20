const euro = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' })

export function formatCents(cents: number): string {
  return euro.format(cents / 100)
}

export function formatDate(isoDate: string): string {
  const [year, month, day] = isoDate.split('-')
  return `${day}/${month}/${year}`
}
