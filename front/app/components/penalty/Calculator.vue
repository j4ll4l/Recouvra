<script setup lang="ts">
import type { ClientType } from '#shared/utils/penalties'
import { computeLatePenalties, PENALTY_RATES } from '#shared/utils/penalties'
import { formatCents, parseEurosToCents } from '#shared/utils/format'
import { todayIso } from '#shared/utils/date'

const clientType = ref<ClientType>('professional')
const amount = ref('7 200,00')
const dueAt = ref('2026-10-13')
const paidAt = ref(todayIso())
const useContractRate = ref(false)
const contractRate = ref('10,00')
const formalNoticeAt = ref('')

const amountCents = computed(() => parseEurosToCents(amount.value))
const amountError = computed(() => (amountCents.value === null || amountCents.value <= 0 ? 'Entrez un montant, par exemple 1 250,00.' : ''))

const result = computed(() => {
  const rate = Number(contractRate.value.replace(',', '.'))
  return computeLatePenalties({
    amountCents: amountCents.value ?? 0,
    dueAt: dueAt.value,
    paidAt: paidAt.value,
    clientType: clientType.value,
    contractRate: useContractRate.value && rate > 0 ? rate : undefined,
    formalNoticeAt: formalNoticeAt.value || undefined,
  })
})

const percent = (rate: number) => `${rate.toLocaleString('fr-FR', { minimumFractionDigits: 2 })} %`
const clientTypes: { value: ClientType, label: string }[] = [
  { value: 'professional', label: 'Un professionnel' },
  { value: 'consumer', label: 'Un particulier' },
]
</script>

<template>
  <form
    class="calc"
    aria-label="Calcul des pénalités de retard"
    @submit.prevent
  >
    <div class="calc__inputs">
      <fieldset class="calc__choice">
        <legend>Votre client est</legend>
        <label
          v-for="type in clientTypes"
          :key="type.value"
        >
          <input
            v-model="clientType"
            type="radio"
            name="client-type"
            :value="type.value"
          >
          {{ type.label }}
        </label>
      </fieldset>

      <UiField
        v-slot="{ id, describedby, invalid }"
        label="Montant TTC restant dû (€)"
        :error="amountError"
      >
        <input
          :id="id"
          v-model="amount"
          inputmode="decimal"
          :aria-describedby="describedby"
          :aria-invalid="invalid"
        >
      </UiField>

      <div class="calc__row">
        <UiField
          v-slot="{ id }"
          label="Date d’échéance"
        >
          <input
            :id="id"
            v-model="dueAt"
            type="date"
          >
        </UiField>
        <UiField
          v-slot="{ id }"
          label="Date du paiement ou du calcul"
        >
          <input
            :id="id"
            v-model="paidAt"
            type="date"
          >
        </UiField>
      </div>

      <template v-if="clientType === 'professional'">
        <label class="calc__check">
          <input
            v-model="useContractRate"
            type="checkbox"
          >
          Mes conditions générales prévoient un autre taux que {{ percent(PENALTY_RATES.defaultRate) }}
        </label>
        <UiField
          v-if="useContractRate"
          v-slot="{ id, describedby }"
          label="Taux de mes conditions générales (%)"
          :hint="`Minimum légal : ${percent(PENALTY_RATES.minimumRate)} (${PENALTY_RATES.period})`"
        >
          <input
            :id="id"
            v-model="contractRate"
            inputmode="decimal"
            :aria-describedby="describedby"
          >
        </UiField>
        <UiAlert
          v-if="result.belowMinimum"
          tone="warning"
        >
          Ce taux est sous le minimum légal : le calcul utilise {{ percent(PENALTY_RATES.minimumRate) }}.
        </UiAlert>
      </template>

      <UiField
        v-else
        v-slot="{ id, describedby }"
        label="Date de la mise en demeure"
        hint="Avec un particulier, les intérêts courent à partir de la mise en demeure. Pas d’indemnité de 40 €."
      >
        <input
          :id="id"
          v-model="formalNoticeAt"
          type="date"
          :aria-describedby="describedby"
        >
      </UiField>
    </div>

    <div
      class="calc__result"
      aria-live="polite"
    >
      <h2 class="calc__title">
        Ce que votre client vous doit
      </h2>
      <dl class="calc__lines">
        <div>
          <dt>Jours de retard</dt>
          <dd>{{ result.days }}</dd>
        </div>
        <div>
          <dt>Principal</dt>
          <dd>{{ formatCents(amountCents ?? 0) }}</dd>
        </div>
        <div>
          <dt>{{ clientType === 'professional' ? 'Pénalités' : 'Intérêts' }} ({{ percent(result.rate) }})</dt>
          <dd>{{ formatCents(result.penaltyCents) }}</dd>
        </div>
        <div>
          <dt>Indemnité forfaitaire</dt>
          <dd>{{ clientType === 'professional' ? formatCents(result.indemnityCents) : 'Non due' }}</dd>
        </div>
      </dl>
      <p class="calc__total">
        <span>Total exigible</span>
        <strong>{{ formatCents(result.totalCents) }}</strong>
      </p>
    </div>
  </form>
</template>

<style scoped>
.calc {
  display: flex;
  flex-wrap: wrap;
  overflow: hidden;
  border: 1px solid var(--color-line);
  border-radius: var(--radius-md);
  background: var(--color-white);
  color: var(--color-text);
}

.calc__inputs {
  flex: 3 1 420px;
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
  padding: var(--space-6);
}

.calc__choice {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-4);
  margin: 0;
  padding: 0;
  border: 0;
}

.calc__choice legend {
  margin-bottom: var(--space-2);
  font-size: var(--text-sm);
  font-weight: 600;
}

.calc__choice label,
.calc__check {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  min-height: var(--tap-min);
}

.calc__row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: var(--space-4);
}

.calc__result {
  flex: 2 1 300px;
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  padding: var(--space-6);
  background: var(--color-paper);
}

.calc__title {
  font-size: var(--text-xl);
}

.calc__lines div {
  display: flex;
  justify-content: space-between;
  gap: var(--space-3);
  padding: var(--space-3) 0;
  border-bottom: 1px solid var(--color-line-strong);
}

.calc__lines dt {
  color: var(--color-muted);
}

.calc__lines dd {
  margin: 0;
  font-variant-numeric: tabular-nums;
}

.calc__total {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: var(--space-3);
  padding: var(--space-4);
  border-radius: var(--radius-sm);
  background: var(--color-ink);
  color: var(--color-white);
}

.calc__total strong {
  font-size: var(--text-xl);
  color: var(--color-yellow);
  font-variant-numeric: tabular-nums;
}
</style>
