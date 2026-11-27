<script setup lang="ts">
import type { FaqItem } from '~/components/landing/Faq.vue'
import { PENALTY_RATES } from '#shared/utils/penalties'

const percent = (rate: number) => `${rate.toLocaleString('fr-FR', { minimumFractionDigits: 2 })} %`

/** Historique affiché dans le tableau. Ajouter une ligne à chaque semestre. */
const history = [
  { period: '2d semestre 2026', ecb: 2.4, legal: 2.75 },
  { period: '1er semestre 2026', ecb: 2.15, legal: 2.62 },
  { period: '2d semestre 2025', ecb: 2.15, legal: 2.76 },
].map(row => ({ ...row, defaultRate: Math.round((row.ecb + 10) * 100) / 100, minimumRate: Math.round(row.legal * 3 * 100) / 100 }))

const claims = [
  { title: 'Les pénalités de retard', text: `Au taux de vos conditions générales, ou à défaut ${percent(PENALTY_RATES.defaultRate)} par an, calculées jour par jour sur le reste dû TTC.` },
  { title: 'L’indemnité de 40 €', text: 'Due de plein droit par facture payée en retard par un professionnel, sans rappel préalable. Pas pour un particulier.' },
  { title: 'Les frais réels', text: 'Si vos frais de recouvrement dépassent 40 € (huissier, avocat), vous pouvez demander une indemnité complémentaire, sur justificatifs.' },
]

const faq: FaqItem[] = [
  { question: 'Faut-il envoyer une relance avant de réclamer les pénalités ?', answer: 'Non. Entre professionnels, les pénalités et l’indemnité de 40 € sont dues dès le lendemain de l’échéance, sans rappel. Avec un particulier, les intérêts ne courent qu’à partir de la mise en demeure.' },
  { question: 'Quel taux appliquer si rien n’est écrit sur ma facture ?', answer: `Le taux par défaut : taux de la BCE + 10 points, soit ${percent(PENALTY_RATES.defaultRate)} au ${PENALTY_RATES.period}. Les mentions de pénalités restent obligatoires sur vos factures.` },
  { question: 'Puis-je fixer un taux plus bas dans mes conditions ?', answer: `Oui, mais pas en dessous de trois fois le taux de l’intérêt légal, soit ${percent(PENALTY_RATES.minimumRate)} au ${PENALTY_RATES.period}.` },
  { question: 'Les pénalités sont-elles soumises à la TVA ?', answer: 'Non. Les pénalités de retard et l’indemnité forfaitaire sont des dommages et intérêts : elles ne sont pas soumises à la TVA.' },
  { question: 'Le calcul est-il juridiquement garanti ?', answer: 'Le calcul est indicatif. Il applique les règles du Code de commerce (art. L441-10 et D441-5) aux dates que vous saisissez. Pour un litige, faites-vous conseiller.' },
]

const breadcrumb = [
  { label: 'Accueil', to: '/' },
  { label: 'Calcul des pénalités de retard' },
]

usePublicPage({
  path: '/outils/calcul-penalites-retard',
  title: `Calcul des pénalités de retard de paiement (${PENALTY_RATES.period}) | Recouvra`,
  description: `Calculez gratuitement les pénalités de retard et l’indemnité de 40 € d’une facture impayée. Taux à jour : ${percent(PENALTY_RATES.defaultRate)} par défaut au ${PENALTY_RATES.period}.`,
  breadcrumb,
  schemas: [
    {
      '@type': 'WebApplication',
      'name': 'Calculateur de pénalités de retard',
      'applicationCategory': 'BusinessApplication',
      'operatingSystem': 'Web',
      'isAccessibleForFree': true,
      'offers': { '@type': 'Offer', 'price': '0', 'priceCurrency': 'EUR' },
    },
    faqSchema(faq),
  ],
})
</script>

