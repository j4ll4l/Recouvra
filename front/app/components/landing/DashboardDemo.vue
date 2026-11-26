<script setup lang="ts">
// Démo interactive avec des données d'exemple : « Encaisser » une facture fait revenir la bulle au centre.
const invoices = ref([
  { id: 'a', client: 'SARL Morel Bâtiment', meta: 'F-2026-118 · échue depuis 41 j', cents: 720_000, late: true, paid: false },
  { id: 'b', client: 'Dupont Rénovation', meta: 'F-2026-121 · échue depuis 33 j', cents: 548_000, late: true, paid: false },
  { id: 'c', client: 'Maçonnerie Bernard', meta: 'F-2026-132 · échéance 5 déc.', cents: 1_176_000, late: false, paid: false },
  { id: 'd', client: 'Électricité Rhône', meta: 'F-2026-130 · échéance 28 nov.', cents: 294_000, late: false, paid: false },
  { id: 'e', client: 'Charpentes Lambert', meta: 'F-2026-124 · échue depuis 17 j', cents: 27_000, late: true, paid: false },
])

const open = computed(() => invoices.value.filter(i => !i.paid))
const totalCents = computed(() => open.value.reduce((sum, i) => sum + i.cents, 0))
const lateCents = computed(() => open.value.filter(i => i.late).reduce((sum, i) => sum + i.cents, 0))
const lateShare = computed(() => (totalCents.value ? lateCents.value / totalCents.value : 0))
const levelLabel = computed(() => (lateShare.value > 0.05
  ? `${Math.round(lateShare.value * 100)} % de l’encours en retard`
  : 'Aucun retard : d’aplomb'))

const euros = (cents: number) => formatCents(cents).replace(/,00\s€$/, ' €')
</script>

<template>
  <section
    id="tableau"
    class="section theme-dark"
    aria-labelledby="tab-titre"
  >
    <div class="container demo">
      <div class="demo__text">
        <span class="eyebrow">Tableau de bord</span>
        <h2
          id="tab-titre"
          class="h2"
        >
          Qui vous doit quoi, en un coup d’œil.
        </h2>
        <p class="lead">
          Chaque facture, son retard et sa prochaine relance. Encaissez-en une : la bulle revient au centre.
        </p>
        <ul class="demo__checks">
          <li>Sur ordinateur et sur téléphone</li>
          <li>Relances suspendues dès le paiement</li>
          <li>Export pour votre expert-comptable</li>
        </ul>
      </div>

      <div class="panel">
        <div class="panel__top">
          <span>Factures clients · novembre 2026</span>
          <span class="panel__tag">Données d’exemple</span>
        </div>
        <dl class="panel__kpis">
          <div>
            <dt>Reste à encaisser</dt>
            <dd>{{ euros(totalCents) }}</dd>
          </div>
          <div>
            <dt>Dont en retard</dt>
            <dd>{{ euros(lateCents) }}</dd>
          </div>
          <div>
            <dt>Relances programmées</dt>
            <dd>{{ open.length }}</dd>
          </div>
        </dl>

        <div class="level">
          <span
            class="level__label"
            aria-live="polite"
          >{{ levelLabel }}</span>
          <div
            class="level__tube"
            aria-hidden="true"
          >
            <span class="level__mark level__mark--left" />
            <span class="level__mark level__mark--right" />
            <span
              class="level__bubble"
              :class="{ 'level__bubble--ok': lateShare <= 0.05 }"
              :style="{ transform: `translateX(${Math.round(lateShare * 96)}px)` }"
            />
          </div>
        </div>

        <ul class="rows">
          <li
            v-for="invoice in invoices"
            :key="invoice.id"
            class="row"
          >
            <span class="row__client">
              <strong>{{ invoice.client }}</strong>
              <span>{{ invoice.meta }}</span>
            </span>
            <span class="row__amount">{{ formatCents(invoice.cents) }}</span>
            <span
              class="row__status"
              :data-state="invoice.paid ? 'paid' : invoice.late ? 'late' : 'due'"
            >{{ invoice.paid ? 'Payée' : invoice.late ? 'En retard' : 'À échoir' }}</span>
            <button
              v-if="!invoice.paid"
              type="button"
              class="row__pay"
              @click="invoice.paid = true"
            >
              Encaisser<span class="sr-only"> la facture de {{ invoice.client }}</span>
            </button>
            <span
              v-else
              class="row__done"
            >Réglée</span>
          </li>
        </ul>
      </div>
    </div>
  </section>
