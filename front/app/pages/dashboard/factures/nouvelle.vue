<script setup lang="ts">
import type { InvoiceInput } from '#shared/types'
import type { PaymentTerm } from '#shared/utils/date'
import type { VatRate } from '#shared/utils/vat'
import { addDays, dueDateFor, PAYMENT_TERM_LABELS, todayIso } from '#shared/utils/date'
import { formatCents, parseEurosToCents } from '#shared/utils/format'
import { ttcFromHt, VAT_RATES } from '#shared/utils/vat'
import { violationsByField } from '#shared/utils/violations'

definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Nouvelle facture | Recouvra', robots: 'noindex' })

const invoices = useInvoices()
const clients = useClients()

const { data: clientOptions } = await useAsyncData('client-options', () => clients.list({ itemsPerPage: 100 }))

const today = todayIso()
const form = reactive({
  client: '',
  number: '',
  amountHt: '',
  vatRate: 20 as VatRate,
  amountTtc: '',
  issuedAt: today,
  term: '30' as PaymentTerm | 'custom',
  dueAt: addDays(today, 30),
})
const errors = ref<Record<string, string>>({})
const globalError = ref<string | null>(null)
const pending = ref(false)

// ---------- TTC calculé depuis le HT et la TVA, tant qu'on ne le modifie pas à la main ----------
const ttcTouched = ref(false)
watch(() => [form.amountHt, form.vatRate] as const, ([ht, rate]) => {
  if (ttcTouched.value) return
  const htCents = parseEurosToCents(ht)
  form.amountTtc = htCents === null ? '' : (ttcFromHt(htCents, rate) / 100).toFixed(2).replace('.', ',')
})

// ---------- Échéance proposée selon le délai choisi ----------
watch(() => [form.issuedAt, form.term] as const, ([issuedAt, term]) => {
  if (issuedAt && term !== 'custom') form.dueAt = dueDateFor(issuedAt, term)
})

// Au-delà de 60 jours après l'émission, on prévient (plafond légal, art. L441-10)
const beyondLegalLimit = computed(() => Boolean(form.issuedAt && form.dueAt && form.dueAt > addDays(form.issuedAt, 60)))

async function onSubmit() {
  errors.value = {}
  globalError.value = null

  const amountHtCents = parseEurosToCents(form.amountHt)
  const amountTtcCents = parseEurosToCents(form.amountTtc)
  if (amountHtCents === null) errors.value.amountHtCents = 'Montant invalide, par exemple 1 234,56.'
  if (amountTtcCents === null) errors.value.amountTtcCents = 'Montant invalide, par exemple 1 480,00.'
  if (amountHtCents === null || amountTtcCents === null) return

  const input: InvoiceInput = {
    client: form.client,
    number: form.number.trim(),
    amountHtCents,
    amountTtcCents,
    issuedAt: form.issuedAt,
    dueAt: form.dueAt,
  }

  pending.value = true
  try {
    const created = await invoices.create(input)
    await navigateTo(`/dashboard/factures/${created.id}`)
  }
  catch (error) {
    const fieldErrors = violationsByField(error)
    if (fieldErrors) errors.value = fieldErrors
    else globalError.value = 'Enregistrement impossible. Réessayez dans un instant.'
  }
  finally {
    pending.value = false
  }
}
</script>

