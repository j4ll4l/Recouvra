/** Taux de TVA courants dans le BTP, en % (0 : autoliquidation en sous-traitance) */
export const VAT_RATES = [20, 10, 5.5, 0] as const
export type VatRate = typeof VAT_RATES[number]

/** TTC arrondi au centime : HT × (1 + taux) */
export function ttcFromHt(htCents: number, rate: number): number {
  return Math.round(htCents * (100 + rate) / 100)
}
