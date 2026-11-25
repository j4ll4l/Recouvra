<script setup lang="ts">
// Le symbole (un niveau à bulle) est décoratif : le nom « recouvra » est écrit à côté.
// Sans texte (withText: false), le parent doit donner un nom accessible, par ex. aria-label sur le lien.
withDefaults(defineProps<{
  withText?: boolean
  height?: number
}>(), {
  withText: true,
  height: 28,
})
</script>

<template>
  <span class="logo">
    <svg
      :width="Math.round(height * 1.9)"
      :height="height"
      viewBox="0 0 76 40"
      aria-hidden="true"
      focusable="false"
    >
      <rect
        class="logo__yellow"
        width="76"
        height="26"
        rx="13"
      />
      <rect
        class="logo__hole"
        x="26"
        y="5"
        width="3"
        height="16"
      />
      <rect
        class="logo__hole"
        x="47"
        y="5"
        width="3"
        height="16"
      />
      <circle
        class="logo__hole"
        cx="38"
        cy="13"
        r="6"
      />
      <g class="logo__yellow">
        <rect
          x="4"
          y="30"
          width="3"
          height="10"
        />
        <rect
          x="16"
          y="30"
          width="3"
          height="6"
        />
        <rect
          x="28"
          y="30"
          width="3"
          height="6"
        />
        <rect
          x="40"
          y="30"
          width="3"
          height="10"
        />
        <rect
          x="52"
          y="30"
          width="3"
          height="6"
        />
        <rect
          x="64"
          y="30"
          width="3"
          height="6"
        />
      </g>
    </svg>
    <span
      v-if="withText"
      class="logo__text"
    >recouvra</span>
  </span>
</template>

<style scoped>
.logo {
  display: inline-flex;
  align-items: center;
  gap: var(--space-3);
}

.logo__yellow {
  fill: var(--color-yellow);
}

/* Les « trous » du niveau prennent la couleur du fond : graphite par défaut */
.logo__hole {
  fill: var(--logo-hole, var(--color-ink));
}

.logo__text {
  font-size: var(--text-xl);
  font-weight: 700;
  letter-spacing: -0.03em;
}
</style>
