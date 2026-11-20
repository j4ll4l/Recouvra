// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: ['@nuxt/eslint', '@pinia/nuxt', 'nuxt-auth-utils'],
  devtools: { enabled: true },

  runtimeConfig: {
    apiBaseUrl: 'http://localhost:8000/api', // port de ton compose.yaml
    session: { maxAge: 60 * 60 * 8 },
  },

  routeRules: {
    '/': { prerender: true },
    '/dashboard/**': { ssr: false },
  },
  compatibilityDate: '2025-07-15',
  eslint: {
    config: {
      stylistic: true, // 2 espaces, guillemets simples, pas de ;
    },
  },
})
