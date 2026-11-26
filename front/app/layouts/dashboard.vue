<script setup lang="ts">
const { user, fetch: refreshSession } = useUserSession()
const route = useRoute()

// L'espace connecté ne doit jamais apparaître dans Google
useSeoMeta({ robots: 'noindex, nofollow' })

// Relances et Paramètres seront ajoutés quand leurs pages existeront (atelier 5 et suivants)
const links = [
  { to: '/dashboard', label: 'Tableau de bord', short: 'Accueil', icon: 'home' },
  { to: '/dashboard/factures', label: 'Factures', short: 'Factures', icon: 'file' },
  { to: '/dashboard/clients', label: 'Clients', short: 'Clients', icon: 'users' },
] as const

// Lien actif : correspondance exacte pour l'accueil, par préfixe pour le reste (/dashboard/factures/12…)
function isActive(to: string) {
  return to === '/dashboard' ? route.path === to : route.path.startsWith(to)
}

const pageTitle = computed(() => [...links].reverse().find(link => isActive(link.to))?.label ?? 'Recouvra')
const initials = computed(() => (user.value?.email ?? '?').slice(0, 2).toUpperCase())

async function logout() {
  await $fetch('/api/auth/logout', { method: 'POST' })
  await refreshSession() // met à jour l'état côté navigateur
  await navigateTo('/login')
}
</script>

<template>
  <div class="app">
    <a
      href="#contenu"
      class="skip-link"
    >Aller au contenu</a>

    <!-- Ordinateur : barre latérale -->
    <aside class="sidebar theme-dark">
      <NuxtLink
        to="/dashboard"
        aria-label="Recouvra, tableau de bord"
        class="sidebar__logo"
      >
        <AppLogo :height="24" />
      </NuxtLink>
      <nav
        aria-label="Espace connecté"
        class="sidebar__nav"
      >
        <NuxtLink
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          class="sidebar__link"
          :aria-current="isActive(link.to) ? 'page' : undefined"
        >
          <AppIcon :name="link.icon" />
          {{ link.label }}
        </NuxtLink>
      </nav>
      <div class="sidebar__user">
        <span
          class="avatar"
          aria-hidden="true"
        >{{ initials }}</span>
        <span class="sidebar__email">{{ user?.email }}</span>
        <button
          type="button"
          class="sidebar__logout"
          aria-label="Se déconnecter"
          @click="logout"
        >
          <AppIcon name="logout" />
        </button>
      </div>
    </aside>

    <!-- Mobile : barre du haut -->
    <header class="topbar theme-dark">
      <NuxtLink
        to="/dashboard"
        aria-label="Recouvra, tableau de bord"
      >
        <AppLogo
          :height="20"
          :with-text="false"
        />
      </NuxtLink>
      <p class="topbar__title">
        {{ pageTitle }}
      </p>
      <button
        type="button"
        class="topbar__logout"
        @click="logout"
      >
        Déconnexion
      </button>
    </header>

    <main
      id="contenu"
      tabindex="-1"
      class="app__main"
    >
      <slot />
    </main>

    <!-- Mobile : barre d'onglets -->
    <nav
      aria-label="Espace connecté"
      class="tabbar"
    >
      <NuxtLink
        v-for="link in links.slice(0, 2)"
        :key="link.to"
        :to="link.to"
        class="tabbar__link"
        :aria-current="isActive(link.to) ? 'page' : undefined"
      >
        <AppIcon :name="link.icon" />
        {{ link.short }}
      </NuxtLink>
      <NuxtLink
        to="/dashboard/factures/nouvelle"
        class="tabbar__add"
        aria-label="Ajouter une facture"
      >
        <AppIcon
          name="plus"
          :size="26"
        />
      </NuxtLink>
      <NuxtLink
        v-for="link in links.slice(2)"
        :key="link.to"
        :to="link.to"
        class="tabbar__link"
        :aria-current="isActive(link.to) ? 'page' : undefined"
      >
        <AppIcon :name="link.icon" />
        {{ link.short }}
      </NuxtLink>
    </nav>
  </div>
