<script setup lang="ts">
import type { PaymentMethod } from '#shared/types'
import { formatCents, formatDate, parseEurosToCents } from '#shared/utils/format'
import { idFromIri, paidPercent, PAYMENT_METHOD_LABELS } from '#shared/utils/payment'
import { computeLatePenalties, daysBetween } from '#shared/utils/penalties'
import { violationsByField } from '#shared/utils/violations'

definePageMeta({ layout: 'dashboard' })

const route = useRoute()
const invoices = useInvoices()
const payments = usePayments()
const today = todayIso()
const id = Number(route.params.id)

// ---------- Données ----------
// Une facture inexistante ou d'une autre entreprise renvoie une erreur : on affiche « introuvable »
const { data: invoice, error } = await useAsyncData(`invoice-${id}`, () => invoices.get(id))

const { data: paymentList, refresh: refreshPayments } = await useAsyncData(
  `invoice-${id}-payments`,
  () => (invoice.value ? payments.listForInvoice(invoice.value['@id']) : Promise.resolve(null)),
  { watch: [invoice] },
)

useSeoMeta({ title: () => `${invoice.value?.number ?? 'Facture'} | Recouvra`, robots: 'noindex' })

// ---------- Calculs ----------
const paidCents = computed(() => (paymentList.value?.member ?? []).reduce((sum, p) => sum + p.amountCents, 0))
const remainingCents = computed(() => Math.max(0, (invoice.value?.amountTtcCents ?? 0) - paidCents.value))
const percent = computed(() => paidPercent(paidCents.value, invoice.value?.amountTtcCents ?? 0))
const daysLate = computed(() => (invoice.value ? daysBetween(invoice.value.dueAt, today) : 0))

// Pénalités sur le reste dû, en supposant un client professionnel (L441-10)
const penalties = computed(() => {
  if (!invoice.value || remainingCents.value === 0 || daysLate.value <= 0) return null
  return computeLatePenalties({ amountCents: remainingCents.value, dueAt: invoice.value.dueAt, paidAt: today, clientType: 'professional' })
})

// ---------- Enregistrer un paiement ----------
const modalOpen = ref(false)
const success = ref('')
const form = reactive({ amount: '', paidAt: today, method: 'transfer' as PaymentMethod })
const errors = reactive({ amount: '', paidAt: '', method: '', form: '' })
const clearErrors = () => Object.assign(errors, { amount: '', paidAt: '', method: '', form: '' })
const sending = ref(false)

function openModal() {
  form.amount = (remainingCents.value / 100).toFixed(2).replace('.', ',') // proposé : le reste dû
  form.paidAt = today
  form.method = 'transfer'
  clearErrors()
  modalOpen.value = true
}

async function submitPayment() {
  clearErrors()
  const amountCents = parseEurosToCents(form.amount)

  // Contrôles rapides côté navigateur ; l'API refait tout (règle R11 comprise)
  if (amountCents === null || amountCents <= 0) errors.amount = 'Entrez un montant, par exemple 1 250,00.'
  else if (amountCents > remainingCents.value) errors.amount = `Le montant dépasse le reste dû (${formatCents(remainingCents.value)}).`
  if (!form.paidAt || form.paidAt > today) errors.paidAt = 'La date ne peut pas être dans le futur.'
  if (errors.amount || errors.paidAt || !invoice.value || amountCents === null) return

  sending.value = true
  try {
    await payments.create({ invoice: invoice.value['@id'], amountCents, paidAt: form.paidAt, method: form.method })
    modalOpen.value = false
    success.value = `Paiement de ${formatCents(amountCents)} enregistré.`
    await refreshPayments()
  }
  catch (e) {
    const fieldErrors = violationsByField(e)
    if (fieldErrors) {
      // Les champs de l'API (amountCents) n'ont pas tous le même nom que ceux du formulaire
      errors.amount = fieldErrors.amountCents ?? ''
      errors.paidAt = fieldErrors.paidAt ?? ''
      errors.method = fieldErrors.method ?? ''
      errors.form = fieldErrors[''] ?? ''
    }
    else {
      errors.form = 'Le paiement n’a pas pu être enregistré. Réessayez dans un instant.'
    }
  }
  finally {
    sending.value = false
  }
}
</script>

