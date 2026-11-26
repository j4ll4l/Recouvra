<script setup lang="ts">
export interface FaqItem {
  question: string
  answer: string
}

defineProps<{ items: FaqItem[] }>()
</script>

<template>
  <section
    id="faq"
    class="section"
    aria-labelledby="faq-titre"
  >
    <div class="container faq">
      <div class="faq__intro">
        <span class="eyebrow">FAQ</span>
        <h2
          id="faq-titre"
          class="h2"
        >
          Questions fréquentes
        </h2>
        <p class="lead">
          Une autre question ? <a href="mailto:contact@recouvra.fr">Écrivez-nous</a>.
        </p>
      </div>
      <div class="faq__list">
        <details
          v-for="item in items"
          :key="item.question"
          class="faq__item"
        >
          <summary class="faq__question">
            {{ item.question }}<span
              class="faq__plus"
              aria-hidden="true"
            >+</span>
          </summary>
          <p class="faq__answer">
            {{ item.answer }}
          </p>
        </details>
      </div>
    </div>
  </section>
</template>

<style scoped>
.faq {
  display: grid;
  gap: var(--space-10);
}

.faq__intro {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.faq__list {
  border-top: 1px solid var(--color-line-strong);
}

.faq__item {
  border-bottom: 1px solid var(--color-line-strong);
}

.faq__question {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: var(--space-5);
  padding: var(--space-5) 0;
  font-size: var(--text-lg);
  font-weight: 600;
  list-style: none;
  cursor: pointer;
}

.faq__question::-webkit-details-marker {
  display: none;
}

.faq__plus {
  flex: none;
  font-size: 1.75rem;
  font-weight: 400;
  line-height: 1;
  transition: transform 200ms;
}

.faq__item[open] .faq__plus {
  transform: rotate(45deg);
}

.faq__answer {
  max-width: 680px;
  padding-bottom: var(--space-5);
  color: var(--color-muted);
}

@media (min-width: 1024px) {
  .faq {
    grid-template-columns: 1fr 2fr;
    gap: var(--space-16);
  }
}
</style>
