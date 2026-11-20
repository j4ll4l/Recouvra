/**
 * Client HTTP vers le proxy BFF.
 * Toutes les pages passent par ici et non par /api/backend directement.
 */
export function useApi() {
  const nuxtApp = useNuxtApp()

  return $fetch.create({
    baseURL: '/api/backend',
    headers: {
      'Accept': 'application/ld+json',
      'Content-Type': 'application/ld+json',
    },
    async onResponseError({ response }) {
      // Session absente ou expirée : retour à la connexion
      if (response.status === 401) {
        await nuxtApp.runWithContext(() => navigateTo('/login'))
      }
    },
  })
}