<template>
  <div class="detail">
    <NuxtLink
      to="/dashboard/factures"
      class="detail__back"
    >← Factures</NuxtLink>

    <UiEmptyState
      v-if="error || !invoice"
      title="Facture introuvable"
      text="Elle a peut-être été supprimée, ou elle n’appartient pas à votre entreprise."
    >
      <UiButton to="/dashboard/factures">
        Retour aux factures
      </UiButton>
    </UiEmptyState>

    <template v-else>
      <header class="detail__head">
        <div>
          <div class="detail__title-row">
            <h1 class="detail__title">
              {{ invoice.number }}
            </h1>
            <InvoiceStatusBadge :status="invoice.status" />
          </div>
          <p class="detail__client">
            <NuxtLink :to="`/dashboard/clients/${idFromIri(invoice.client['@id'])}`">
              {{ invoice.client.name }}
            </NuxtLink>
          </p>
        </div>
        <UiButton
          v-if="remainingCents > 0"
          @click="openModal"
        >
          Enregistrer un paiement
        </UiButton>
      </header>

      <UiAlert
        v-if="success"
        tone="success"
      >
        {{ success }}
      </UiAlert>

      <div class="detail__grid">
        <UiCard aria-labelledby="montants-titre">
          <h2
            id="montants-titre"
            class="block__title"
          >
            Montants
          </h2>
          <dl class="amounts">
            <div>
              <dt>Montant HT</dt>
              <dd>{{ formatCents(invoice.amountHtCents) }}</dd>
            </div>
            <div>
              <dt>Montant TTC</dt>
              <dd>{{ formatCents(invoice.amountTtcCents) }}</dd>
            </div>
            <div>
              <dt>Déjà payé</dt>
              <dd>{{ formatCents(paidCents) }}</dd>
            </div>
            <div class="amounts__remaining">
              <dt>Reste dû</dt>
              <dd>{{ formatCents(remainingCents) }}</dd>
            </div>
          </dl>
          <div
            class="progress"
            role="progressbar"
            :aria-valuenow="percent"
            aria-valuemin="0"
            aria-valuemax="100"
            :aria-label="`Payé à ${percent} %`"
          >
            <span :style="{ width: `${percent}%` }" />
          </div>
          <p class="block__muted">
            Payé à {{ percent }} %
          </p>
        </UiCard>

        <UiCard aria-labelledby="dates-titre">
          <h2
            id="dates-titre"
            class="block__title"
          >
            Dates
          </h2>
          <dl class="amounts">
            <div>
              <dt>Émise le</dt>
              <dd>{{ formatDate(invoice.issuedAt) }}</dd>
            </div>
            <div>
              <dt>Échéance</dt>
              <dd>{{ formatDate(invoice.dueAt) }}</dd>
            </div>
            <div v-if="remainingCents > 0 && daysLate > 0">
              <dt>Retard</dt>
              <dd class="late">
                {{ daysLate }} jours
              </dd>
            </div>
          </dl>
          <template v-if="penalties">
            <h3 class="block__subtitle">
              Pénalités à ce jour
            </h3>
            <dl class="amounts">
              <div>
                <dt>Pénalités ({{ penalties.rate.toLocaleString('fr-FR') }} %)</dt>
                <dd>{{ formatCents(penalties.penaltyCents) }}</dd>
              </div>
              <div>
                <dt>Indemnité forfaitaire</dt>
                <dd>{{ formatCents(penalties.indemnityCents) }}</dd>
              </div>
              <div class="amounts__remaining">
                <dt>Total exigible</dt>
                <dd>{{ formatCents(penalties.totalCents) }}</dd>
              </div>
            </dl>
            <p class="block__muted">
              Calcul pour un client professionnel (art. L441-10 et D441-5).
            </p>
          </template>
        </UiCard>
      </div>

      <UiCard aria-labelledby="paiements-titre">
        <h2
          id="paiements-titre"
          class="block__title"
        >
          Paiements
        </h2>
        <p
          v-if="!paymentList?.member.length"
          class="block__muted"
        >
          Aucun paiement enregistré.
        </p>
        <ul
          v-else
          class="payments"
        >
          <li
            v-for="payment in paymentList.member"
            :key="payment.id"
          >
            <span>{{ formatDate(payment.paidAt) }} · {{ PAYMENT_METHOD_LABELS[payment.method] }}</span>
            <strong>{{ formatCents(payment.amountCents) }}</strong>
          </li>
        </ul>
      </UiCard>

      <UiCard aria-labelledby="relances-titre">
        <h2
          id="relances-titre"
          class="block__title"
        >
          Relances
        </h2>
        <UiEmptyState
          title="Arrive avec le module de relances"
          text="Vous verrez ici les relances envoyées et programmées pour cette facture."
        />
      </UiCard>

      <UiModal
        v-model:open="modalOpen"
        title="Enregistrer un paiement"
      >
        <form
          class="pay-form"
          novalidate
          @submit.prevent="submitPayment"
        >
          <p class="block__muted">
            Reste dû : {{ formatCents(remainingCents) }}
          </p>
          <UiField
            v-slot="{ id: fieldId, describedby, invalid }"
            label="Montant reçu (€)"
            :error="errors.amount"
          >
            <input
              :id="fieldId"
              v-model="form.amount"
              inputmode="decimal"
              :aria-describedby="describedby"
              :aria-invalid="invalid"
            >
          </UiField>
          <UiField
            v-slot="{ id: fieldId, describedby, invalid }"
            label="Date du paiement"
            :error="errors.paidAt"
          >
            <input
              :id="fieldId"
              v-model="form.paidAt"
              type="date"
              :max="today"
              :aria-describedby="describedby"
              :aria-invalid="invalid"
            >
          </UiField>
          <UiField
            v-slot="{ id: fieldId, describedby, invalid }"
            label="Moyen de paiement"
            :error="errors.method"
          >
            <select
              :id="fieldId"
              v-model="form.method"
              :aria-describedby="describedby"
              :aria-invalid="invalid"
            >
              <option
                v-for="(label, value) in PAYMENT_METHOD_LABELS"
                :key="value"
                :value="value"
              >
                {{ label }}
              </option>
            </select>
          </UiField>
          <UiAlert
            v-if="errors.form"
            tone="error"
          >
            {{ errors.form }}
          </UiAlert>
          <UiButton
            type="submit"
            block
            :loading="sending"
          >
            Enregistrer
          </UiButton>
        </form>
      </UiModal>
    </template>
  </div>
