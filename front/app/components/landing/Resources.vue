<script setup lang="ts">
const resources = [
  { kind: 'Guide et modèle', title: 'Mise en demeure de payer pour le BTP', text: 'Quand l’envoyer, ce qu’elle doit contenir, et un modèle à copier.', to: '/guides/mise-en-demeure-facture-impayee' },
  { kind: 'Outil gratuit', title: 'Calcul des pénalités de retard', text: 'Pénalités, indemnité de 40 € et total, au taux du semestre.', to: '/outils/calcul-penalites-retard' },
  { kind: 'Repères', title: 'Ce que dit la loi sur les délais de paiement', text: '60 jours, 40 €, BCE + 10 points : l’essentiel en une minute.', to: '#loi' },
]
</script>

<template>
  <section
    id="ressources"
    class="section section--paper"
    aria-labelledby="ress-titre"
  >
    <div class="container">
      <LandingSectionHead
        eyebrow="Ressources"
        title="Guides gratuits pour se faire payer."
        title-id="ress-titre"
      />
      <ul class="resources">
        <li
          v-for="resource in resources"
          :key="resource.title"
        >
          <NuxtLink
            :to="resource.to"
            class="resource"
          >
            <span class="resource__kind">{{ resource.kind }}</span>
            <span class="resource__title">{{ resource.title }}</span>
            <span class="resource__text">{{ resource.text }}</span>
          </NuxtLink>
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
.resources {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--space-3);
  margin: 0;
  padding: 0;
  list-style: none;
}

.resource {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  height: 100%;
  padding: var(--space-6);
  border: 1px solid var(--color-line);
  border-radius: var(--radius-md);
  background: var(--color-white);
  text-decoration: none;
}

.resource:hover {
  border-color: var(--color-ink);
}

.resource__kind {
  font-size: var(--text-xs);
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-muted);
}

.resource__title {
  font-size: var(--text-lg);
  font-weight: 700;
}

.resource__text {
  color: var(--color-muted);
}
</style>
