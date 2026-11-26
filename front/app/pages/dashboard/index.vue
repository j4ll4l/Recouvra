<script setup lang="ts">
import type { Invoice } from '#shared/types'
import { daysBetween } from '#shared/utils/penalties'

definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Tableau de bord | Recouvra', robots: 'noindex' })

const invoices = useInvoices()
const today = todayIso()

/**
 * Factures en retard (échues + relancées), triées de la plus ancienne à la plus récente.
 * V1 on additionne côté navigateur
 * À remplacer par un endpoint de statistiques quand il existera.
 */
const { data: late, status: lateStatus, refresh: refreshLate } = await useAsyncData('dashboard-late', async () => {
  const [overdue, reminded] = await Promise.all([
    invoices.list({ 'status': 'overdue', 'itemsPerPage': 100, 'order[dueAt]': 'asc' }),
    invoices.list({ 'status': 'reminded', 'itemsPerPage': 100, 'order[dueAt]': 'asc' }),
  ])
  const all: Invoice[] = [...overdue.member, ...reminded.member].sort((a, b) => a.dueAt.localeCompare(b.dueAt))
  return {
    overdueCount: overdue.totalItems,
    remindedCount: reminded.totalItems,
    totalCents: all.reduce((sum, invoice) => sum + invoice.amountTtcCents, 0),
    oldest: all.slice(0, 5),
  }
})

// Pour un simple compteur, une facture par page, on lit totalItems
const { data: counts, status: countsStatus } = await useAsyncData('dashboard-counts', async () => {
  const [issued, disputed] = await Promise.all([
    invoices.list({ status: 'issued', itemsPerPage: 1 }),
    invoices.list({ status: 'disputed', itemsPerPage: 1 }),
  ])
  return { issued: issued.totalItems, disputed: disputed.totalItems }
})

const loading = computed(() => lateStatus.value === 'pending' || countsStatus.value === 'pending')
const failed = computed(() => lateStatus.value === 'error' || countsStatus.value === 'error')

const dateLabel = new Intl.DateTimeFormat('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' }).format(new Date())
const lateDays = (invoice: Invoice) => daysBetween(invoice.dueAt, today)
</script>

<template>
  <div class="dash">
    <header class="dash__head">
      <div>
        <h1 class="dash__title">
          Tableau de bord
        </h1>
        <p class="dash__date">
          {{ dateLabel }}
        </p>
      </div>
      <UiButton to="/dashboard/factures/nouvelle">
        Ajouter une facture
      </UiButton>
    </header>

    <UiAlert
      v-if="failed"
      tone="error"
    >
      Impossible de charger le tableau de bord.
      <button
        type="button"
        class="dash__retry"
        @click="refreshLate()"
      >
        Réessayer
      </button>
    </UiAlert>

    <section
      aria-label="Indicateurs"
      class="kpis"
      :aria-busy="loading"
    >
      <UiCard class="kpi kpi--strong">
        <p class="kpi__label">
          En retard (TTC)
        </p>
        <p class="kpi__value">
          {{ late ? formatCents(late.totalCents) : '—' }}
        </p>
        <p class="kpi__hint">
          {{ late ? `${late.overdueCount + late.remindedCount} factures` : 'Chargement…' }}
        </p>
      </UiCard>
      <UiCard class="kpi">
        <p class="kpi__label">
          Échues, pas encore relancées
        </p>
        <p class="kpi__value">
          {{ late?.overdueCount ?? '—' }}
        </p>
      </UiCard>
      <UiCard class="kpi">
        <p class="kpi__label">
          Relancées
        </p>
        <p class="kpi__value">
          {{ late?.remindedCount ?? '—' }}
        </p>
      </UiCard>
      <UiCard class="kpi">
        <p class="kpi__label">
          À échoir
        </p>
        <p class="kpi__value">
          {{ counts?.issued ?? '—' }}
        </p>
        <p
          v-if="counts?.disputed"
          class="kpi__hint"
        >
          + {{ counts.disputed }} en litige
        </p>
      </UiCard>
    </section>

    <div class="dash__grid">
      <UiCard aria-labelledby="todo-titre">
        <div class="block__head">
          <h2
            id="todo-titre"
            class="block__title"
          >
            À traiter
          </h2>
          <NuxtLink
            to="/dashboard/factures?status=overdue"
            class="block__link"
          >Toutes les factures échues</NuxtLink>
        </div>

        <p
          v-if="loading && !late"
          class="block__muted"
        >
          Chargement…
        </p>
        <UiEmptyState
          v-else-if="late && !late.oldest.length"
          title="Aucune facture en retard"
          text="Tout est à jour. Les relances repartiront dès qu’une facture sera échue."
        />
        <ul
          v-else-if="late"
          class="todo"
        >
          <li
            v-for="invoice in late.oldest"
            :key="invoice.id"
          >
            <NuxtLink
              :to="`/dashboard/factures/${invoice.id}`"
              class="todo__row"
            >
              <span class="todo__client">
                <strong>{{ invoice.client.name }}</strong>
                <span>{{ invoice.number }} · échue le {{ formatDate(invoice.dueAt) }}</span>
              </span>
              <span class="todo__late">{{ lateDays(invoice) }} j</span>
              <span class="todo__amount">{{ formatCents(invoice.amountTtcCents) }}</span>
              <InvoiceStatusBadge :status="invoice.status" />
            </NuxtLink>
          </li>
        </ul>
      </UiCard>

      <UiCard aria-labelledby="relances-titre">
        <h2
          id="relances-titre"
          class="block__title"
        >
          Relances des 7 prochains jours
        </h2>
        <UiEmptyState
          title="Arrive avec le module de relances"
          text="Vous verrez ici les relances programmées et les mises en demeure à valider."
        />
      </UiCard>
    </div>
  </div>
