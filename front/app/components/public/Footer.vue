<script setup lang="ts">
const columns = [
  { title: 'Produit', links: [{ to: '/', label: 'Fonctionnalités' }, { to: '/tarifs', label: 'Tarifs' }, { to: '/login', label: 'Se connecter' }] },
  { title: 'Outils gratuits', links: [{ to: '/outils/calcul-penalites-retard', label: 'Calcul des pénalités' }, { to: '/guides/mise-en-demeure-facture-impayee', label: 'Modèle de mise en demeure' }] },
  { title: 'Métiers', links: [{ to: '/metiers/plombier', label: 'Plombiers' }] },
  { title: 'Guides', links: [{ to: '/guides/mise-en-demeure-facture-impayee', label: 'Mise en demeure' }, { to: '/outils/calcul-penalites-retard', label: 'Pénalités de retard' }] },
]

const legal = [
  { to: '/mentions-legales', label: 'Mentions légales' },
  { to: '/cgu', label: 'CGU' },
  { to: '/confidentialite', label: 'Confidentialité' },
]
</script>

<template>
  <footer class="footer theme-dark">
    <div class="container footer__grid">
      <div class="footer__brand">
        <AppLogo style="--logo-hole: var(--color-ink-3)" />
        <p>Logiciel de relance de factures impayées pour les artisans et PME du BTP.</p>
        <a
          href="mailto:contact@recouvra.fr"
          class="footer__mail"
        >contact@recouvra.fr</a>
      </div>
      <nav
        v-for="column in columns"
        :key="column.title"
        :aria-label="column.title"
        class="footer__col"
      >
        <p class="footer__title">
          {{ column.title }}
        </p>
        <NuxtLink
          v-for="link in column.links"
          :key="link.label"
          :to="link.to"
        >
          {{ link.label }}
        </NuxtLink>
      </nav>
    </div>
    <div class="footer__bottom">
      <div class="container footer__bottom-inner">
        <span>© {{ new Date().getFullYear() }} Recouvra · Fait à Lyon</span>
        <nav
          aria-label="Informations légales"
          class="footer__legal"
        >
          <NuxtLink
            v-for="link in legal"
            :key="link.to"
            :to="link.to"
          >
            {{ link.label }}
          </NuxtLink>
        </nav>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  background: var(--color-ink-3);
  font-size: var(--text-sm);
  color: var(--color-text-on-dark);
}

.footer a {
  text-decoration: none;
}

.footer a:hover {
  color: var(--color-yellow);
}

.footer__grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: var(--space-8) var(--space-10);
  padding-block: var(--space-12) var(--space-10);
}

.footer__brand {
  grid-column: 1 / -1;
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  max-width: 320px;
  color: var(--color-muted-on-dark);
}

.footer__mail {
  align-self: flex-start;
  padding-bottom: 2px;
  border-bottom: 2px solid var(--color-yellow);
  font-weight: 600;
  color: var(--color-white);
}

.footer__col {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.footer__title {
  font-size: var(--text-xs);
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-muted-on-dark);
}

.footer__bottom {
  border-top: 1px solid var(--color-ink-2);
  color: var(--color-muted-on-dark);
}

.footer__bottom-inner {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: var(--space-3) var(--space-6);
  padding-block: var(--space-5);
}

.footer__legal {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2) var(--space-5);
}

@media (min-width: 1024px) {
  .footer__brand {
    grid-column: auto;
  }
}
</style>
