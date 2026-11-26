<script setup lang="ts">
import type { FaqItem } from '~/components/landing/Faq.vue'

const siteUrl = useRuntimeConfig().public.siteUrl
const title = 'Relance de factures impayées pour le BTP | Recouvra'
const description = 'Recouvra relance vos clients à J+3, J+15 et J+30, calcule les pénalités et rédige la mise en demeure. Pour les artisans et PME du BTP. Essai gratuit de 14 jours.'

useSeoMeta({
  title,
  description,
  ogTitle: title,
  ogDescription: description,
  ogType: 'website',
  ogUrl: siteUrl,
  ogImage: `${siteUrl}/og-image.jpg`,
  ogImageWidth: 1200,
  ogImageHeight: 630,
  twitterCard: 'summary_large_image',
})

const faq: FaqItem[] = [
  { question: 'Mes clients sauront-ils que la relance vient d’un logiciel ?', answer: 'Chaque email porte le nom de votre entreprise et votre signature. Il part d’une adresse Recouvra, et les réponses de votre client arrivent directement dans votre boîte mail.' },
  { question: 'Puis-je modifier les messages et les dates de relance ?', answer: 'Oui. Le scénario J+3, J+15, J+30, J+45 est un point de départ : vous changez les jours, le ton et le texte, client par client si besoin.' },
  { question: 'Que se passe-t-il quand mon client paie ?', answer: 'Vous enregistrez le paiement, total ou partiel. Le reste dû est recalculé, et les relances s’arrêtent dès que la facture est soldée.' },
  { question: 'Quelles pénalités puis-je réclamer à un client professionnel ?', answer: 'Les pénalités prévues dans vos conditions générales (au minimum 3 fois le taux d’intérêt légal, ou le taux BCE + 10 points si rien n’est prévu), plus une indemnité forfaitaire de 40 € par facture. Code de commerce, articles L441-10 et D441-5.' },
  { question: 'Mes données sont-elles en sécurité ?', answer: 'Chaque entreprise a son espace isolé, les accès dépendent du rôle de chacun, et vous pouvez tout exporter à tout moment.' },
]

// Données structurées : aident Google à comprendre la page (entreprise, logiciel, FAQ)
const jsonLd = {
  '@context': 'https://schema.org',
  '@graph': [
    {
      '@type': 'Organization',
      'name': 'Recouvra',
      'url': siteUrl,
      'logo': `${siteUrl}/favicon.svg`,
      'email': 'contact@recouvra.fr',
    },
    {
      '@type': 'SoftwareApplication',
      'name': 'Recouvra',
      'applicationCategory': 'BusinessApplication',
      'operatingSystem': 'Web',
      'description': description,
      'offers': { '@type': 'Offer', 'price': '29', 'priceCurrency': 'EUR' },
    },
    {
      '@type': 'FAQPage',
      'mainEntity': faq.map(item => ({
        '@type': 'Question',
        'name': item.question,
        'acceptedAnswer': { '@type': 'Answer', 'text': item.answer },
      })),
    },
  ],
}

useHead({
  link: [{ rel: 'canonical', href: siteUrl }],
  script: [{ type: 'application/ld+json', innerHTML: JSON.stringify(jsonLd) }],
})
</script>

<template>
  <div>
    <LandingHero />
    <LandingTrust />
    <LandingDashboardDemo />
    <LandingSteps />
    <LandingScenarioRuler />
    <LandingFeatures />
    <LandingLaw />
    <LandingCalculatorSection />
    <LandingTrades />
    <LandingResources />
    <LandingPricing />
    <LandingFaq :items="faq" />
    <LandingFinalCta />
  </div>
</template>
