<script setup lang="ts">
defineProps<{
  eyebrow: string
  title: string
  titleId: string
}>()
</script>

<template>
  <div class="head">
    <div class="head__titles">
      <span class="eyebrow">{{ eyebrow }}</span>
      <h2
        :id="titleId"
        class="h2"
      >
        {{ title }}
      </h2>
    </div>
    <!-- Texte d'accompagnement à droite du titre (facultatif) -->
    <div
      v-if="$slots.default"
      class="head__aside lead"
    >
      <slot />
    </div>
  </div>
</template>

<style scoped>
.head {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: flex-end;
  gap: var(--space-5);
  margin-bottom: var(--space-10);
}

.head__titles {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  max-width: 700px;
}

.head__aside {
  max-width: 420px;
}
</style>
