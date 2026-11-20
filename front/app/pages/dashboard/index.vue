<script setup lang="ts">
definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Tableau de bord | Recouvra', robots: 'noindex' })

const { user } = useUserSession()
</script>

<template>
  <h1>Bonjour {{ user?.email }}</h1>
</template>
