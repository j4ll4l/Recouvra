<script setup lang="ts">
import { countPages } from '#shared/utils/pagination'

definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Clients | Recouvra', robots: 'noindex' })

const route = useRoute()
const clients = useClients()

// Page et recherche viennent de l'URL
const page = computed(() => Math.max(1, Number(route.query.page) || 1))
const name = computed(() =>
  typeof route.query.name === 'string' ? route.query.name : '',
)

const {
  data,
  status: loadState,
  error,
  refresh,
} = await useAsyncData(
  'clients',
  () => clients.list({ page: page.value, name: name.value || undefined }),
  { watch: [page, name] },
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
</script>

<template>
  <section>
    <h1>Clients</h1>

    <NuxtLink to="/dashboard/clients/nouveau">Nouveau client</NuxtLink>

    <label for="search">Rechercher un client</label>
    <input
      id="search"
      v-model="search"
      type="search"
      placeholder="Nom du client"
      autocomplete="off"
    >

    <p v-if="loadState === 'pending'">
      Chargement…
    </p>
    <p
      v-else-if="error"
      role="alert"
    >
      Impossible de charger les clients.
      <button
        type="button"
        @click="refresh()"
      >
        Réessayer
      </button>
    </p>
    <p v-else-if="!data?.member.length">
      {{ name ? `Aucun client ne correspond à « ${name} ».` : "Aucun client." }}
    </p>

    <template v-else>
      <table>
        <caption>
          {{
            data.totalItems
          }}
          client(s)
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
          >
            <td>{{ client.name }}</td>
            <td>{{ client.email }}</td>
            <td>{{ client.phone ?? "—" }}</td>
            <td>{{ client.siret ?? "—" }}</td>
          </tr>
        </tbody>
      </table>

      <AppPagination
        :page="page"
        :total-pages="totalPages"
      />
    </template>
  </section>
</template>
