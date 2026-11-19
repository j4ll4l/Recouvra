export type PaymentMethod = 'transfer' | 'check' | 'card' | 'cash'

export interface Payment extends JsonLdResource {
  id: number
  invoice: Iri
  amountCents: number
  paidAt: IsoDate
  method: PaymentMethod
}

export type PaymentInput = Omit<Payment, keyof JsonLdResource | 'id'>