</template>

<style scoped>
.app {
  min-height: 100vh;
  background: var(--color-paper);
}

.app a {
  text-decoration: none;
}

.app__main {
  /* De la place en bas pour la barre d'onglets */
  padding: var(--space-5) var(--space-4) calc(72px + var(--space-6));
}

.app__main:focus {
  outline: none;
}

.skip-link {
  position: absolute;
  top: var(--space-3);
  left: -9999px;
  z-index: 30;
  padding: var(--space-3) var(--space-4);
  background: var(--color-yellow);
  color: var(--color-ink);
  font-weight: 700;
}

.skip-link:focus {
  left: var(--space-4);
}

/* ---------- Mobile ---------- */
.sidebar {
  display: none;
}

.topbar {
  position: sticky;
  top: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  gap: var(--space-3);
  height: 56px;
  padding: 0 var(--space-4);
}

.topbar__title {
  flex: 1;
  font-weight: 700;
}

.topbar__logout {
  min-height: var(--tap-min);
  border: 0;
  background: transparent;
  font-size: var(--text-sm);
  color: var(--color-muted-on-dark);
}

.tabbar {
  position: fixed;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 10;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  align-items: center;
  height: 64px;
  padding-bottom: env(safe-area-inset-bottom);
  border-top: 1px solid var(--color-line);
  background: var(--color-white);
}

.tabbar__link {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
  height: 100%;
  font-size: var(--text-xs);
  font-weight: 600;
  color: var(--color-muted);
}

.tabbar__link[aria-current='page'] {
  color: var(--color-ink);
}

.tabbar__link[aria-current='page'] svg {
  color: var(--color-ink);
  stroke-width: 2.6;
}

.tabbar__add {
  justify-self: center;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: var(--color-yellow);
  color: var(--color-ink);
}

/* ---------- Ordinateur ---------- */
@media (min-width: 1024px) {
  .app {
    display: grid;
    grid-template-columns: 248px 1fr;
  }

  .topbar,
  .tabbar {
    display: none;
  }

  .sidebar {
    position: sticky;
    top: 0;
    display: flex;
    flex-direction: column;
    gap: var(--space-8);
    height: 100vh;
    padding: var(--space-6) var(--space-4);
  }

  .sidebar__logo {
    padding: 0 var(--space-2);
  }

  .sidebar__nav {
    display: flex;
    flex-direction: column;
    gap: var(--space-1);
  }

  .sidebar__link {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    min-height: var(--tap-min);
    padding: 0 var(--space-3);
    border-radius: var(--radius-sm);
    font-size: var(--text-sm);
    font-weight: 500;
    color: var(--color-text-on-dark);
  }

  .sidebar__link:hover {
    background: var(--color-ink-2);
  }

  .sidebar__link[aria-current='page'] {
    background: var(--color-ink-2);
    font-weight: 700;
    color: var(--color-white);
    box-shadow: inset 3px 0 0 var(--color-yellow);
  }

  .sidebar__user {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    margin-top: auto;
    padding-top: var(--space-4);
    border-top: 1px solid var(--color-ink-2);
  }

  .sidebar__email {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: var(--text-sm);
    color: var(--color-muted-on-dark);
  }

  .sidebar__logout {
    display: flex;
    align-items: center;
    justify-content: center;
    width: var(--tap-min);
    height: var(--tap-min);
    border: 0;
    border-radius: var(--radius-sm);
    background: transparent;
    color: var(--color-muted-on-dark);
  }

  .sidebar__logout:hover {
    background: var(--color-ink-2);
    color: var(--color-white);
  }

  .app__main {
    padding: var(--space-8) var(--space-10);
  }
}

.avatar {
  flex: none;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--color-yellow);
  font-size: var(--text-xs);
  font-weight: 700;
  color: var(--color-ink);
}
</style>