</template>

<style scoped>
.dash {
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
  max-width: 1200px;
}

.dash__head {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: flex-end;
  gap: var(--space-4);
}

.dash__title {
  font-size: var(--text-2xl);
}

.dash__date {
  color: var(--color-muted);
}

.dash__date::first-letter {
  text-transform: uppercase;
}

.dash__retry {
  margin-left: var(--space-2);
  border: 0;
  background: none;
  font-weight: 700;
  text-decoration: underline;
  color: inherit;
}

.kpis {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--space-3);
}

.kpi {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.kpi--strong {
  grid-column: 1 / -1;
  border-top: 3px solid var(--color-yellow);
}

.kpi__label,
.kpi__hint {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

.kpi__value {
  font-size: var(--text-xl);
  font-weight: 700;
  font-variant-numeric: tabular-nums;
}

.kpi--strong .kpi__value {
  font-size: var(--text-2xl);
}

.dash__grid {
  display: grid;
  gap: var(--space-4);
}

.block__head {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: baseline;
  gap: var(--space-2);
  margin-bottom: var(--space-3);
}

.block__title {
  margin-bottom: var(--space-3);
  font-size: var(--text-lg);
}

.block__head .block__title {
  margin-bottom: 0;
}

.block__link {
  font-size: var(--text-sm);
  font-weight: 600;
}

.block__muted {
  color: var(--color-muted);
}

.todo {
  margin: 0;
  padding: 0;
  list-style: none;
}

.todo__row {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: var(--space-1) var(--space-3);
  align-items: center;
  padding: var(--space-3) 0;
  border-top: 1px solid var(--color-line);
  text-decoration: none;
}

.todo__row:hover .todo__client strong {
  text-decoration: underline;
}

.todo__client {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.todo__client span {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

.todo__late {
  font-weight: 700;
  color: var(--color-warning);
}

.todo__amount {
  font-variant-numeric: tabular-nums;
}

@media (min-width: 768px) {
  .kpis {
    grid-template-columns: 2fr repeat(3, 1fr);
  }

  .kpi--strong {
    grid-column: auto;
  }

  .todo__row {
    grid-template-columns: 1fr 56px 130px 110px;
  }

  .todo__amount {
    text-align: right;
  }
}

@media (min-width: 1200px) {
  .dash__grid {
    grid-template-columns: 3fr 2fr;
  }
}
</style>
