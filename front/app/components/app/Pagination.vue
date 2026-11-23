<script setup lang="ts">
defineProps<{ page: number, totalPages: number }>()

const route = useRoute()
</script>

<template>
  <nav
    v-if="totalPages > 1"
    aria-label="Pagination"
  >
    <NuxtLink
      v-if="page > 1"
      :to="{ query: { ...route.query, page: page - 1 } }"
    >
      Précédent
    </NuxtLink>
    <span aria-current="page">Page {{ page }} / {{ totalPages }}</span>
    <NuxtLink
      v-if="page < totalPages"
      :to="{ query: { ...route.query, page: page + 1 } }"
    >
      Suivant
    </NuxtLink>
  </nav>
</template>
