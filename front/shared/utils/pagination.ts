/** La valeur doit correspondre à pagination_items_per_page côté API Platform */
export const ITEMS_PER_PAGE = 20

export function countPages(totalItems: number): number {
  return Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE))
}