</template>

<style scoped>
.detail {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
  max-width: 1100px;
}

.detail__back {
  align-self: flex-start;
  font-size: var(--text-sm);
  font-weight: 600;
  text-decoration: none;
}

.detail__head {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: flex-end;
  gap: var(--space-4);
}

.detail__title-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-3);
}

.detail__title {
  font-size: var(--text-2xl);
}

.detail__client {
  margin-top: var(--space-1);
  font-weight: 600;
}

.detail__grid {
  display: grid;
  gap: var(--space-4);
}

.block__title {
  margin-bottom: var(--space-3);
  font-size: var(--text-lg);
}

.block__subtitle {
  margin: var(--space-5) 0 var(--space-2);
  font-size: var(--text-base);
}

.block__muted {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

.amounts div {
  display: flex;
  justify-content: space-between;
  gap: var(--space-3);
  padding: var(--space-2) 0;
  border-bottom: 1px solid var(--color-line);
}

.amounts dt {
  color: var(--color-muted);
}

.amounts dd {
  margin: 0;
  font-variant-numeric: tabular-nums;
}

.amounts__remaining dt,
.amounts__remaining dd {
  font-weight: 700;
  color: var(--color-text);
}

.late {
  font-weight: 700;
  color: var(--color-warning);
}

.progress {
  height: 8px;
  margin: var(--space-4) 0 var(--space-2);
  overflow: hidden;
  border-radius: 4px;
  background: var(--color-line);
}

.progress span {
  display: block;
  height: 100%;
  background: var(--color-success);
  transition: width 300ms;
}

.payments {
  margin: 0;
  padding: 0;
  list-style: none;
}

.payments li {
  display: flex;
  justify-content: space-between;
  gap: var(--space-3);
  padding: var(--space-3) 0;
  border-bottom: 1px solid var(--color-line);
  font-variant-numeric: tabular-nums;
}

.pay-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

@media (min-width: 900px) {
  .detail__grid {
    grid-template-columns: 1fr 1fr;
  }
}
</style>
