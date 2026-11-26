<script setup lang="ts">
// Icônes en trait (style des maquettes). Décoratives : le texte à côté donne le sens.
const paths = {
  home: 'M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10',
  file: 'M14 3H6v18h12V7zM14 3v4h4M9 13h6M9 17h6',
  users: 'M16 20v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9.5 10a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M21 20v-2a4 4 0 0 0-3-3.9M15.5 3.1a3.5 3.5 0 0 1 0 6.8',
  plus: 'M12 5v14M5 12h14',
  logout: 'M15 4h4v16h-4M10 17l-5-5 5-5M5 12h11',
} as const

withDefaults(defineProps<{
  name: keyof typeof paths
  size?: number
}>(), { size: 20 })
</script>

<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    focusable="false"
  >
    <path :d="paths[name]" />
  </svg>
</template>
