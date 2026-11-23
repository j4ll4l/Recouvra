<script setup lang="ts">
import type { InvoiceInput } from '#shared/types'
import { addDays, todayIso } from '#shared/utils/date'
import { parseEurosToCents } from '#shared/utils/format'
import { violationsByField } from '#shared/utils/violations'

definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Nouvelle facture | Recouvra', robots: 'noindex' })

const invoices = useInvoices()
const clients = useClients()

// Liste des clients
const { data: clientOptions } = await useAsyncData('client-options', () => clients.list({ page: 1 }))

const today = todayIso()
const form = reactive({
  client: '',
  number: '',
  amountHt: '',
  amountTtc: '',
  issuedAt: today,
  dueAt: addDays(today, 30),
})
const errors = ref<Record<string, string>>({})
const globalError = ref<string | null>(null)
const pending = ref(false)

// Tant que l'utilisateur n'a pas touché à l'échéance, elle suit la date d'émission (+30 jours)
const dueAtTouched = ref(false)
watch(() => form.issuedAt, (issuedAt) => {
  if (!dueAtTouched.value && issuedAt) {
    form.dueAt = addDays(issuedAt, 30)
  }
})

async function onSubmit() {
  errors.value = {}
  globalError.value = null

  // Vérification côté front le format du montant, l'API ne peut pas le recevoir tel quel
  const amountHtCents = parseEurosToCents(form.amountHt)
  const amountTtcCents = parseEurosToCents(form.amountTtc)
  if (amountHtCents === null) errors.value.amountHtCents = 'Montant invalide (ex. 1 234,56).'
  if (amountTtcCents === null) errors.value.amountTtcCents = 'Montant invalide (ex. 1 234,56).'
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
    await invoices.create(input)
    await navigateTo('/dashboard/factures')
  }
  catch (error) {
    const fieldErrors = violationsByField(error)
    if (fieldErrors) {
      errors.value = fieldErrors
    }
    else {
      globalError.value = 'Enregistrement impossible. Réessayez dans un instant.'
    }
  }
  finally {
    pending.value = false
  }
}
</script>

<template>
  <section>
    <h1>Nouvelle facture</h1>

    <form
      novalidate
      @submit.prevent="onSubmit"
    >
      <div>
        <label for="client">Client</label>
        <select
          id="client"
          v-model="form.client"
          :aria-invalid="!!errors.client"
          aria-describedby="client-error"
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
        <p
          v-if="errors.client"
          id="client-error"
        >
          {{ errors.client }}
        </p>
        <NuxtLink to="/dashboard/clients/nouveau">Nouveau client</NuxtLink>
      </div>

      <div>
        <label for="number">Numéro de facture</label>
        <input
          id="number"
          v-model="form.number"
          type="text"
          placeholder="F-2026-0008"
          :aria-invalid="!!errors.number"
          aria-describedby="number-error"
        >
        <p
          v-if="errors.number"
          id="number-error"
        >
          {{ errors.number }}
        </p>
      </div>

      <div>
        <label for="amountHt">Montant HT (€)</label>
        <input
          id="amountHt"
          v-model="form.amountHt"
          type="text"
          inputmode="decimal"
          placeholder="1 000,00"
          :aria-invalid="!!errors.amountHtCents"
          aria-describedby="amountHt-error"
        >
        <p
          v-if="errors.amountHtCents"
          id="amountHt-error"
        >
          {{ errors.amountHtCents }}
        </p>
      </div>

      <div>
        <label for="amountTtc">Montant TTC (€)</label>
        <input
          id="amountTtc"
          v-model="form.amountTtc"
          type="text"
          inputmode="decimal"
          placeholder="1 200,00"
          :aria-invalid="!!errors.amountTtcCents"
          aria-describedby="amountTtc-error"
        >
        <p
          v-if="errors.amountTtcCents"
          id="amountTtc-error"
        >
          {{ errors.amountTtcCents }}
        </p>
      </div>

      <div>
        <label for="issuedAt">Date d'émission</label>
        <input
          id="issuedAt"
          v-model="form.issuedAt"
          type="date"
          :aria-invalid="!!errors.issuedAt"
          aria-describedby="issuedAt-error"
        >
        <p
          v-if="errors.issuedAt"
          id="issuedAt-error"
        >
          {{ errors.issuedAt }}
        </p>
      </div>

      <div>
        <label for="dueAt">Date d'échéance</label>
        <input
          id="dueAt"
          v-model="form.dueAt"
          type="date"
          :aria-invalid="!!errors.dueAt"
          aria-describedby="dueAt-error"
          @input="dueAtTouched = true"
        >
        <p
          v-if="errors.dueAt"
          id="dueAt-error"
        >
          {{ errors.dueAt }}
        </p>
      </div>

      <p
        v-if="globalError"
        role="alert"
      >
        {{ globalError }}
      </p>

      <button
        type="submit"
        :disabled="pending"
      >
        {{ pending ? 'Enregistrement…' : 'Créer la facture' }}
      </button>
      <NuxtLink to="/dashboard/factures">Annuler</NuxtLink>
    </form>
  </section>
</template>
