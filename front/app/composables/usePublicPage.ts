export interface BreadcrumbItem {
  label: string
  /** Chemin interne ; absent pour la page courante */
  to?: string
}

/**
 * SEO commun aux pages publiques : title, description, image de partage, URL canonique
 * et données structurées (fil d'Ariane + schémas propres à la page).
 */
export function usePublicPage(options: {
  path: string
  title: string
  description: string
  breadcrumb: BreadcrumbItem[]
  schemas?: Record<string, unknown>[]
}) {
  const siteUrl = useRuntimeConfig().public.siteUrl
  const url = `${siteUrl}${options.path}`

  useSeoMeta({
    title: options.title,
    description: options.description,
    ogTitle: options.title,
    ogDescription: options.description,
    ogType: 'website',
    ogUrl: url,
    ogImage: `${siteUrl}/og-image.jpg`,
    twitterCard: 'summary_large_image',
  })

  const breadcrumbSchema = {
    '@type': 'BreadcrumbList',
    'itemListElement': options.breadcrumb.map((item, index) => ({
      '@type': 'ListItem',
      'position': index + 1,
      'name': item.label,
      'item': `${siteUrl}${item.to ?? options.path}`,
    })),
  }

  useHead({
    link: [{ rel: 'canonical', href: url }],
    script: [{
      type: 'application/ld+json',
      innerHTML: JSON.stringify({ '@context': 'https://schema.org', '@graph': [breadcrumbSchema, ...(options.schemas ?? [])] }),
    }],
  })
}

/** Schéma FAQPage construit à partir des questions affichées sur la page */
export function faqSchema(items: { question: string, answer: string }[]) {
  return {
    '@type': 'FAQPage',
    'mainEntity': items.map(item => ({
      '@type': 'Question',
      'name': item.question,
      'acceptedAnswer': { '@type': 'Answer', 'text': item.answer },
    })),
  }
}
