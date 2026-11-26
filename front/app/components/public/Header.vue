<script setup lang="ts">
const links = [
  { to: '/#fonctionnalites', label: 'Fonctionnalités' },
  { to: '/metiers/plombier', label: 'Métiers' },
  { to: '/outils/calcul-penalites-retard', label: 'Calcul des pénalités' },
  { to: '/guides/mise-en-demeure-facture-impayee', label: 'Guides' },
  { to: '/tarifs', label: 'Tarifs' },
]

const menuOpen = ref(false)
const route = useRoute()

// Le menu mobile se ferme quand on change de page
watch(() => route.fullPath, () => {
  menuOpen.value = false
})

function onKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape') menuOpen.value = false
}
</script>

<template>
  <header
    class="header theme-dark"
    @keydown="onKeydown"
  >
    <div class="header__top">
      <div class="container header__top-inner">
        <span>Logiciel de relance de factures pour les artisans et PME du BTP</span>
        <NuxtLink to="/login">Espace client →</NuxtLink>
      </div>
    </div>

    <div class="container header__bar">
      <NuxtLink
        to="/"
        aria-label="Recouvra, accueil"
        class="header__logo"
      >
        <AppLogo />
      </NuxtLink>

      <nav
        aria-label="Navigation principale"
        class="header__nav"
      >
        <NuxtLink
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          class="header__link"
        >
          {{ link.label }}
        </NuxtLink>
      </nav>

      <div class="header__actions">
        <NuxtLink
          to="/login"
          class="header__link header__login"
        >
          Se connecter
        </NuxtLink>
        <UiButton to="/login">
          Essai gratuit
        </UiButton>
        <button
          type="button"
          class="header__burger"
          :aria-expanded="menuOpen"
          aria-controls="menu-mobile"
          :aria-label="menuOpen ? 'Fermer le menu' : 'Ouvrir le menu'"
          @click="menuOpen = !menuOpen"
        >
          <svg
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.2"
            stroke-linecap="round"
            aria-hidden="true"
          >
            <path
              v-if="menuOpen"
              d="M6 6l12 12M18 6L6 18"
            />
            <path
              v-else
              d="M4 7h16M4 12h16M4 17h16"
            />
          </svg>
        </button>
      </div>
    </div>

    <nav
      v-show="menuOpen"
      id="menu-mobile"
      aria-label="Navigation principale"
      class="header__menu"
    >
      <NuxtLink
        v-for="link in links"
        :key="link.to"
        :to="link.to"
        class="header__menu-link"
      >
        {{ link.label }}<span aria-hidden="true">→</span>
      </NuxtLink>
      <UiButton
        to="/login"
        variant="outline"
        size="lg"
        block
      >
        Se connecter
      </UiButton>
    </nav>
  </header>
</template>

<style scoped>
.header {
  position: sticky;
  top: 0;
  z-index: 10;
  border-bottom: 1px solid var(--color-ink-2);
}

.header a {
  text-decoration: none;
}

.header__top {
  display: none;
  background: var(--color-ink-3);
  font-size: var(--text-sm);
  color: var(--color-muted-on-dark);
}

.header__top-inner {
  display: flex;
  justify-content: space-between;
  padding-block: var(--space-2);
}

.header__bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-6);
  min-height: 64px;
}

.header__nav,
.header__login {
  display: none;
}

.header__nav {
  gap: var(--space-6);
}

.header__link {
  font-size: var(--text-sm);
  font-weight: 500;
}

.header__link:hover,
.header__link.router-link-exact-active {
  color: var(--color-yellow);
}

.header__actions {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.header__burger {
  display: flex;
  align-items: center;
  justify-content: center;
  width: var(--tap-min);
  height: var(--tap-min);
  border: 0;
  background: transparent;
  color: inherit;
}

.header__menu {
  display: flex;
  flex-direction: column;
  padding: var(--space-2) var(--space-5) var(--space-6);
  border-top: 1px solid var(--color-ink-2);
}

.header__menu-link {
  display: flex;
  justify-content: space-between;
  align-items: center;
  min-height: 56px;
  margin-bottom: 0;
  border-bottom: 1px solid var(--color-ink-2);
  font-size: var(--text-lg);
  font-weight: 600;
}

.header__menu-link:last-of-type {
  margin-bottom: var(--space-5);
}

.header__menu-link.router-link-exact-active {
  color: var(--color-yellow);
}

@media (min-width: 1024px) {
  .header {
    position: static;
  }

  .header__top,
  .header__nav {
    display: flex;
  }

  .header__login {
    display: inline;
    margin-right: var(--space-3);
  }

  .header__burger,
  .header__menu {
    display: none;
  }
}
</style>
