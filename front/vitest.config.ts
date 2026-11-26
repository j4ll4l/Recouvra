import { defineConfig } from 'vitest/config'

// Tests unitaires de la logique pure (shared/utils) : pas besoin de démarrer Nuxt
export default defineConfig({
  test: {
    include: ['tests/unit/**/*.test.ts'],
    environment: 'node',
  },
})
