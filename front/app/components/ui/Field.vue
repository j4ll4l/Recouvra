<script setup lang="ts">
// Le champ (input, select, textarea) est passé dans le slot.
// Le slot reçoit id, describedby et invalid pour relier label, aide et erreur au champ.
const props = defineProps<{
  label: string
  hint?: string
  error?: string
  required?: boolean
}>()

const id = useId()
const hintId = `${id}-hint`
const errorId = `${id}-error`

const describedby = computed(() => {
  const ids = [props.hint ? hintId : '', props.error ? errorId : ''].filter(Boolean)
  return ids.length ? ids.join(' ') : undefined
})
</script>

<template>
  <div class="field">
    <label
      :for="id"
      class="field__label"
    >
      {{ label }}<span
        v-if="required"
        aria-hidden="true"
      > *</span>
    </label>
    <slot
      :id="id"
      :describedby="describedby"
      :invalid="Boolean(error)"
    />
    <p
      v-if="hint"
      :id="hintId"
      class="field__hint"
    >
      {{ hint }}
    </p>
    <p
      v-if="error"
      :id="errorId"
      class="field__error"
    >
      {{ error }}
    </p>
  </div>
</template>

<style scoped>
.field {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  min-width: 0;
}

.field__label {
  font-size: var(--text-sm);
  font-weight: 600;
}

.field__hint {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

.field__error {
  font-size: var(--text-sm);
  color: var(--color-error);
}

:slotted(input),
:slotted(select),
:slotted(textarea) {
  width: 100%;
  min-height: var(--tap-min);
  padding: var(--space-2) var(--space-3);
  border: 1px solid var(--color-line-strong);
  border-radius: var(--radius-sm);
  background: var(--color-white);
  font-size: var(--text-base);
}

:slotted([aria-invalid='true']) {
  border-color: var(--color-error);
}
</style>
