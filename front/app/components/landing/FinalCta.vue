<template>
  <section
    class="final section--yellow"
    aria-labelledby="cta-titre"
  >
    <div class="final__text">
      <h2
        id="cta-titre"
        class="final__title"
      >
        Arrêtez de relancer le soir, après le chantier.
      </h2>
      <p class="lead final__lead">
        Recouvra s’en charge pendant que vous travaillez.
      </p>
      <div class="final__actions">
        <UiButton
          to="/login"
          variant="dark"
          size="lg"
        >
          Essayer gratuitement
        </UiButton>
        <a
          href="#calcul"
          class="final__link"
        >Calculer mes pénalités</a>
      </div>
    </div>
    <img
      src="/images/artisan-soir-800.webp"
      srcset="/images/artisan-soir-400.webp 400w, /images/artisan-soir-800.webp 800w"
      sizes="(min-width: 1024px) 50vw, 100vw"
      width="800"
      height="600"
      alt="Artisan qui fait ses papiers tard le soir sur son ordinateur"
      loading="lazy"
      class="final__img"
    >
  </section>
</template>

<style scoped>
.final {
  display: grid;
}

.final__text {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
  padding: var(--space-12) var(--space-5);
}

.final__title {
  font-size: var(--text-3xl);
  font-weight: 800;
  letter-spacing: -0.04em;
  line-height: 1;
}

.final__lead {
  color: var(--color-ink);
}

.final__actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-5);
}

.final__link {
  padding-bottom: 2px;
  border-bottom: 2px solid var(--color-ink);
  font-weight: 600;
  text-decoration: none;
}

.final__img {
  width: 100%;
  height: 100%;
  min-height: 280px;
  object-fit: cover;
}

@media (min-width: 1024px) {
  .final {
    grid-template-columns: 1fr 1fr;
  }

  /* Texte aligné sur le contenu des autres sections */
  .final__text {
    padding-block: var(--space-24);
    padding-inline: max(var(--space-10), calc((100vw - var(--container)) / 2 + var(--space-10))) var(--space-16);
  }
}
</style>
