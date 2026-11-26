<script setup lang="ts">
const trades = [
  { name: 'Plombiers', to: '/metiers/plombier', image: 'plombier', alt: 'Mains d’un plombier qui coupe un tube au coupe-tube' },
  { name: 'Électriciens', image: 'electricien', alt: 'Électricien qui teste un tableau électrique' },
  { name: 'Maçons', image: 'macon', alt: 'Maçon qui monte un mur de parpaings à la truelle' },
  { name: 'Menuisiers', image: 'menuisier', alt: 'Mains d’un menuisier qui trace au crayon sur une planche' },
]
const others = ['Couvreurs', 'Peintres', 'Plaquistes', 'Carreleurs', 'Chauffagistes', 'Charpentiers', 'Paysagistes', 'Entreprises générales']
</script>

<template>
  <section
    id="metiers"
    class="section"
    aria-labelledby="metiers-titre"
  >
    <div class="container">
      <LandingSectionHead
        eyebrow="Par métier"
        title="Un scénario adapté à votre métier."
        title-id="metiers-titre"
      >
        Acomptes, situations de travaux, retenue de garantie : chaque métier a ses habitudes de paiement.
      </LandingSectionHead>
      <ul class="trades">
        <li
          v-for="trade in trades"
          :key="trade.name"
        >
          <component
            :is="trade.to ? resolveComponent('NuxtLink') : 'div'"
            :to="trade.to"
            class="trade"
            :class="{ 'trade--link': trade.to }"
          >
            <img
              :src="`/images/${trade.image}-800.webp`"
              :srcset="`/images/${trade.image}-400.webp 400w, /images/${trade.image}-800.webp 800w`"
              sizes="(min-width: 1024px) 300px, (min-width: 600px) 50vw, 100vw"
              width="800"
              height="600"
              :alt="trade.alt"
              loading="lazy"
              class="trade__img"
            >
            <span class="trade__name">{{ trade.name }}<span
              v-if="trade.to"
              aria-hidden="true"
            > →</span></span>
          </component>
        </li>
      </ul>
      <ul class="others">
        <li
          v-for="name in others"
          :key="name"
        >
          {{ name }}
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
/* La section précédente (calculateur) est aussi blanche : pas de double espace */
.section {
  padding-top: 0;
}

.trades {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: var(--space-3);
  margin: 0;
  padding: 0;
  list-style: none;
}

.trade {
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
  border: 1px solid var(--color-line);
  border-radius: var(--radius-md);
  text-decoration: none;
}

.trade--link:hover {
  border-color: var(--color-ink);
}

.trade__img {
  width: 100%;
  aspect-ratio: 4 / 3;
  object-fit: cover;
}

.trade__name {
  padding: var(--space-4);
  font-size: var(--text-lg);
  font-weight: 700;
}

.others {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
  margin: var(--space-6) 0 0;
  padding: 0;
  list-style: none;
}

.others li {
  padding: var(--space-2) var(--space-4);
  border: 1px solid var(--color-line-strong);
  border-radius: var(--radius-sm);
  font-size: var(--text-sm);
  font-weight: 600;
}
</style>
