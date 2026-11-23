<script setup lang="ts">
import type { ClientInput } from '#shared/types'
import { violationsByField } from '#shared/utils/violations'

definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Nouveau client | Recouvra', robots: 'noindex' })

const clients = useClients()

const form = reactive({ name: '', email: '', phone: '', siret: '', address: '' })
const errors = ref<Record<string, string>>({})
const globalError = ref<string | null>(null)
const pending = ref(false)

/** Un champ optionnel vide n'est pas envoyé : l'API stocke null plutôt qu'une chaîne vide */
function optional(value: string): string | undefined {
  return value.trim() || undefined
}

async function onSubmit() {
  errors.value = {}
  globalError.value = null
  pending.value = true

  const input: ClientInput = {
    name: form.name.trim(),
    email: form.email.trim(),
    phone: optional(form.phone),
    siret: optional(form.siret),
    address: optional(form.address),
  }

  try {
    await clients.create(input)
    await navigateTo('/dashboard/clients')
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
    <h1>Nouveau client</h1>

    <form
      novalidate
      @submit.prevent="onSubmit"
    >
      <div>
        <label for="name">Nom ou raison sociale</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
          :aria-invalid="!!errors.name"
          aria-describedby="name-error"
        >
        <p
          v-if="errors.name"
          id="name-error"
        >
          {{ errors.name }}
        </p>
      </div>

      <div>
        <label for="email">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          :aria-invalid="!!errors.email"
          aria-describedby="email-error"
        >
        <p
          v-if="errors.email"
          id="email-error"
        >
          {{ errors.email }}
        </p>
      </div>

      <div>
        <label for="phone">Téléphone (facultatif)</label>
        <input
          id="phone"
          v-model="form.phone"
          type="tel"
          :aria-invalid="!!errors.phone"
          aria-describedby="phone-error"
        >
        <p
          v-if="errors.phone"
          id="phone-error"
        >
          {{ errors.phone }}
        </p>
      </div>

      <div>
        <label for="siret">SIRET (facultatif)</label>
        <input
          id="siret"
          v-model="form.siret"
          type="text"
          inputmode="numeric"
          :aria-invalid="!!errors.siret"
          aria-describedby="siret-error"
        >
        <p
          v-if="errors.siret"
          id="siret-error"
        >
          {{ errors.siret }}
        </p>
      </div>

      <div>
        <label for="address">Adresse (facultatif)</label>
        <input
          id="address"
          v-model="form.address"
          type="text"
          :aria-invalid="!!errors.address"
          aria-describedby="address-error"
        >
        <p
          v-if="errors.address"
          id="address-error"
        >
          {{ errors.address }}
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
        {{ pending ? 'Enregistrement…' : 'Créer le client' }}
      </button>
      <NuxtLink to="/dashboard/clients">Annuler</NuxtLink>
    </form>
  </section>
</template>
