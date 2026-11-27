<script setup lang="ts">
import type { Invoice } from '#shared/types'
import { formatCents, formatDate } from '#shared/utils/format'
import { daysBetween } from '#shared/utils/penalties'
import { formatSiret } from '#shared/utils/siret'

definePageMeta({ layout: 'dashboard' })

const route = useRoute()
const clients = useClients()
const invoices = useInvoices()
const today = todayIso()
const id = Number(route.params.id)

const { data: client, error } = await useAsyncData(`client-${id}`, () => clients.get(id))

// Toutes les factures du client, les plus récentes d'abord (100 au maximum)
const { data: clientInvoices } = await useAsyncData(
  `client-${id}-invoices`,
  () => (client.value
    ? invoices.list({ 'client': client.value['@id'], 'itemsPerPage': 100, 'order[dueAt]': 'desc' })
    : Promise.resolve(null)),
  { watch: [client] },
)

useSeoMeta({ title: () => `${client.value?.name ?? 'Client'} | Recouvra`, robots: 'noindex' })

const list = computed(() => clientInvoices.value?.member ?? [])
const isLate = (invoice: Invoice) => invoice.status === 'overdue' || invoice.status === 'reminded'
const isOpen = (invoice: Invoice) => !['paid', 'written_off', 'draft'].includes(invoice.status)

// Totaux en TTC (les paiements partiels ne sont pas déduits dans cette V1)
const openCents = computed(() => list.value.filter(isOpen).reduce((sum, i) => sum + i.amountTtcCents, 0))
const lateCents = computed(() => list.value.filter(isLate).reduce((sum, i) => sum + i.amountTtcCents, 0))
const lateCount = computed(() => list.value.filter(isLate).length)
</script>

<template>
  <div class="fiche">
    <NuxtLink
      to="/dashboard/clients"
      class="fiche__back"
    >← Clients</NuxtLink>

    <UiEmptyState
      v-if="error || !client"
      title="Client introuvable"
      text="Il a peut-être été supprimé, ou il n’appartient pas à votre entreprise."
    >
      <UiButton to="/dashboard/clients">
        Retour aux clients
      </UiButton>
    </UiEmptyState>

    <template v-else>
      <header class="fiche__head">
        <h1 class="fiche__title">
          {{ client.name }}
        </h1>
        <div class="fiche__actions">
          <UiButton
            :to="`/dashboard/clients/${id}/modifier`"
            variant="outline"
          >
            Modifier
          </UiButton>
          <UiButton :to="{ path: '/dashboard/factures/nouvelle', query: { client: client['@id'] } }">
            Nouvelle facture
          </UiButton>
        </div>
      </header>

      <section
        aria-label="Indicateurs"
        class="kpis"
      >
        <UiCard class="kpi">
          <p class="kpi__label">
            Encours (TTC)
          </p>
          <p class="kpi__value">
            {{ formatCents(openCents) }}
          </p>
        </UiCard>
        <UiCard class="kpi">
          <p class="kpi__label">
            En retard (TTC)
          </p>
          <p
            class="kpi__value"
            :class="{ 'kpi__value--late': lateCents > 0 }"
          >
            {{ formatCents(lateCents) }}
          </p>
          <p class="kpi__hint">
            {{ lateCount }} facture(s)
          </p>
        </UiCard>
        <UiCard class="kpi">
          <p class="kpi__label">
            Factures
          </p>
          <p class="kpi__value">
            {{ clientInvoices?.totalItems ?? '—' }}
          </p>
        </UiCard>
      </section>

      <div class="fiche__grid">
        <UiCard aria-labelledby="coord-titre">
          <h2
            id="coord-titre"
            class="block__title"
          >
            Coordonnées
          </h2>
          <dl class="coords">
            <div>
              <dt>Email</dt>
              <dd><a :href="`mailto:${client.email}`">{{ client.email }}</a></dd>
            </div>
            <div>
              <dt>Téléphone</dt>
              <dd>
                <a
                  v-if="client.phone"
                  :href="`tel:${client.phone.replace(/\s/g, '')}`"
                >{{ client.phone }}</a>
                <span v-else>—</span>
              </dd>
            </div>
            <div>
              <dt>SIRET</dt>
              <dd>{{ client.siret ? formatSiret(client.siret) : '—' }}</dd>
            </div>
            <div>
              <dt>Adresse</dt>
              <dd class="coords__address">
                {{ client.address ?? '—' }}
              </dd>
            </div>
          </dl>
        </UiCard>

        <UiCard aria-labelledby="factures-titre">
          <h2
            id="factures-titre"
            class="block__title"
          >
            Factures
          </h2>
          <UiEmptyState
            v-if="!list.length"
            title="Aucune facture pour ce client"
          >
            <UiButton :to="{ path: '/dashboard/factures/nouvelle', query: { client: client['@id'] } }">
              Ajouter une facture
            </UiButton>
          </UiEmptyState>
          <ul
            v-else
            class="invoices"
          >
            <li
              v-for="invoice in list"
              :key="invoice.id"
            >
              <NuxtLink
                :to="`/dashboard/factures/${invoice.id}`"
                class="invoice"
              >
                <span class="invoice__main">
                  <strong>{{ invoice.number }}</strong>
                  <span>
                    échéance {{ formatDate(invoice.dueAt) }}<template v-if="isLate(invoice)"> · {{ daysBetween(invoice.dueAt, today) }} j de retard</template>
                  </span>
                </span>
                <span class="invoice__amount">{{ formatCents(invoice.amountTtcCents) }}</span>
                <InvoiceStatusBadge :status="invoice.status" />
              </NuxtLink>
            </li>
          </ul>
        </UiCard>
      </div>
    </template>
  </div>
</template>

<style scoped>
.fiche {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
  max-width: 1200px;
}

.fiche__back {
  align-self: flex-start;
  font-size: var(--text-sm);
  font-weight: 600;
  text-decoration: none;
}

.fiche__head {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: var(--space-4);
}

.fiche__title {
  font-size: var(--text-2xl);
}

.fiche__actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
}

.kpis {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: var(--space-3);
}

.kpi {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
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

.kpi__value--late {
  color: var(--color-warning);
}

.fiche__grid {
  display: grid;
  gap: var(--space-4);
  align-items: start;
}

.block__title {
  margin-bottom: var(--space-3);
  font-size: var(--text-lg);
}

.coords div {
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: var(--space-3) 0;
  border-bottom: 1px solid var(--color-line);
}

.coords dt {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

.coords dd {
  margin: 0;
  overflow-wrap: anywhere;
}

.coords__address {
  white-space: pre-line;
}

.invoices {
  margin: 0;
  padding: 0;
  list-style: none;
}

.invoice {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: var(--space-1) var(--space-3);
  align-items: center;
  padding: var(--space-3) 0;
  border-top: 1px solid var(--color-line);
  text-decoration: none;
}

.invoice:hover strong {
  text-decoration: underline;
}

.invoice__main {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.invoice__main span {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

.invoice__amount {
  text-align: right;
  font-variant-numeric: tabular-nums;
}

@media (min-width: 768px) {
  .invoice {
    grid-template-columns: 1fr 130px 110px;
  }
}

@media (min-width: 1100px) {
  .fiche__grid {
    grid-template-columns: 1fr 2fr;
  }
}
</style>
