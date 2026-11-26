<script setup lang="ts">
const props = withDefaults(defineProps<{
  tone?: 'info' | 'success' | 'warning' | 'error'
}>(), { tone: 'info' })

// Une erreur est annoncée tout de suite par le lecteur d'écran (alert),
// les autres messages poliment, sans couper la lecture en cours (status).
const role = computed(() => (props.tone === 'error' ? 'alert' : 'status'))
</script>

<template>
  <div
    :role="role"
    :class="['alert', `alert--${tone}`]"
  >
    <slot />
  </div>
</template>

<style scoped>
.alert {
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-sm);
  font-size: var(--text-sm);
}

.alert--info {
  background: var(--status-issued-bg);
  color: var(--status-issued);
}

.alert--success {
  background: var(--color-success-bg);
  color: var(--color-success);
}

.alert--warning {
  background: var(--color-warning-bg);
  color: var(--color-warning);
}

.alert--error {
  background: var(--color-danger-bg);
  color: var(--color-danger);
}
</style>
