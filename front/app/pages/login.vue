<script setup lang="ts">
definePageMeta({ layout: false }) // page plein écran : pas de header ni de footer public

useSeoMeta({ title: 'Connexion | Recouvra', robots: 'noindex' })

const route = useRoute()
const { fetch: refreshSession } = useUserSession()

const form = reactive({ email: '', password: '' })
const error = ref<string | null>(null)
const pending = ref(false)
const showPassword = ref(false)

/**
 * N'accepte qu'un chemin interne, pour éviter une redirection vers un site externe.
 */
function safeRedirect(value: unknown): string {
  return typeof value === 'string' && value.startsWith('/') && !value.startsWith('//') && !value.includes('\\')
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
  <div class="login">
    <main class="login__main">
      <NuxtLink
        to="/"
        aria-label="Recouvra, accueil"
      >
        <AppLogo />
      </NuxtLink>

      <div class="login__box">
        <h1 class="login__title">
          Connexion
        </h1>
        <p class="login__lead">
          Retrouvez vos factures et vos relances.
        </p>

        <form
          class="login__form"
          novalidate
          @submit.prevent="onSubmit"
        >
          <UiField
            v-slot="{ id }"
            label="Email professionnel"
          >
            <input
              :id="id"
              v-model="form.email"
              type="email"
              autocomplete="email"
              required
            >
          </UiField>

          <UiField
            v-slot="{ id }"
            label="Mot de passe"
          >
            <div class="password">
              <input
                :id="id"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
              >
              <button
                type="button"
                class="password__toggle"
                :aria-pressed="showPassword"
                @click="showPassword = !showPassword"
              >
                {{ showPassword ? 'Masquer' : 'Afficher' }}<span class="sr-only"> le mot de passe</span>
              </button>
            </div>
          </UiField>

          <UiAlert
            v-if="error"
            tone="error"
          >
            {{ error }}
          </UiAlert>

          <UiButton
            type="submit"
            size="lg"
            block
            :loading="pending"
          >
            {{ pending ? 'Connexion…' : 'Se connecter' }}
          </UiButton>
        </form>

        <p class="login__signup">
          Pas encore de compte ? <NuxtLink to="/#tarifs">Découvrir Recouvra</NuxtLink>
        </p>
      </div>

      <p class="login__legal">
        © {{ new Date().getFullYear() }} Recouvra · Hébergé en France
      </p>
    </main>

    <div
      class="login__visual theme-dark"
      aria-hidden="true"
    >
      <img
        src="/images/artisan-atelier-1200.webp"
        width="1200"
        height="1440"
        alt=""
        class="login__img"
      >
      <p class="login__quote">
        Vos relances partent à 9 h. Vous, vous êtes sur le chantier.
      </p>
    </div>
  </div>
</template>

<style scoped>
.login {
  display: grid;
  min-height: 100vh;
  background: var(--color-white);
}

.login__main {
  display: flex;
  flex-direction: column;
  gap: var(--space-10);
  padding: var(--space-6) var(--space-5);
}

.login__box {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
  width: 100%;
  max-width: 420px;
  margin: auto;
}

.login__title {
  font-size: var(--text-2xl);
}

.login__lead {
  margin-top: calc(-1 * var(--space-3));
  color: var(--color-muted);
}

.login__form {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
}

.password {
  position: relative;
}

.password input {
  padding-right: 96px;
}

.password__toggle {
  position: absolute;
  top: 0;
  right: 0;
  height: 100%;
  min-width: var(--tap-min);
  padding: 0 var(--space-3);
  border: 0;
  background: transparent;
  font-size: var(--text-sm);
  font-weight: 600;
  color: var(--color-ink);
}

.login__signup {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

.login__signup a {
  font-weight: 600;
  color: var(--color-ink);
}

.login__legal {
  font-size: var(--text-xs);
  color: var(--color-muted);
}

.login__visual {
  display: none;
}

@media (min-width: 1024px) {
  .login {
    grid-template-columns: 1fr 1fr;
  }

  .login__main {
    padding: var(--space-8) var(--space-12);
  }

  .login__visual {
    position: relative;
    display: block;
    overflow: hidden;
  }

  .login__img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .login__quote {
    position: absolute;
    right: var(--space-10);
    bottom: var(--space-10);
    left: var(--space-10);
    padding: var(--space-5) var(--space-6);
    border-left: 4px solid var(--color-yellow);
    background: var(--color-ink);
    font-size: var(--text-xl);
    font-weight: 700;
    line-height: 1.3;
  }
}
</style>