<template>
  <div class="new">
    <NuxtLink
      to="/dashboard/factures"
      class="new__back"
    >← Factures</NuxtLink>
    <h1 class="new__title">
      Nouvelle facture
    </h1>

    <form
      class="new__form"
      novalidate
      @submit.prevent="onSubmit"
    >
      <UiCard aria-labelledby="client-titre">
        <h2
          id="client-titre"
          class="block__title"
        >
          Client et numéro
        </h2>
        <div class="grid">
          <UiField
            v-slot="{ id, describedby, invalid }"
            label="Client"
            :error="errors.client"
          >
            <select
              :id="id"
              v-model="form.client"
              :aria-describedby="describedby"
              :aria-invalid="invalid"
            >
              <option
                value=""
                disabled
              >
                Choisir un client
              </option>
              <option
                v-for="client in clientOptions?.member"
                :key="client.id"
                :value="client['@id']"
              >
                {{ client.name }}
              </option>
            </select>
          </UiField>
          <UiField
            v-slot="{ id, describedby, invalid }"
            label="Numéro de facture"
            :error="errors.number"
          >
            <input
              :id="id"
              v-model="form.number"
              type="text"
              placeholder="F-2026-0008"
              autocomplete="off"
              :aria-describedby="describedby"
              :aria-invalid="invalid"
            >
          </UiField>
        </div>
        <NuxtLink
          to="/dashboard/clients/nouveau"
          class="new__link"
        >+ Nouveau client</NuxtLink>
      </UiCard>

      <UiCard aria-labelledby="montants-titre">
        <h2
          id="montants-titre"
          class="block__title"
        >
          Montants
        </h2>
        <div class="grid grid--3">
          <UiField
            v-slot="{ id, describedby, invalid }"
            label="Montant HT (€)"
            :error="errors.amountHtCents"
          >
            <input
              :id="id"
              v-model="form.amountHt"
              type="text"
              inputmode="decimal"
              placeholder="1 000,00"
              :aria-describedby="describedby"
              :aria-invalid="invalid"
            >
          </UiField>
          <UiField
            v-slot="{ id }"
            label="TVA"
          >
            <select
              :id="id"
              v-model.number="form.vatRate"
            >
              <option
                v-for="rate in VAT_RATES"
                :key="rate"
                :value="rate"
              >
                {{ rate === 0 ? 'Autoliquidation (0 %)' : `${rate.toLocaleString('fr-FR')} %` }}
              </option>
            </select>
          </UiField>
          <UiField
            v-slot="{ id, describedby, invalid }"
            label="Montant TTC (€)"
            hint="Calculé, modifiable"
            :error="errors.amountTtcCents"
          >
            <input
              :id="id"
              v-model="form.amountTtc"
              type="text"
              inputmode="decimal"
              :aria-describedby="describedby"
              :aria-invalid="invalid"
              @input="ttcTouched = true"
            >
          </UiField>
        </div>
      </UiCard>

      <UiCard aria-labelledby="dates-titre">
        <h2
          id="dates-titre"
          class="block__title"
        >
          Dates
        </h2>
        <div class="grid grid--3">
          <UiField
            v-slot="{ id, describedby, invalid }"
            label="Date d’émission"
            :error="errors.issuedAt"
          >
            <input
              :id="id"
              v-model="form.issuedAt"
              type="date"
              :aria-describedby="describedby"
              :aria-invalid="invalid"
            >
          </UiField>
          <UiField
            v-slot="{ id }"
            label="Délai de paiement"
          >
            <select
              :id="id"
              v-model="form.term"
            >
              <option
                v-for="(label, value) in PAYMENT_TERM_LABELS"
                :key="value"
                :value="value"
              >
                {{ label }}
              </option>
              <option value="custom">
                Date précise
              </option>
            </select>
          </UiField>
          <UiField
            v-slot="{ id, describedby, invalid }"
            label="Date d’échéance"
            :error="errors.dueAt"
          >
            <input
              :id="id"
              v-model="form.dueAt"
              type="date"
              :aria-describedby="describedby"
              :aria-invalid="invalid"
              @input="form.term = 'custom'"
            >
          </UiField>
        </div>
        <UiAlert
          v-if="beyondLegalLimit"
          tone="warning"
          class="new__warning"
        >
          Échéance à plus de 60 jours après l’émission : c’est au-delà du délai maximal entre professionnels (art. L441-10).
        </UiAlert>
      </UiCard>

      <UiAlert
        v-if="globalError"
        tone="error"
      >
        {{ globalError }}
      </UiAlert>

      <div class="new__actions">
        <UiButton
          to="/dashboard/factures"
          variant="outline"
        >
          Annuler
        </UiButton>
        <UiButton
          type="submit"
          :loading="pending"
        >
          {{ pending ? 'Enregistrement…' : 'Créer la facture' }}
        </UiButton>
      </div>
      <p
        v-if="form.amountTtc && parseEurosToCents(form.amountTtc) !== null"
        class="new__summary"
      >
        Recouvra relancera le client si les {{ formatCents(parseEurosToCents(form.amountTtc) ?? 0) }} ne sont pas payés à l’échéance.
      </p>
    </form>
  </div>
</template>

<style scoped>
.new {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  max-width: 900px;
}

.new__back {
  align-self: flex-start;
  font-size: var(--text-sm);
  font-weight: 600;
  text-decoration: none;
}

.new__title {
  font-size: var(--text-2xl);
}

.new__form {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.block__title {
  margin-bottom: var(--space-4);
  font-size: var(--text-lg);
}

.grid {
  display: grid;
  gap: var(--space-4);
}

.new__link {
  display: inline-block;
  margin-top: var(--space-3);
  font-size: var(--text-sm);
  font-weight: 600;
}

.new__warning {
  margin-top: var(--space-4);
}

.new__actions {
  display: flex;
  flex-direction: column-reverse;
  gap: var(--space-3);
}

.new__summary {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

@media (min-width: 768px) {
  .grid {
    grid-template-columns: 1fr 1fr;
  }

  .grid--3 {
    grid-template-columns: repeat(3, 1fr);
  }

  .new__actions {
    flex-direction: row;
    justify-content: flex-end;
  }
}
</style>
