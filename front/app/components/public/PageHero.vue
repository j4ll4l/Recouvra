<script setup lang="ts">
import type { BreadcrumbItem } from '~/composables/usePublicPage'

defineProps<{
  eyebrow: string
  title: string
  lead: string
  breadcrumb: BreadcrumbItem[]
}>()
</script>

<template>
  <section
    class="page-hero theme-dark"
    aria-labelledby="h1"
  >
    <div class="container page-hero__inner">
      <nav aria-label="Fil d’Ariane">
        <ol class="crumbs">
          <li
            v-for="item in breadcrumb"
            :key="item.label"
          >
            <NuxtLink
              v-if="item.to"
              :to="item.to"
            >
              {{ item.label }}
            </NuxtLink>
            <span
              v-else
              aria-current="page"
            >{{ item.label }}</span>
          </li>
        </ol>
      </nav>
      <span class="eyebrow">{{ eyebrow }}</span>
      <h1
        id="h1"
        class="page-hero__title"
      >
        {{ title }}
      </h1>
      <p class="page-hero__lead">
        {{ lead }}
      </p>
      <!-- Contenu sous le texte : outil, prix… -->
      <slot />
    </div>
  </section>
</template>

<style scoped>
.page-hero {
  padding-block: var(--space-8) var(--space-16);
}

.page-hero__inner {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
}

.crumbs {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
  margin: 0 0 var(--space-3);
  padding: 0;
  list-style: none;
  font-size: var(--text-sm);
  color: var(--color-muted-on-dark);
}

.crumbs li:not(:last-child)::after {
  content: '/';
  margin-left: var(--space-2);
  color: var(--color-ink-line);
}

.crumbs a {
  text-decoration: none;
}

.crumbs a:hover {
  color: var(--color-yellow);
}

.crumbs [aria-current] {
  color: var(--color-white);
}

.page-hero__title {
  max-width: 860px;
  font-size: var(--text-3xl);
  letter-spacing: -0.035em;
}

.page-hero__lead {
  max-width: 720px;
  font-size: var(--text-lg);
  color: var(--color-text-on-dark);
}
</style>
