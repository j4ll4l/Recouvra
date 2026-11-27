export default defineNuxtConfig({
  modules: ['@nuxt/eslint', '@pinia/nuxt', 'nuxt-auth-utils', '@nuxt/fonts'],
  devtools: { enabled: true },

  app: {
    head: {
      htmlAttrs: { lang: 'fr' },
      link: [
        { rel: 'icon', type: 'image/svg+xml', href: '/favicon.svg' },
        { rel: 'apple-touch-icon', href: '/apple-touch-icon.png' },
      ],
    },
  },

  css: ['~/assets/css/main.css'],

  runtimeConfig: {
    apiBaseUrl: 'http://localhost:8000/api', // port de compose.yaml
    session: { maxAge: 60 * 60 * 8 },
    public: {
      siteUrl: 'https://recouvra.fr', // surchargeable avec NUXT_PUBLIC_SITE_URL
    },
  },

  routeRules: {
    '/': { prerender: true },
    '/outils/calcul-penalites-retard': { prerender: true },
    '/tarifs': { prerender: true },
    '/dashboard/**': { ssr: false },
  },
  compatibilityDate: '2025-07-15',
  eslint: {
    config: {
      stylistic: true, // 2 espaces, guillemets simples, pas de ;
    },
  },

  fonts: {
    families: [
      { name: 'Archivo', provider: 'npm', weights: [400, 500, 600, 700, 800], styles: ['normal'], subsets: ['latin', 'latin-ext'] },
    ],
  },
})
