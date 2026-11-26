<template>
  <div class="layout">
    <a
      href="#contenu"
      class="skip-link"
    >Aller au contenu</a>
    <PublicHeader />
    <main
      id="contenu"
      tabindex="-1"
      class="layout__main"
    >
      <slot />
    </main>
    <PublicFooter />
  </div>
</template>

<style scoped>
.layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.layout__main {
  flex: 1;
}

.layout__main:focus {
  outline: none;
}

/* Invisible tant qu'on n'arrive pas dessus avec Tab */
.skip-link {
  position: absolute;
  top: var(--space-3);
  left: -9999px;
  z-index: 20;
  padding: var(--space-3) var(--space-4);
  background: var(--color-yellow);
  color: var(--color-ink);
  font-weight: 700;
}

.skip-link:focus {
  left: var(--space-4);
}
</style>
