<script setup lang="ts">
// <dialog> + showModal() : le navigateur garde le focus dans la modale,
// rend le reste de la page inerte et ferme avec Échap.
defineProps<{ title: string }>()
const open = defineModel<boolean>('open', { default: false })

const dialog = ref<HTMLDialogElement | null>(null)
const titleId = useId()

watch(open, (value) => {
  if (!dialog.value) return
  if (value && !dialog.value.open) dialog.value.showModal()
  if (!value && dialog.value.open) dialog.value.close()
})

onMounted(() => {
  if (open.value) dialog.value?.showModal()
})

// Clic sur le fond
function onClick(event: MouseEvent) {
  if (event.target === dialog.value) open.value = false
}
</script>

<template>
  <dialog
    ref="dialog"
    class="modal"
    :aria-labelledby="titleId"
    @close="open = false"
    @click="onClick"
  >
    <div class="modal__box">
      <div class="modal__head">
        <h2
          :id="titleId"
          class="modal__title"
        >
          {{ title }}
        </h2>
        <button
          type="button"
          class="modal__close"
          aria-label="Fermer"
          @click="open = false"
        >
          ✕
        </button>
      </div>
      <slot />
    </div>
  </dialog>
</template>

<style scoped>
.modal {
  width: min(520px, 100% - var(--space-8));
  padding: 0;
  border: 0;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
}

.modal::backdrop {
  background: rgb(20 22 25 / 55%);
}

.modal__box {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  padding: var(--space-6);
}

.modal__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-3);
}

.modal__title {
  font-size: var(--text-xl);
}

.modal__close {
  width: var(--tap-min);
  height: var(--tap-min);
  border: 0;
  border-radius: var(--radius-sm);
  background: transparent;
  font-size: var(--text-lg);
}
</style>
