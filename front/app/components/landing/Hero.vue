<template>
  <section
    class="hero theme-dark"
    aria-labelledby="h1"
  >
    <div class="container hero__grid">
      <div class="hero__text">
        <span class="eyebrow">Logiciel de relance · BTP</span>
        <h1
          id="h1"
          class="hero__title"
        >
          Relance des factures impayées, pensée pour le BTP.
        </h1>
        <p class="hero__lead">
          Recouvra relance vos clients à J+3, J+15 et J+30, rédige la mise en demeure et vous montre qui vous doit quoi.
          Vous gardez la main, vos factures rentrent.
        </p>
        <div class="hero__actions">
          <UiButton
            to="/login"
            size="lg"
          >
            Essayer gratuitement →
          </UiButton>
          <a
            href="#relances"
            class="hero__ghost"
          >Voir la démo</a>
        </div>
        <ul class="hero__checks">
          <li>Essai gratuit de 14 jours</li>
          <li>Sans engagement</li>
          <li>Relances envoyées à votre nom</li>
        </ul>
      </div>

      <div class="hero__visual">
        <img
          src="/images/artisan-atelier-1200.webp"
          srcset="/images/artisan-atelier-600.webp 600w, /images/artisan-atelier-1200.webp 1200w"
          sizes="(min-width: 1024px) 560px, 100vw"
          width="1200"
          height="1440"
          alt="Artisan souriant dans son atelier, téléphone en main"
          fetchpriority="high"
          class="hero__photo"
        >
        <div
          class="hero__card hero__card--top"
          aria-hidden="true"
        >
          <strong>Paiement reçu · 2 940,00 €</strong>
          <span>Électricité Rhône · F-2026-130</span>
        </div>
        <div
          class="hero__card hero__card--bottom"
          aria-hidden="true"
        >
          <strong>Relance J+30 envoyée</strong>
          <span>SARL Morel Bâtiment · aujourd’hui, 9 h</span>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.hero {
  padding-block: var(--space-10) var(--space-16);
}

.hero__grid {
  display: grid;
  gap: var(--space-10);
  align-items: center;
}

.hero__text {
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
}

.hero__title {
  font-size: var(--text-3xl);
  letter-spacing: -0.035em;
}

.hero__lead {
  max-width: 560px;
  font-size: var(--text-lg);
  color: var(--color-text-on-dark);
}

.hero__actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-3);
}

.hero__ghost {
  display: inline-flex;
  align-items: center;
  min-height: 52px;
  padding: 0 var(--space-6);
  border: 1px solid var(--color-ink-line);
  border-radius: var(--radius-sm);
  font-weight: 600;
  text-decoration: none;
}

.hero__ghost:hover {
  border-color: var(--color-yellow);
  color: var(--color-yellow);
}

.hero__checks {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2) var(--space-6);
  margin: 0;
  padding: 0;
  list-style: none;
  font-size: var(--text-sm);
  color: var(--color-muted-on-dark);
}

.hero__checks li::before {
  content: '✓ ';
  font-weight: 700;
  color: var(--color-yellow);
}

.hero__visual {
  position: relative;
}

.hero__photo {
  width: 100%;
  height: 420px;
  object-fit: cover;
  border-radius: var(--radius-md);
}

.hero__card {
  display: flex;
  flex-direction: column;
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-md);
  background: var(--color-white);
  box-shadow: var(--shadow-lg);
  font-size: var(--text-sm);
  color: var(--color-ink);
}

.hero__card span {
  color: var(--color-muted);
}

.hero__card--top {
  display: none;
}

.hero__card--bottom {
  margin: calc(-1 * var(--space-8)) var(--space-3) 0;
  position: relative;
}

@media (min-width: 1024px) {
  .hero {
    padding-block: var(--space-16) var(--space-24);
  }

  .hero__grid {
    grid-template-columns: 1fr 1fr;
    gap: var(--space-16);
  }

  .hero__photo {
    height: 560px;
  }

  .hero__card {
    position: absolute;
    margin: 0;
  }

  .hero__card--top {
    display: flex;
    top: var(--space-6);
    right: calc(-1 * var(--space-6));
  }

  .hero__card--bottom {
    bottom: var(--space-8);
    left: calc(-1 * var(--space-8));
  }
}
</style>
