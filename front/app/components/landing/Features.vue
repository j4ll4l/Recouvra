<script setup lang="ts">
const features = [
  { title: 'Lecture des factures PDF', text: 'Déposez un PDF : client, montant TTC et échéance sont remplis pour vous.' },
  { title: 'Rédaction par l’IA', text: 'Des relances au bon ton, à votre nom, modifiables avant chaque envoi.' },
  { title: 'Tableau de bord des encours', text: 'Qui vous doit quoi, depuis quand, et ce qui doit rentrer ce mois-ci.' },
  { title: 'Utilisateurs et rôles', text: 'Gérant, secrétariat, comptable : chacun voit ce qui le concerne.' },
  { title: 'Mise en demeure prête', text: 'Le courrier officiel, avec les bons articles de loi, prêt pour le recommandé.' },
  { title: 'Exports CSV et PDF', text: 'Pour votre expert-comptable, en un clic.' },
]
</script>

<template>
  <section
    id="fonctionnalites"
    class="section theme-dark"
    aria-labelledby="fonc-titre"
  >
    <div class="container">
      <LandingSectionHead
        eyebrow="Fonctionnalités"
        title="Tout ce qu’il faut pour se faire payer. Rien de plus."
        title-id="fonc-titre"
      />
      <ul class="features">
        <li
          v-for="feature in features"
          :key="feature.title"
          class="features__item"
        >
          <h3 class="features__title">
            {{ feature.title }}
          </h3>
          <p>{{ feature.text }}</p>
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: var(--space-3);
  margin: 0;
  padding: 0;
  list-style: none;
}

.features__item {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  padding: var(--space-6);
  border: 1px solid var(--color-ink-line);
  border-top: 3px solid var(--color-yellow);
  border-radius: var(--radius-md);
  background: var(--color-ink-soft);
}

.features__title {
  font-size: var(--text-xl);
}

.features__item p {
  color: var(--color-muted-on-dark);
}
</style>
