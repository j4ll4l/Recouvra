<script setup lang="ts">
import type { Client, ClientInput } from '#shared/types'
import { normalizeSiret } from '#shared/utils/siret'

// Formulaire partagé par « Nouveau client » et « Modifier le client ».
// Il ne parle pas à l'API, il émet les données, la page décide quoi en faire (POST ou PATCH).
const props = defineProps<{
  initial?: Client | null
  submitLabel: string
  cancelTo: string
  pending: boolean
  errors: Record<string, string>
  globalError: string | null
}>()

const emit = defineEmits<{ submit: [input: ClientInput] }>()

const form = reactive({
  name: props.initial?.name ?? '',
  email: props.initial?.email ?? '',
  phone: props.initial?.phone ?? '',
  siret: props.initial?.siret ?? '',
  address: props.initial?.address ?? '',
})

/** Un champ facultatif vide devient undefined */
const optional = (value: string) => value.trim() || undefined

function onSubmit() {
  emit('submit', {
    name: form.name.trim(),
    email: form.email.trim(),
    phone: optional(form.phone),
    siret: optional(normalizeSiret(form.siret)),
    address: optional(form.address),
  })
}
</script>

<template>
  <form
    class="client-form"
    novalidate
    @submit.prevent="onSubmit"
  >
    <UiCard>
      <div class="grid">
        <UiField
          v-slot="{ id, describedby, invalid }"
          label="Nom ou raison sociale"
          :error="errors.name"
          required
        >
          <input
            :id="id"
            v-model="form.name"
            type="text"
            autocomplete="organization"
            :aria-describedby="describedby"
            :aria-invalid="invalid"
          >
        </UiField>
        <UiField
          v-slot="{ id, describedby, invalid }"
          label="Email"
          hint="Les relances partent à cette adresse."
          :error="errors.email"
          required
        >
          <input
            :id="id"
            v-model="form.email"
            type="email"
            autocomplete="email"
            :aria-describedby="describedby"
            :aria-invalid="invalid"
          >
        </UiField>
        <UiField
          v-slot="{ id, describedby, invalid }"
          label="Téléphone (facultatif)"
          :error="errors.phone"
        >
          <input
            :id="id"
            v-model="form.phone"
            type="tel"
            autocomplete="tel"
            :aria-describedby="describedby"
            :aria-invalid="invalid"
          >
        </UiField>
        <UiField
          v-slot="{ id, describedby, invalid }"
          label="SIRET (facultatif)"
          hint="14 chiffres, les espaces sont acceptés."
          :error="errors.siret"
        >
          <input
            :id="id"
            v-model="form.siret"
            type="text"
            inputmode="numeric"
            placeholder="812 345 678 00019"
            :aria-describedby="describedby"
            :aria-invalid="invalid"
          >
        </UiField>
      </div>
      <UiField
        v-slot="{ id, describedby, invalid }"
        label="Adresse (facultatif)"
        :error="errors.address"
        class="client-form__address"
      >
        <textarea
          :id="id"
          v-model="form.address"
          rows="3"
          autocomplete="street-address"
          :aria-describedby="describedby"
          :aria-invalid="invalid"
        />
      </UiField>
    </UiCard>

    <UiAlert
      v-if="globalError"
      tone="error"
    >
      {{ globalError }}
    </UiAlert>

    <div class="client-form__actions">
      <UiButton
        :to="cancelTo"
        variant="outline"
      >
        Annuler
      </UiButton>
      <UiButton
        type="submit"
        :loading="pending"
      >
        {{ pending ? 'Enregistrement…' : submitLabel }}
      </UiButton>
    </div>
  </form>
</template>

<style scoped>
.client-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.grid {
  display: grid;
  gap: var(--space-4);
}

.client-form__address {
  margin-top: var(--space-4);
}

.client-form__actions {
  display: flex;
  flex-direction: column-reverse;
  gap: var(--space-3);
}

@media (min-width: 768px) {
  .grid {
    grid-template-columns: 1fr 1fr;
  }

  .client-form__actions {
    flex-direction: row;
    justify-content: flex-end;
  }
}
</style>
