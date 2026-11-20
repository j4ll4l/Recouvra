<script setup lang="ts">
useSeoMeta({ title: 'Connexion | Recouvra', robots: 'noindex' })

const route = useRoute()
const { fetch: refreshSession } = useUserSession()

const form = reactive({ email: '', password: '' })
const error = ref<string | null>(null)
const pending = ref(false)

/** N'accepte qu'un chemin interne, pour éviter une redirection vers un site externe */
function safeRedirect(value: unknown): string {
  return typeof value === 'string' && value.startsWith('/') && !value.startsWith('//')
    ? value
    : '/dashboard'
}

async function onSubmit() {
  error.value = null
  pending.value = true
  try {
    await $fetch('/api/auth/login', { method: 'POST', body: form })
    await refreshSession()
    await navigateTo(safeRedirect(route.query.redirect))
  }
  catch (e) {
    const status = (e as { statusCode?: number }).statusCode
    error.value = status === 401
      ? 'Email ou mot de passe incorrect.'
      : 'Connexion impossible pour le moment. Réessayez dans un instant.'
  }
  finally {
    pending.value = false
  }
}
</script>

<template>
  <section>
    <h1>Connexion</h1>
    <form @submit.prevent="onSubmit">
      <label for="email">Email professionnel</label>
      <input
        id="email"
        v-model="form.email"
        type="email"
        autocomplete="email"
        required
      >

      <label for="password">Mot de passe</label>
      <input
        id="password"
        v-model="form.password"
        type="password"
        autocomplete="current-password"
        required
      >

      <p
        v-if="error"
        role="alert"
      >
        {{ error }}
      </p>

      <button
        type="submit"
        :disabled="pending"
      >
        {{ pending ? 'Connexion…' : 'Se connecter' }}
      </button>
    </form>
  </section>
</template>
