<script setup lang="ts">
import type { Invoice, InvoiceStatus } from '#shared/types'
import type { InvoiceFilters } from '~/composables/useInvoices'
import { formatCents, formatDate } from '#shared/utils/format'
import { INVOICE_STATUS_LABELS, isInvoiceStatus } from '#shared/utils/invoice-status'
import { countPages } from '#shared/utils/pagination'
import { daysBetween } from '#shared/utils/penalties'

definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Factures | Recouvra', robots: 'noindex' })

const route = useRoute()
const invoices = useInvoices()
const today = todayIso()

// ---------- État lu dans l'URL : lien partageable et bouton retour fonctionnel ----------
const SORTS = {
  'due-asc': { label: 'Échéance la plus ancienne', order: { 'order[dueAt]': 'asc' } },
  'due-desc': { label: 'Échéance la plus récente', order: { 'order[dueAt]': 'desc' } },
  'amount-desc': { label: 'Montant le plus élevé', order: { 'order[amountTtcCents]': 'desc' } },
  'amount-asc': { label: 'Montant le plus faible', order: { 'order[amountTtcCents]': 'asc' } },
} satisfies Record<string, { label: string, order: InvoiceFilters }>
type SortKey = keyof typeof SORTS

const page = computed(() => Math.max(1, Number(route.query.page) || 1))
const status = computed(() => (isInvoiceStatus(route.query.status) ? route.query.status : undefined))
const search = computed(() => (typeof route.query.q === 'string' ? route.query.q : ''))
const sort = computed<SortKey>(() => (typeof route.query.sort === 'string' && route.query.sort in SORTS ? route.query.sort as SortKey : 'due-asc'))

/** Change l'URL ; tout changement de filtre ramène à la page 1 */
function setQuery(changes: Record<string, string | undefined>) {
  return navigateTo({ query: { ...route.query, ...changes, page: undefined } })
}

// ---------- Données ----------
const { data, status: loadState, error, refresh } = await useAsyncData(
  'invoices',
  () => invoices.list({
    page: page.value,
    status: status.value,
    number: search.value || undefined,
    ...SORTS[sort.value].order,
  }),
  { watch: [page, status, search, sort] },
)

// Compteurs des onglets : une facture par page suffit, on lit totalItems
const TAB_STATUSES: InvoiceStatus[] = ['overdue', 'reminded', 'issued', 'paid', 'disputed', 'draft', 'written_off']
const { data: counts } = await useAsyncData('invoice-counts', async () => {
  const [all, ...byStatus] = await Promise.all([
    invoices.list({ itemsPerPage: 1 }),
    ...TAB_STATUSES.map(value => invoices.list({ status: value, itemsPerPage: 1 })),
  ])
  const result: Partial<Record<InvoiceStatus | 'all', number>> = { all: all.totalItems }
  TAB_STATUSES.forEach((value, i) => {
    result[value] = byStatus[i]?.totalItems
  })
  return result
})

const tabs = computed(() => [
  { value: '', label: 'Toutes', count: counts.value?.all },
  ...TAB_STATUSES.map(value => ({ value, label: INVOICE_STATUS_LABELS[value], count: counts.value?.[value] })),
])

const tabModel = computed({
  get: () => status.value ?? '',
  set: (value: string) => setQuery({ status: value || undefined }),
})

const sortModel = computed({
  get: () => sort.value,
  set: (value: string) => setQuery({ sort: value === 'due-asc' ? undefined : value }),
})

// Recherche : on attend 300 ms après la dernière frappe avant d'interroger l'API
const searchInput = ref(search.value)
let timer: ReturnType<typeof setTimeout> | undefined
watch(searchInput, (value) => {
  clearTimeout(timer)
  timer = setTimeout(() => setQuery({ q: value.trim() || undefined }), 300)
})

const totalPages = computed(() => countPages(data.value?.totalItems ?? 0))
const isLate = (invoice: Invoice) => invoice.status === 'overdue' || invoice.status === 'reminded'
const lateDays = (invoice: Invoice) => daysBetween(invoice.dueAt, today)
const detailUrl = (invoice: Invoice) => `/dashboard/factures/${invoice.id}`
</script>

