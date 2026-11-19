// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  modules: ['@nuxt/eslint', '@pinia/nuxt', 'nuxt-auth-utils'],

  runtimeConfig: {
    apiBaseUrl: 'http://localhost:8000/api', // port de ton compose.yaml
    session: { maxAge: 60 * 60 * 8 },
  },

  routeRules: {
    '/': { prerender: true },
    '/dashboard/**': { ssr: false },
  },
})
