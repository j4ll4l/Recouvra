<script setup lang="ts">
import { formatCents, formatDate } from '#shared/utils/format'
import { INVOICE_STATUS_LABELS, isInvoiceStatus } from '#shared/utils/invoice-status'
import { countPages } from '#shared/utils/pagination'

definePageMeta({ layout: 'dashboard' })
useSeoMeta({ title: 'Factures | Recouvra', robots: 'noindex' })

const route = useRoute()
const invoices = useInvoices()

// Les filtres viennent de l'URL : lien partageable et bouton retour fonctionnel
const page = computed(() => Math.max(1, Number(route.query.page) || 1))
const status = computed(() => isInvoiceStatus(route.query.status) ? route.query.status : undefined)

const { data, status: loadState, error, refresh } = await useAsyncData(
  'invoices',
  () => invoices.list({ page: page.value, status: status.value }),
  { watch: [page, status] },
)

const totalPages = computed(() => countPages(data.value?.totalItems ?? 0))

// Changer de statut met à jour l'URL et revient à la page 1
const statusModel = computed({
  get: () => status.value ?? '',
  set: (value: string) => navigateTo({
    query: { ...route.query, status: value || undefined, page: undefined },
  }),
})
</script>

<template>
  <section>
    <h1>Factures</h1>

    <label for="status">Statut</label>
    <select
      id="status"
      v-model="statusModel"
    >
      <option value="">
        Tous
      </option>
      <option
        v-for="(label, value) in INVOICE_STATUS_LABELS"
        :key="value"
        :value="value"
      >
        {{ label }}
      </option>
    </select>

    <p v-if="loadState === 'pending'">
      Chargement…
    </p>
    <p
      v-else-if="error"
      role="alert"
    >
      Impossible de charger les factures.
      <button
        type="button"
        @click="refresh()"
      >
        Réessayer
      </button>
    </p>
    <p v-else-if="!data?.member.length">
      Aucune facture.
    </p>

    <template v-else>
      <table>
        <caption>{{ data.totalItems }} facture(s)</caption>
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
          >
            <td>{{ invoice.number }}</td>
            <td>{{ invoice.client.name }}</td>
            <td>{{ formatDate(invoice.dueAt) }}</td>
            <td>{{ formatCents(invoice.amountTtcCents) }}</td>
            <td><InvoiceStatusBadge :status="invoice.status" /></td>
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