<template>
  <div class="list">
    <header class="list__head">
      <h1 class="list__title">
        Factures
      </h1>
      <UiButton to="/dashboard/factures/nouvelle">
        Ajouter une facture
      </UiButton>
    </header>

    <UiTabs
      v-model="tabModel"
      :tabs="tabs"
      label="Filtrer par statut"
    />

    <div class="list__tools">
      <div class="list__search">
        <label
          for="search"
          class="sr-only"
        >Rechercher par numéro de facture</label>
        <input
          id="search"
          v-model="searchInput"
          type="search"
          placeholder="Rechercher un numéro, ex. F-2026-0004"
          autocomplete="off"
        >
      </div>
      <label class="list__sort">
        <span>Trier par</span>
        <select v-model="sortModel">
          <option
            v-for="(option, key) in SORTS"
            :key="key"
            :value="key"
          >
            {{ option.label }}
          </option>
        </select>
      </label>
    </div>

    <p
      v-if="loadState === 'pending' && !data"
      class="list__muted"
    >
      Chargement…
    </p>
    <UiAlert
      v-else-if="error"
      tone="error"
    >
      Impossible de charger les factures.
      <button
        type="button"
        class="list__retry"
        @click="refresh()"
      >
        Réessayer
      </button>
    </UiAlert>
    <UiEmptyState
      v-else-if="!data?.member.length && (search || status)"
      title="Aucune facture ne correspond"
      text="Changez d’onglet ou modifiez la recherche."
    />
    <UiEmptyState
      v-else-if="!data?.member.length"
      title="Aucune facture pour l’instant"
      text="Ajoutez votre première facture : Recouvra s’occupe des relances."
    >
      <UiButton to="/dashboard/factures/nouvelle">
        Ajouter une facture
      </UiButton>
    </UiEmptyState>

    <template v-else-if="data">
      <!-- Ordinateur : tableau -->
      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">
            {{ data.totalItems }} facture(s)
          </caption>
          <thead>
            <tr>
              <th scope="col">
                N°
              </th>
              <th scope="col">
                Client
              </th>
              <th scope="col">
                Échéance
              </th>
              <th scope="col">
                Retard
              </th>
              <th
                scope="col"
                class="num"
              >
                Montant TTC
              </th>
              <th scope="col">
                Statut
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="invoice in data.member"
              :key="invoice.id"
              class="table__row"
            >
              <td>
                <!-- Le lien couvre toute la ligne (voir .table__link::after) -->
                <NuxtLink
                  :to="detailUrl(invoice)"
                  class="table__link"
                >{{ invoice.number }}</NuxtLink>
              </td>
              <td>{{ invoice.client.name }}</td>
              <td>{{ formatDate(invoice.dueAt) }}</td>
              <td>
                <span
                  v-if="isLate(invoice)"
                  class="late"
                >{{ lateDays(invoice) }} j</span>
              </td>
              <td class="num">
                {{ formatCents(invoice.amountTtcCents) }}
              </td>
              <td><InvoiceStatusBadge :status="invoice.status" /></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile : cartes -->
      <ul class="cards">
        <li
          v-for="invoice in data.member"
          :key="invoice.id"
        >
          <NuxtLink
            :to="detailUrl(invoice)"
            class="card"
          >
            <span class="card__top">
              <strong>{{ invoice.client.name }}</strong>
              <strong class="num">{{ formatCents(invoice.amountTtcCents) }}</strong>
            </span>
            <span class="card__meta">{{ invoice.number }} · échéance {{ formatDate(invoice.dueAt) }}</span>
            <span class="card__bottom">
              <InvoiceStatusBadge :status="invoice.status" />
              <span
                v-if="isLate(invoice)"
                class="late"
              >{{ lateDays(invoice) }} j de retard</span>
            </span>
          </NuxtLink>
        </li>
      </ul>

      <AppPagination
        :page="page"
        :total-pages="totalPages"
      />
    </template>
  </div>
</template>

<style scoped>
.list {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
  max-width: 1200px;
}

.list__head {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: var(--space-4);
}

.list__title {
  font-size: var(--text-2xl);
}

.list__tools {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-3);
}

.list__search {
  flex: 1 1 260px;
}

.list__search input,
.list__sort select {
  width: 100%;
  min-height: var(--tap-min);
  padding: 0 var(--space-3);
  border: 1px solid var(--color-line-strong);
  border-radius: var(--radius-sm);
  background: var(--color-white);
  font-size: var(--text-base);
}

.list__sort {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  flex: 1 1 240px;
  font-size: var(--text-sm);
  color: var(--color-muted);
}

.list__sort span {
  flex: none;
}

.list__muted {
  color: var(--color-muted);
}

.list__retry {
  margin-left: var(--space-2);
  border: 0;
  background: none;
  font-weight: 700;
  text-decoration: underline;
  color: inherit;
}

.num {
  text-align: right;
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
}

.late {
  font-weight: 700;
  color: var(--color-warning);
  white-space: nowrap;
}

/* ---------- Mobile : cartes ---------- */
.table-wrap {
  display: none;
}

.cards {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  margin: 0;
  padding: 0;
  list-style: none;
}

.card {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  padding: var(--space-4);
  border: 1px solid var(--color-line);
  border-radius: var(--radius-md);
  background: var(--color-white);
  text-decoration: none;
}

.card__top,
.card__bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: var(--space-3);
}

.card__meta {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

/* ---------- Ordinateur : tableau ---------- */
@media (min-width: 768px) {
  .cards {
    display: none;
  }

  .table-wrap {
    display: block;
    overflow-x: auto;
    border: 1px solid var(--color-line);
    border-radius: var(--radius-md);
    background: var(--color-white);
  }

  .table {
    width: 100%;
    border-collapse: collapse;
    font-size: var(--text-sm);
  }

  .table th {
    padding: var(--space-3) var(--space-4);
    border-bottom: 1px solid var(--color-line);
    font-size: var(--text-xs);
    font-weight: 600;
    letter-spacing: 0.05em;
    text-align: left;
    text-transform: uppercase;
    color: var(--color-muted);
  }

  .table td {
    padding: var(--space-3) var(--space-4);
    border-bottom: 1px solid var(--color-line);
  }

  .table th.num,
  .table td.num {
    text-align: right;
  }

  .table__row {
    position: relative;
  }

  .table__row:hover {
    background: var(--color-paper);
  }

  .table__link {
    font-weight: 700;
    text-decoration: none;
  }

  /* Toute la ligne devient cliquable, sans imbriquer de lien dans un <tr> */
  .table__link::after {
    content: '';
    position: absolute;
    inset: 0;
  }
}
</style>
