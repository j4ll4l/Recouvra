<script setup lang="ts">
import type { FaqItem } from '~/components/landing/Faq.vue'

type Billing = 'monthly' | 'yearly'
const billing = ref<Billing>('monthly')

const plans: Record<Billing, { label: string, price: string, unit: string, note: string }> = {
  monthly: { label: 'Mensuel', price: '29 €', unit: 'HT / mois', note: 'Sans engagement, résiliable à tout moment. CB ou prélèvement SEPA.' },
  yearly: { label: 'Annuel', price: '290 €', unit: 'HT / an', note: '2 mois offerts. CB, prélèvement SEPA ou virement.' },
}

const included = [
  'Factures et clients illimités',
  'Jusqu’à 5 utilisateurs',
  'Scénarios de relance automatiques',
  'Calcul des pénalités et de l’indemnité de 40 €',
  'Modèles de relance et de mise en demeure',
  'Tableau de bord des retards',
]

const trial = [
  { title: 'Créez votre compte', text: 'En deux minutes, avec votre SIRET.' },
  { title: 'Ajoutez vos factures', text: 'Saisie rapide ou import de vos factures en retard.' },
  { title: 'Décidez au bout de 14 jours', text: 'Vous continuez seulement si Recouvra vous fait gagner du temps.' },
]

const comparison = [
  { label: 'Coût', manual: 'Votre temps, le soir', recouvra: '29 € HT / mois', agency: '10 à 30 % des sommes récupérées' },
  { label: 'Relances envoyées à temps', manual: 'Quand vous y pensez', recouvra: 'Oui, automatiquement', agency: 'Oui' },
  { label: 'Pénalités calculées', manual: 'Rarement', recouvra: 'Oui, sur chaque facture', agency: 'Oui' },
  { label: 'Relation client', manual: 'Gardée', recouvra: 'Gardée, avec votre ton', agency: 'Confiée à un tiers' },
  { label: 'Quand l’utiliser', manual: 'Quelques factures par an', recouvra: 'Dès la première facture en retard', agency: 'Après une mise en demeure restée sans réponse' },
]

const faq: FaqItem[] = [
  { question: 'Le prix dépend-il du nombre de factures ?', answer: 'Non. Le prix est le même pour toute l’entreprise, quel que soit le nombre de factures et de clients.' },
  { question: 'Puis-je changer de formule ?', answer: 'Oui. Vous pouvez passer du mensuel à l’annuel à tout moment ; le changement prend effet à la fin de la période en cours.' },
  { question: 'Comment résilier ?', answer: 'Depuis vos paramètres, en un clic. La formule mensuelle s’arrête à la fin du mois payé.' },
  { question: 'Recevrai-je une facture ?', answer: 'Oui, une facture conforme est disponible chaque mois (ou chaque année) dans votre espace.' },
]

const breadcrumb = [
  { label: 'Accueil', to: '/' },
  { label: 'Tarifs' },
]

const siteUrl = useRuntimeConfig().public.siteUrl
const offer = (price: string, unitCode: 'MON' | 'ANN') => ({
  '@type': 'Offer',
  'price': price,
  'priceCurrency': 'EUR',
  'url': `${siteUrl}/tarifs`,
  'priceSpecification': {
    '@type': 'UnitPriceSpecification',
    'price': price,
    'priceCurrency': 'EUR',
    'unitCode': unitCode,
    'valueAddedTaxIncluded': false,
  },
})

usePublicPage({
  path: '/tarifs',
  title: 'Tarifs : 29 € HT par mois pour toute l’entreprise | Recouvra',
  description: 'Recouvra coûte 29 € HT par mois ou 290 € HT par an, pour jusqu’à 5 utilisateurs. Essai gratuit de 14 jours, sans engagement.',
  breadcrumb,
  schemas: [
    {
      '@type': 'Product',
      'name': 'Recouvra',
      'description': 'Logiciel de relance des factures impayées pour les artisans du bâtiment.',
      'brand': { '@type': 'Brand', 'name': 'Recouvra' },
      'offers': [offer('29.00', 'MON'), offer('290.00', 'ANN')],
    },
    faqSchema(faq),
  ],
})
</script>

