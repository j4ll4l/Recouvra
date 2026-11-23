const euro = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' })

export function formatCents(cents: number): string {
  return euro.format(cents / 100)
}

export function formatDate(isoDate: string): string {
  const [year, month, day] = isoDate.split('-')
  return `${day}/${month}/${year}`
}

export function parseEurosToCents(value: string): number | null {
  const normalized = value.replace(/\s/g, '').replace(',', '.')
  const match = /^(\d+)(?:\.(\d{1,2}))?$/.exec(normalized)
  if (!match) {
    return null
  }

  const euros = Number(match[1])
  const cents = Number((match[2] ?? '').padEnd(2, '0'))
  return euros * 100 + cents
}
