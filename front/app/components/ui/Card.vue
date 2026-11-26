<script setup lang="ts">
withDefaults(defineProps<{
  as?: string
}>(), { as: 'section' })
</script>

<template>
  <component
    :is="as"
    class="card"
  >
    <slot />
  </component>
</template>

<style scoped>
.card {
  padding: var(--space-4);
  background: var(--color-white);
  border: 1px solid var(--color-line);
  border-radius: var(--radius-md);
}

@media (min-width: 768px) {
  .card {
    padding: var(--space-6);
  }
}
</style>
