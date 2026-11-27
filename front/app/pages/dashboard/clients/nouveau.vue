<script setup lang="ts">
import type { ClientInput } from '#shared/types'
import { violationsByField } from '#shared/utils/violations'

definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Nouveau client | Recouvra', robots: 'noindex' })

const clients = useClients()

const errors = ref<Record<string, string>>({})
const globalError = ref<string | null>(null)
const pending = ref(false)

async function create(input: ClientInput) {
  errors.value = {}
  globalError.value = null
  pending.value = true
  try {
    const created = await clients.create(input)
    await navigateTo(`/dashboard/clients/${created.id}`)
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
  <div class="page">
    <NuxtLink
      to="/dashboard/clients"
      class="page__back"
    >← Clients</NuxtLink>
    <h1 class="page__title">
      Nouveau client
    </h1>
    <ClientForm
      submit-label="Créer le client"
      cancel-to="/dashboard/clients"
      :pending="pending"
      :errors="errors"
      :global-error="globalError"
      @submit="create"
    />
  </div>
</template>

<style scoped>
.page {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  max-width: 900px;
}

.page__back {
  align-self: flex-start;
  font-size: var(--text-sm);
  font-weight: 600;
  text-decoration: none;
}

.page__title {
  font-size: var(--text-2xl);
}
</style>
