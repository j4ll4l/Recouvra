<script setup lang="ts">
const items = [
  { title: 'Écrites à votre nom', text: 'Votre client répond directement dans votre boîte mail.' },
  { title: 'Conforme au Code de commerce', text: 'Pénalités et indemnité de 40 € selon L441-10 et D441-5.' },
  { title: 'Hébergé en France', text: 'Vos factures restent privées à votre entreprise.' },
  { title: 'Sans engagement', text: 'Vos données exportables en CSV à tout moment.' },
]
</script>

<template>
  <section
    class="trust theme-dark"
    aria-label="Garanties"
  >
    <ul class="container trust__list">
      <li
        v-for="item in items"
        :key="item.title"
      >
        <strong>{{ item.title }}</strong>
        <span>{{ item.text }}</span>
      </li>
    </ul>
  </section>
</template>

<style scoped>
.trust {
  background: var(--color-ink-band);
  border-block: 1px solid var(--color-ink-line);
}

.trust__list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: var(--space-6);
  margin-block: 0;
  padding-block: var(--space-8);
  list-style: none;
}

.trust__list li {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
  padding-left: var(--space-4);
  border-left: 3px solid var(--color-yellow);
}

.trust__list span {
  font-size: var(--text-sm);
  color: var(--color-muted-on-dark);
}
</style>