<template>
  <div>
    <PublicPageHero
      eyebrow="Tarifs"
      title="Un prix simple, pour toute l’entreprise"
      lead="Pas de commission sur les sommes récupérées, pas de supplément par facture."
      :breadcrumb="breadcrumb"
    >
      <div class="plan">
        <div
          class="plan__toggle"
          role="group"
          aria-label="Facturation"
        >
          <button
            v-for="(plan, key) in plans"
            :key="key"
            type="button"
            class="plan__option"
            :aria-pressed="billing === key"
            @click="billing = key"
          >
            {{ plan.label }}
          </button>
        </div>
        <p
          class="plan__price"
          aria-live="polite"
        >
          <strong>{{ plans[billing].price }}</strong>
          <span>{{ plans[billing].unit }}</span>
        </p>
        <p class="plan__note">
          {{ plans[billing].note }}
        </p>
        <ul class="plan__list">
          <li
            v-for="item in included"
            :key="item"
          >
            {{ item }}
          </li>
        </ul>
        <UiButton
          to="/login"
          size="lg"
        >
          Essayer 14 jours gratuitement
        </UiButton>
      </div>
    </PublicPageHero>

    <section
      class="section section--paper"
      aria-labelledby="essai-titre"
    >
      <div class="container">
        <LandingSectionHead
          eyebrow="Essai gratuit"
          title="14 jours pour juger sur pièces"
          title-id="essai-titre"
        />
        <ol class="trial">
          <li
            v-for="(step, index) in trial"
            :key="step.title"
            class="trial__step"
          >
            <span
              class="trial__number"
              aria-hidden="true"
            >{{ index + 1 }}</span>
            <h3>{{ step.title }}</h3>
            <p>{{ step.text }}</p>
          </li>
        </ol>
      </div>
    </section>

    <section
      class="section"
      aria-labelledby="comparaison-titre"
    >
      <div class="container">
        <LandingSectionHead
          eyebrow="Comparaison"
          title="Recouvra face aux autres solutions"
          title-id="comparaison-titre"
        />
        <div
          class="table-wrap"
          role="region"
          aria-labelledby="comparaison-titre"
          tabindex="0"
        >
          <table class="compare">
            <thead>
              <tr>
                <td />
                <th scope="col">
                  À la main
                </th>
                <th
                  scope="col"
                  class="compare__us"
                >
                  Recouvra
                </th>
                <th scope="col">
                  Société de recouvrement
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="row in comparison"
                :key="row.label"
              >
                <th scope="row">
                  {{ row.label }}
                </th>
                <td>{{ row.manual }}</td>
                <td class="compare__us">
                  {{ row.recouvra }}
                </td>
                <td>{{ row.agency }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <LandingFaq :items="faq" />

    <PublicCtaBand
      title="Combien vous doivent vos clients ?"
      text="Calculez les pénalités d’une facture en retard en 30 secondes, gratuitement."
      secondary-label="Ouvrir le calculateur"
      secondary-to="/outils/calcul-penalites-retard"
    />
  </div>
</template>

<style scoped>
.plan {
  display: flex;
  flex-direction: column;
  gap: var(--space-5);
  max-width: 520px;
  margin-top: var(--space-6);
  padding: var(--space-8);
  border: 1px solid var(--color-ink-line);
  border-radius: var(--radius-md);
  background: var(--color-ink-soft);
}

.plan__toggle {
  display: inline-flex;
  align-self: flex-start;
  padding: var(--space-1);
  border: 1px solid var(--color-ink-line);
  border-radius: var(--radius-sm);
}

.plan__option {
  min-height: var(--tap-min);
  padding: 0 var(--space-5);
  border: 0;
  border-radius: var(--radius-sm);
  background: transparent;
  color: var(--color-text-on-dark);
  font: inherit;
  font-weight: 600;
  cursor: pointer;
}

.plan__option[aria-pressed='true'] {
  background: var(--color-yellow);
  color: var(--color-ink);
}

.plan__price {
  display: flex;
  align-items: baseline;
  gap: var(--space-3);
}

.plan__price strong {
  font-size: var(--text-3xl);
  font-weight: 800;
  letter-spacing: -0.04em;
  color: var(--color-white);
}

.plan__note {
  color: var(--color-muted-on-dark);
}

.plan__list {
  display: grid;
  gap: var(--space-3);
  margin: 0;
  padding: 0;
  list-style: none;
}

.plan__list li::before {
  content: '✓';
  margin-right: var(--space-3);
  color: var(--color-yellow);
  font-weight: 800;
}

.trial {
  display: grid;
  gap: var(--space-5);
  margin: var(--space-8) 0 0;
  padding: 0;
  list-style: none;
}

.trial__step {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  padding: var(--space-6);
  border: 1px solid var(--color-line);
  border-radius: var(--radius-md);
  background: var(--color-white);
}

.trial__number {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--color-yellow);
  font-weight: 800;
}

.trial__step h3 {
  font-size: var(--text-lg);
}

.table-wrap {
  overflow-x: auto;
  margin-top: var(--space-8);
}

.compare {
  width: 100%;
  min-width: 720px;
  border-collapse: collapse;
}

.compare th,
.compare td {
  padding: var(--space-4);
  border-bottom: 1px solid var(--color-line);
  text-align: left;
  vertical-align: top;
}

.compare thead th {
  font-size: var(--text-lg);
}

.compare tbody th {
  color: var(--color-muted);
  font-weight: 600;
}

.compare__us {
  background: var(--color-paper);
  font-weight: 600;
}

.compare thead .compare__us {
  border-top: 4px solid var(--color-yellow);
}

@media (min-width: 1024px) {
  .trial {
    grid-template-columns: repeat(3, 1fr);
  }
}
</style>