<template>
  <div>
    <PublicPageHero
      eyebrow="Outil gratuit"
      title="Calcul des pénalités de retard de paiement"
      :lead="`Montant, échéance, date de paiement : obtenez les pénalités et l’indemnité dues, avec les taux du ${PENALTY_RATES.period}.`"
      :breadcrumb="breadcrumb"
    />

    <section
      class="section section--paper"
      aria-label="Calculateur"
    >
      <div class="container">
        <PenaltyCalculator />
      </div>
    </section>

    <section
      class="section"
      aria-labelledby="methode-titre"
    >
      <div class="container method">
        <div class="method__text">
          <span class="eyebrow">Méthode</span>
          <h2
            id="methode-titre"
            class="h2"
          >
            Comment le calcul est fait
          </h2>
          <p class="formula">
            Reste dû TTC × taux annuel × jours de retard ÷ 365
          </p>
          <p>
            Les jours sont comptés du lendemain de l’échéance jusqu’au paiement.
            Le taux est celui de vos conditions générales ou, à défaut, le taux de la BCE majoré de 10 points.
            Il ne peut pas être inférieur à trois fois le taux de l’intérêt légal.
          </p>
        </div>

        <div
          class="table-wrap"
          role="region"
          aria-labelledby="taux-titre"
          tabindex="0"
        >
          <table class="rates">
            <caption id="taux-titre">
              Taux applicables par semestre
            </caption>
            <thead>
              <tr>
                <th scope="col">
                  Période
                </th>
                <th scope="col">
                  Taux BCE
                </th>
                <th scope="col">
                  Taux par défaut
                </th>
                <th scope="col">
                  Intérêt légal
                </th>
                <th scope="col">
                  Minimum
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="row in history"
                :key="row.period"
              >
                <th scope="row">
                  {{ row.period }}
                </th>
                <td>{{ percent(row.ecb) }}</td>
                <td><strong>{{ percent(row.defaultRate) }}</strong></td>
                <td>{{ percent(row.legal) }}</td>
                <td>{{ percent(row.minimumRate) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <section
      class="section section--paper"
      aria-labelledby="reclamer-titre"
    >
      <div class="container">
        <LandingSectionHead
          eyebrow="Vos droits"
          title="Ce que vous pouvez réclamer"
          title-id="reclamer-titre"
        />
        <ul class="claims">
          <li
            v-for="claim in claims"
            :key="claim.title"
            class="claims__item"
          >
            <h3>{{ claim.title }}</h3>
            <p>{{ claim.text }}</p>
          </li>
        </ul>
      </div>
    </section>

    <LandingFaq :items="faq" />

    <PublicCtaBand
      title="Arrêtez de calculer à la main"
      text="Recouvra calcule les pénalités de chaque facture en retard et les ajoute à vos relances."
      secondary-label="Voir les tarifs"
      secondary-to="/tarifs"
    />
  </div>
</template>

<style scoped>
.method {
  display: grid;
  gap: var(--space-10);
  align-items: start;
}

.method__text {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  max-width: 620px;
}

.formula {
  padding: var(--space-4) var(--space-5);
  border-left: 4px solid var(--color-yellow);
  background: var(--color-paper);
  font-weight: 700;
}

.table-wrap {
  overflow-x: auto;
}

.rates {
  width: 100%;
  min-width: 560px;
  border-collapse: collapse;
  font-variant-numeric: tabular-nums;
}

.rates caption {
  padding-bottom: var(--space-3);
  font-weight: 700;
  text-align: left;
}

.rates th,
.rates td {
  padding: var(--space-3) var(--space-4);
  border-bottom: 1px solid var(--color-line);
  text-align: left;
}

.rates thead th {
  font-size: var(--text-sm);
  color: var(--color-muted);
}

.claims {
  display: grid;
  gap: var(--space-5);
  margin: var(--space-8) 0 0;
  padding: 0;
  list-style: none;
}

.claims__item {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  padding: var(--space-6);
  border: 1px solid var(--color-line);
  border-radius: var(--radius-md);
  background: var(--color-white);
}

.claims__item h3 {
  font-size: var(--text-lg);
}

@media (min-width: 1024px) {
  .method {
    grid-template-columns: 1fr 1.2fr;
  }

  .claims {
    grid-template-columns: repeat(3, 1fr);
  }
}
</style>
