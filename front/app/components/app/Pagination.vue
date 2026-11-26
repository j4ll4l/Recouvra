<script setup lang="ts">
defineProps<{ page: number, totalPages: number }>()

const route = useRoute()
</script>

<template>
  <nav
    v-if="totalPages > 1"
    aria-label="Pagination"
    class="pagination"
  >
    <NuxtLink
      v-if="page > 1"
      :to="{ query: { ...route.query, page: page - 1 } }"
      class="pagination__link"
    >
      ← Précédent
    </NuxtLink>
    <p class="pagination__info">
      Page {{ page }} sur {{ totalPages }}
    </p>
    <NuxtLink
      v-if="page < totalPages"
      :to="{ query: { ...route.query, page: page + 1 } }"
      class="pagination__link"
    >
      Suivant →
    </NuxtLink>
  </nav>
</template>

<style scoped>
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-3);
  padding: var(--space-4) 0;
}

.pagination__link {
  display: inline-flex;
  align-items: center;
  min-height: var(--tap-min);
  padding: 0 var(--space-4);
  border: 1px solid var(--color-line-strong);
  border-radius: var(--radius-sm);
  font-size: var(--text-sm);
  font-weight: 600;
  text-decoration: none;
}

.pagination__link:hover {
  border-color: var(--color-ink);
}

.pagination__info {
  font-size: var(--text-sm);
  color: var(--color-muted);
}
</style>