</template>

<style scoped>
.demo {
  display: grid;
  gap: var(--space-10);
  align-items: center;
}

.demo__text {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
}

.demo__checks {
  margin: 0;
  padding: 0;
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.demo__checks li::before {
  content: '✓ ';
  font-weight: 700;
  color: var(--color-yellow);
}

.panel {
  min-width: 0;
  padding: var(--space-5);
  border: 1px solid var(--color-ink-line);
  border-radius: var(--radius-md);
  background: var(--color-ink-soft);
}

.panel__top {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: var(--space-2);
  font-size: var(--text-sm);
  color: var(--color-muted-on-dark);
}

.panel__tag {
  padding: 2px 8px;
  border: 1px solid var(--color-ink-line);
  border-radius: 3px;
  font-size: var(--text-xs);
}

.panel__kpis {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--space-3);
  margin: var(--space-5) 0;
}

.panel__kpis dt {
  font-size: var(--text-xs);
  color: var(--color-muted-on-dark);
}

.panel__kpis dd {
  margin: 0;
  font-size: var(--text-xl);
  font-weight: 700;
  font-variant-numeric: tabular-nums;
}

.level {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-3);
  padding: var(--space-3) 0;
  border-block: 1px solid var(--color-ink-line);
}

.level__label {
  font-size: var(--text-sm);
  font-weight: 600;
}

.level__tube {
  position: relative;
  width: 200px;
  height: 32px;
  border: 2px solid var(--color-ink-line);
  border-radius: 16px;
}

.level__mark {
  position: absolute;
  top: 4px;
  width: 2px;
  height: 20px;
  background: var(--color-muted-on-dark);
}

.level__mark--left {
  left: 72px;
}

.level__mark--right {
  right: 72px;
}

.level__bubble {
  position: absolute;
  top: 3px;
  left: calc(50% - 22px);
  width: 44px;
  height: 22px;
  border-radius: 11px;
  background: var(--color-yellow);
  transition: transform 400ms ease;
}

.level__bubble--ok {
  background: var(--color-success-on-dark);
}

.rows {
  margin: 0;
  padding: 0;
  list-style: none;
}

.row {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: var(--space-2) var(--space-3);
  align-items: center;
  padding: var(--space-3) 0;
  border-bottom: 1px solid var(--color-ink-line);
}

.row__client {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.row__client span {
  font-size: var(--text-xs);
  color: var(--color-muted-on-dark);
}

.row__amount {
  text-align: right;
  font-variant-numeric: tabular-nums;
}

.row__status {
  justify-self: start;
  padding: 4px 10px;
  border-radius: 3px;
  font-size: var(--text-xs);
  font-weight: 600;
  background: var(--color-ink-line);
}

.row__status[data-state='late'] {
  background: var(--color-warning-bg-dark);
  color: var(--color-yellow);
}

.row__status[data-state='paid'] {
  background: var(--color-success-bg-dark);
  color: var(--color-success-on-dark);
}

.row__pay {
  justify-self: end;
  min-height: 36px;
  padding: 0 var(--space-3);
  border: 1px solid var(--color-yellow);
  border-radius: var(--radius-sm);
  background: transparent;
  font-size: var(--text-sm);
  font-weight: 600;
  color: var(--color-yellow);
}

.row__pay:hover {
  background: var(--color-yellow);
  color: var(--color-ink);
}

.row__done {
  justify-self: end;
  font-size: var(--text-sm);
  color: var(--color-success-on-dark);
}

@media (min-width: 1024px) {
  .demo {
    grid-template-columns: 2fr 3fr;
    gap: var(--space-16);
  }

  .panel {
    padding: var(--space-6);
  }

  .row {
    grid-template-columns: 1fr 120px 100px 100px;
  }

  .row__status {
    justify-self: center;
  }
}
</style>
