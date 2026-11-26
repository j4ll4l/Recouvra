<script setup lang="ts">
const rules = [
  { figure: '60 jours', text: 'Délai de paiement maximal après la date de facture, ou 45 jours fin de mois.', source: 'Code de commerce, art. L441-10' },
  { figure: '40 €', text: 'Indemnité forfaitaire pour frais de recouvrement, due pour chaque facture en retard.', source: 'Code de commerce, art. D441-5' },
  { figure: 'BCE + 10', text: 'Points de taux de pénalité si vos conditions générales ne prévoient rien.', source: 'Code de commerce, art. L441-10' },
  { figure: '3 ×', text: 'Le taux d’intérêt légal : le minimum que vos conditions peuvent prévoir.', source: 'Code de commerce, art. L441-10' },
]
</script>

<template>
  <section
    id="loi"
    class="section section--paper"
    aria-labelledby="loi-titre"
  >
    <div class="container">
      <LandingSectionHead
        eyebrow="Ce que dit la loi"
        title="Vos droits, déjà calculés."
        title-id="loi-titre"
      >
        Entre professionnels, un retard de paiement se paie. Recouvra l’applique pour vous.
      </LandingSectionHead>
      <ul class="law">
        <li
          v-for="rule in rules"
          :key="rule.figure"
          class="law__item"
        >
          <span class="law__figure">{{ rule.figure }}</span>
          <span>{{ rule.text }}</span>
          <span class="law__source">{{ rule.source }}</span>
        </li>
      </ul>
    </div>
  </section>
</template>

<style scoped>
.law {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: var(--space-1);
  margin: 0;
  padding: 0;
  list-style: none;
}

.law__item {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  padding: var(--space-8) var(--space-6);
  background: var(--color-white);
}

.law__figure {
  font-size: var(--text-2xl);
  font-weight: 800;
  letter-spacing: -0.04em;
}

.law__source {
  margin-top: auto;
  font-size: var(--text-sm);
  color: var(--color-muted);
}
</style>
