<template>
  <section
    id="calcul"
    class="section"
    aria-labelledby="calcul-titre"
  >
    <div class="container calc-section">
      <div class="calc-section__text">
        <span class="eyebrow">Outil gratuit</span>
        <h2
          id="calcul-titre"
          class="h2"
        >
          Calcul des pénalités de retard
        </h2>
        <p class="lead">
          Calculez ce que votre client vous doit, gratuitement et sans créer de compte.
        </p>
        <p class="calc-section__formula">
          Formule : montant TTC × taux annuel × jours de retard ÷ 365, plus 40 € d’indemnité entre professionnels.
        </p>
      </div>
      <PenaltyCalculator />
    </div>
  </section>
</template>

<style scoped>
.calc-section {
  display: grid;
  gap: var(--space-10);
}

.calc-section__text {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  max-width: 720px;
}

.calc-section__formula {
  font-size: var(--text-sm);
  color: var(--color-muted);
}
</style>
