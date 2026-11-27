<script setup lang="ts">
import type { ClientInput } from '#shared/types'
import { violationsByField } from '#shared/utils/violations'

definePageMeta({ layout: 'dashboard' })

const route = useRoute()
const clients = useClients()
const id = Number(route.params.id)

const { data: client, error: loadError } = await useAsyncData(`client-${id}`, () => clients.get(id))
useSeoMeta({ title: () => `Modifier ${client.value?.name ?? 'le client'} | Recouvra`, robots: 'noindex' })

const errors = ref<Record<string, string>>({})
const globalError = ref<string | null>(null)
const pending = ref(false)

async function save(input: ClientInput) {
  errors.value = {}
  globalError.value = null
  pending.value = true
  try {
    // PATCH : un champ facultatif vidé est envoyé à null pour être effacé
    await clients.update(id, {
      ...input,
      phone: input.phone ?? null,
      siret: input.siret ?? null,
      address: input.address ?? null,
    })
    await navigateTo(`/dashboard/clients/${id}`)
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
      :to="`/dashboard/clients/${id}`"
      class="page__back"
    >← Fiche client</NuxtLink>

    <UiEmptyState
      v-if="loadError || !client"
      title="Client introuvable"
      text="Il a peut-être été supprimé, ou il n’appartient pas à votre entreprise."
    >
      <UiButton to="/dashboard/clients">
        Retour aux clients
      </UiButton>
    </UiEmptyState>

    <template v-else>
      <h1 class="page__title">
        Modifier {{ client.name }}
      </h1>
      <ClientForm
        :initial="client"
        submit-label="Enregistrer"
        :cancel-to="`/dashboard/clients/${id}`"
        :pending="pending"
        :errors="errors"
        :global-error="globalError"
        @submit="save"
      />
    </template>
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
