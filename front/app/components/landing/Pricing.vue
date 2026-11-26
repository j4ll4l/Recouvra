<script setup lang="ts">
const included = [
  'Relances automatiques',
  'Lecture des factures PDF',
  'Rédaction par l’IA',
  '5 utilisateurs, avec leurs rôles',
  'Tableau de bord et exports CSV, PDF',
  'Pas de commission, pas de frais par relance',
  'Support par email, en français',
]
</script>

<template>
  <section
    id="tarifs"
    class="section theme-dark"
    aria-labelledby="tarifs-titre"
  >
    <div class="container pricing">
      <div class="pricing__price">
        <span class="eyebrow">Tarifs</span>
        <h2
          id="tarifs-titre"
          class="h2"
        >
          Un prix, toute l’entreprise.
        </h2>
        <p class="pricing__amount">
          29 €
        </p>
        <p class="lead">
          par mois, hors taxes, pour toute l’entreprise. Ou 290 € par an, 2 mois offerts.
        </p>
      </div>
      <div class="pricing__card">
        <ul class="pricing__list">
          <li
            v-for="item in included"
            :key="item"
          >
            {{ item }}
          </li>
        </ul>
        <UiButton
          to="/login"
          size="lg"
          block
        >
          Essai gratuit de 14 jours
        </UiButton>
        <p class="pricing__note">
          Sans engagement. Résiliable en ligne à tout moment.
        </p>
      </div>
    </div>
  </section>
</template>

<style scoped>
.pricing {
  display: grid;
  gap: var(--space-10);
  align-items: center;
}

.pricing__price {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.pricing__amount {
  font-size: clamp(5rem, 3rem + 8vw, 9rem);
  font-weight: 700;
  line-height: 0.9;
  letter-spacing: -0.055em;
  color: var(--color-yellow);
}

.pricing__card {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
  padding: var(--space-8);
  border-radius: var(--radius-md);
  background: var(--color-white);
  color: var(--color-text);

  --focus-color: var(--color-ink);
}

.pricing__list {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  margin: 0;
  padding: 0;
  list-style: none;
}

.pricing__list li::before {
  content: '✓ ';
  font-weight: 700;
  color: var(--color-success);
}

.pricing__note {
  font-size: var(--text-sm);
  text-align: center;
  color: var(--color-muted);
}

@media (min-width: 1024px) {
  .pricing {
    grid-template-columns: 3fr 2fr;
    gap: var(--space-16);
  }
}
</style>
