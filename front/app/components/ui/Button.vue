<script setup lang="ts">
import type { RouteLocationRaw } from 'vue-router'

const props = withDefaults(defineProps<{
  variant?: 'primary' | 'dark' | 'outline' | 'danger'
  size?: 'md' | 'lg'
  /** Si `to` est fourni, le bouton devient un lien (NuxtLink) */
  to?: RouteLocationRaw
  type?: 'button' | 'submit' | 'reset'
  loading?: boolean
  disabled?: boolean
  block?: boolean
}>(), {
  variant: 'primary',
  size: 'md',
  to: undefined,
  type: 'button',
  loading: false,
  disabled: false,
  block: false,
})

const classes = computed(() => [
  'btn',
  `btn--${props.variant}`,
  `btn--${props.size}`,
  { 'btn--block': props.block },
])
</script>

<template>
  <NuxtLink
    v-if="to"
    :to="to"
    :class="classes"
  >
    <slot />
  </NuxtLink>
  <button
    v-else
    :type="type"
    :class="classes"
    :disabled="disabled || loading"
    :aria-busy="loading || undefined"
  >
    <span
      v-if="loading"
      class="btn__spinner"
      aria-hidden="true"
    />
    <slot />
  </button>
</template>

<style scoped>
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  min-height: var(--tap-min);
  padding: 0 var(--space-4);
  border: 1px solid transparent;
  border-radius: var(--radius-sm);
  font-size: var(--text-sm);
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
  transition: background-color 150ms, border-color 150ms;
}

.btn--lg {
  min-height: 52px;
  padding: 0 var(--space-6);
  font-size: var(--text-lg);
}

.btn--block {
  width: 100%;
}

.btn--primary {
  background: var(--color-yellow);
  color: var(--color-ink);
}

.btn--primary:hover {
  background: var(--color-yellow-hover);
}

.btn--dark {
  background: var(--color-ink);
  color: var(--color-white);
}

.btn--dark:hover {
  background: var(--color-ink-2);
}

.btn--outline {
  background: var(--color-white);
  border-color: var(--color-line-strong);
  color: var(--color-ink);
}

.btn--outline:hover {
  border-color: var(--color-ink);
}

.btn--danger {
  background: var(--color-white);
  border-color: var(--color-danger);
  color: var(--color-danger);
}

.btn--danger:hover {
  background: var(--color-danger-bg);
}

.btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.btn__spinner {
  width: 16px;
  height: 16px;
  border: 2px solid currentcolor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spin 700ms linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
