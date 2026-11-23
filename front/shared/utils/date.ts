import type { IsoDate } from '../types'

export function todayIso(): IsoDate {
  const now = new Date()
  const pad = (n: number) => String(n).padStart(2, '0')
  return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`
}

export function addDays(isoDate: IsoDate, days: number): IsoDate {
  const [year = 0, month = 1, day = 1] = isoDate.split('-').map(Number)
  return new Date(Date.UTC(year, month - 1, day + days)).toISOString().slice(0, 10)
}
