<script setup lang="ts">
defineProps<{
  title: string
  text: string
  secondaryLabel: string
  secondaryTo: string
}>()
</script>

<template>
  <section
    class="cta section--yellow"
    aria-labelledby="cta-titre"
  >
    <div class="container cta__inner">
      <div class="cta__text">
        <h2
          id="cta-titre"
          class="cta__title"
        >
          {{ title }}
        </h2>
        <p>{{ text }}</p>
      </div>
      <div class="cta__actions">
        <UiButton
          to="/login"
          variant="dark"
          size="lg"
        >
          Essayer gratuitement
        </UiButton>
        <NuxtLink
          :to="secondaryTo"
          class="cta__link"
        >
          {{ secondaryLabel }}
        </NuxtLink>
      </div>
    </div>
  </section>
</template>

<style scoped>
.cta__inner {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: var(--space-6);
  padding-block: var(--space-12);
}

.cta__text {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  max-width: 720px;
  font-size: var(--text-lg);
}

.cta__title {
  font-size: var(--text-2xl);
  font-weight: 800;
  letter-spacing: -0.04em;
}

.cta__actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-5);
}

.cta__link {
  padding-bottom: 2px;
  border-bottom: 2px solid var(--color-ink);
  font-weight: 600;
  text-decoration: none;
}
</style>
