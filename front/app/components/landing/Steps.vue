<script setup lang="ts">
const steps = [
  { title: 'Déposez vos factures', text: 'Glissez vos PDF : Recouvra lit le client, le montant TTC et l’échéance.' },
  { title: 'Choisissez le scénario', text: 'J+3 courtois, J+15 ferme, J+30 formel, J+45 mise en demeure. Tout se modifie.' },
  { title: 'Encaissez', text: 'Dès qu’un paiement est saisi, les relances s’arrêtent pour cette facture.' },
]
</script>

<template>
  <section
    id="comment"
    class="section"
    aria-labelledby="comment-titre"
  >
    <div class="container">
      <LandingSectionHead
        eyebrow="Comment ça marche"
        title="Trois étapes, aucune relance oubliée."
        title-id="comment-titre"
      >
        Pas de formation, pas de paramétrage compliqué : on part de vos factures.
      </LandingSectionHead>
      <ol class="steps">
        <li
          v-for="(step, index) in steps"
          :key="step.title"
          class="steps__item"
        >
          <span
            class="steps__num"
            aria-hidden="true"
          >0{{ index + 1 }}</span>
          <h3 class="steps__title">
            {{ step.title }}
          </h3>
          <p>{{ step.text }}</p>
        </li>
      </ol>
    </div>
  </section>
</template>

<style scoped>
.steps {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: var(--space-1);
  margin: 0;
  padding: 0;
  list-style: none;
}

.steps__item {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  padding: var(--space-8);
  background: var(--color-paper);
  border-top: 3px solid var(--color-ink);
}

.steps__num {
  font-size: 3rem;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -0.04em;
  color: var(--color-yellow);
  -webkit-text-stroke: 1px var(--color-ink);
}

.steps__title {
  font-size: var(--text-xl);
}

.steps__item p {
  color: var(--color-muted);
}
</style>
