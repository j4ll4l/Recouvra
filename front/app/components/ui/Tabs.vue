<script setup lang="ts">
export interface TabItem {
  value: string
  label: string
  count?: number
}

defineProps<{
  tabs: TabItem[]
  label: string
}>()
const model = defineModel<string>({ required: true })

const buttons = ref<HTMLButtonElement[]>([])

// Flèches gauche/droite, Début, Fin : on passe d'un onglet à l'autre (motif WAI-ARIA)
function onKeydown(event: KeyboardEvent, index: number, tabs: TabItem[]) {
  const last = tabs.length - 1
  const next = { ArrowRight: index === last ? 0 : index + 1, ArrowLeft: index === 0 ? last : index - 1, Home: 0, End: last }[event.key]
  if (next === undefined) return
  event.preventDefault()
  model.value = tabs[next]!.value
  buttons.value[next]?.focus()
}
</script>

<template>
  <div
    role="tablist"
    :aria-label="label"
    class="tabs"
  >
    <button
      v-for="(tab, index) in tabs"
      :key="tab.value"
      ref="buttons"
      type="button"
      role="tab"
      :aria-selected="model === tab.value"
      :tabindex="model === tab.value ? 0 : -1"
      class="tabs__tab"
      @click="model = tab.value"
      @keydown="onKeydown($event, index, tabs)"
    >
      {{ tab.label }}
      <span
        v-if="tab.count !== undefined"
        class="tabs__count"
      >{{ tab.count }}</span>
    </button>
  </div>
</template>

<style scoped>
.tabs {
  display: flex;
  gap: var(--space-1);
  overflow-x: auto;
  border-bottom: 1px solid var(--color-line-strong);
}

.tabs__tab {
  flex: none;
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  min-height: var(--tap-min);
  padding: 0 var(--space-3);
  margin-bottom: -1px;
  border: 0;
  border-bottom: 3px solid transparent;
  background: transparent;
  font-size: var(--text-sm);
  font-weight: 500;
  color: var(--color-muted);
}

.tabs__tab[aria-selected='true'] {
  border-bottom-color: var(--color-ink);
  font-weight: 700;
  color: var(--color-ink);
}

.tabs__count {
  padding: 1px 7px;
  border-radius: 10px;
  background: var(--color-line);
  font-size: var(--text-xs);
  font-weight: 700;
}

.tabs__tab[aria-selected='true'] .tabs__count {
  background: var(--color-ink);
  color: var(--color-white);
}
</style>
