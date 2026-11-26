<script setup lang="ts">
defineProps<{
  title: string
  text?: string
}>()
</script>

<template>
  <div class="empty">
    <p class="empty__title">
      {{ title }}
    </p>
    <p
      v-if="text"
      class="empty__text"
    >
      {{ text }}
    </p>
    <!-- Action principale, ex. <UiButton to="…">Ajouter une facture</UiButton> -->
    <slot />
  </div>
</template>

<style scoped>
.empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-12) var(--space-4);
  text-align: center;
  border: 1px dashed var(--color-line-strong);
  border-radius: var(--radius-md);
}

.empty__title {
  font-size: var(--text-lg);
  font-weight: 700;
}

.empty__text {
  max-width: 420px;
  color: var(--color-muted);
}
</style>
