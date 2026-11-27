<script setup lang="ts">
import type { Client } from '#shared/types'
import { countPages } from '#shared/utils/pagination'

definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Clients | Recouvra', robots: 'noindex' })

const route = useRoute()
const clients = useClients()

// Page, recherche et tri viennent de l'URL
const page = computed(() => Math.max(1, Number(route.query.page) || 1))
const name = computed(() =>
  typeof route.query.name === 'string' ? route.query.name : '',
)
const order = computed<'asc' | 'desc'>(() => (route.query.order === 'desc' ? 'desc' : 'asc'))

const {
  data,
  status: loadState,
  error,
  refresh,
} = await useAsyncData(
  'clients',
  () => clients.list({ 'page': page.value, 'name': name.value || undefined, 'order[name]': order.value }),
  { watch: [page, name, order] },
)

const totalPages = computed(() => countPages(data.value?.totalItems ?? 0))

// 300 ms après la dernière frappe avant de mettre à jour l'URL
const search = ref(name.value)
let timer: ReturnType<typeof setTimeout> | undefined

watch(search, (value) => {
  clearTimeout(timer)
  timer = setTimeout(() => {
    navigateTo({
      query: {
        ...route.query,
        name: value.trim() || undefined,
        page: undefined,
      },
    })
  }, 300)
})

watch(name, (value) => {
  if (value !== search.value.trim()) search.value = value
})

onBeforeUnmount(() => clearTimeout(timer))

const orderModel = computed({
  get: () => order.value,
  set: (value: string) => navigateTo({ query: { ...route.query, order: value === 'desc' ? 'desc' : undefined, page: undefined } }),
})

const detailUrl = (client: Client) => `/dashboard/clients/${client.id}`
</script>

<template>
  <div class="list">
    <header class="list__head">
      <h1 class="list__title">
        Clients
      </h1>
      <UiButton to="/dashboard/clients/nouveau">
        Ajouter un client
      </UiButton>
    </header>

    <div class="list__tools">
      <div class="list__search">
        <label
          for="search"
          class="sr-only"
        >Rechercher un client par son nom</label>
        <input
          id="search"
          v-model="search"
          type="search"
          placeholder="Rechercher un client par son nom"
          autocomplete="off"
        >
      </div>
      <label class="list__sort">
        <span>Trier</span>
        <select v-model="orderModel">
          <option value="asc">
            De A à Z
          </option>
          <option value="desc">
            De Z à A
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
      Impossible de charger les clients.
      <button
        type="button"
        class="list__retry"
        @click="refresh()"
      >
        Réessayer
      </button>
    </UiAlert>
    <UiEmptyState
      v-else-if="!data?.member.length && name"
      :title="`Aucun client ne correspond à « ${name} »`"
      text="Vérifiez l’orthographe ou cherchez une partie du nom."
    />
    <UiEmptyState
      v-else-if="!data?.member.length"
      title="Aucun client pour l’instant"
      text="Ajoutez vos clients pour leur rattacher des factures."
    >
      <UiButton to="/dashboard/clients/nouveau">
        Ajouter un client
      </UiButton>
    </UiEmptyState>

    <template v-else-if="data">
      <!-- Ordinateur : tableau -->
      <div class="table-wrap">
        <table class="table">
          <caption class="sr-only">
            {{ data.totalItems }} client(s)
          </caption>
          <thead>
            <tr>
              <th scope="col">
                Nom
              </th>
              <th scope="col">
                Email
              </th>
              <th scope="col">
                Téléphone
              </th>
              <th scope="col">
                SIRET
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="client in data.member"
              :key="client.id"
              class="table__row"
            >
              <td>
                <!-- Le lien couvre toute la ligne (voir .table__link::after) -->
                <NuxtLink
                  :to="detailUrl(client)"
                  class="table__link"
                >{{ client.name }}</NuxtLink>
              </td>
              <td>{{ client.email }}</td>
              <td>{{ client.phone ?? '—' }}</td>
              <td class="mono">
                {{ client.siret ?? '—' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile : cartes -->
      <ul class="cards">
        <li
          v-for="client in data.member"
          :key="client.id"
        >
          <NuxtLink
            :to="detailUrl(client)"
            class="card"
          >
            <strong>{{ client.name }}</strong>
            <span class="card__meta">{{ client.email }}</span>
            <span
              v-if="client.phone"
              class="card__meta"
            >{{ client.phone }}</span>
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
  flex: 3 1 260px;
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
  flex: 1 1 180px;
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

.mono {
  font-variant-numeric: tabular-nums;
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
  gap: var(--space-1);
  padding: var(--space-4);
  border: 1px solid var(--color-line);
  border-radius: var(--radius-md);
  background: var(--color-white);
  text-decoration: none;
}

.card__meta {
  overflow: hidden;
  text-overflow: ellipsis;
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
