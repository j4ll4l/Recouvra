<script setup lang="ts">
const { user, fetch: refreshSession } = useUserSession()

async function logout() {
  await $fetch('/api/auth/logout', { method: 'POST' })
  await refreshSession() // met à jour l'état côté navigateur
  await navigateTo('/login')
}
</script>

<template>
  <div>
    <header>
      <nav aria-label="Espace connecté">
        <NuxtLink to="/dashboard">Tableau de bord</NuxtLink>
        <NuxtLink to="/dashboard/factures">Factures</NuxtLink>
        <NuxtLink to="/dashboard/clients">Clients</NuxtLink>
      </nav>
      <p>{{ user?.email }}</p>
      <button
        type="button"
        @click="logout"
      >
        Se déconnecter
      </button>
    </header>
    <main>
      <slot />
    </main>
  </div>
</template>
