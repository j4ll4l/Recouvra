const METHODS_WITH_BODY = ['POST', 'PUT', 'PATCH']

export default defineEventHandler(async (event) => {
  // 1. Pas de session 401 automatique
  const session = await requireUserSession(event)
  const token = session.secure?.token
  const expiresAt = session.secure?.expiresAt ?? 0

  // 2. JWT expiré on force une reconnexion
  if (!token || Date.now() >= expiresAt) {
    await clearUserSession(event)
    throw createError({ statusCode: 401, statusMessage: 'Session expirée' })
  }

  // 3. Chemin demandé
  const path = getRouterParam(event, 'path') ?? ''
  if (path.includes('..')) {
    throw createError({ statusCode: 400, statusMessage: 'Chemin invalide' })
  }

  const { apiBaseUrl } = useRuntimeConfig(event)
  const hasBody = METHODS_WITH_BODY.includes(event.method)
  const body = hasBody ? await readRawBody(event) : undefined

  // 4. Appel à Symfony avec le JWT.
  const response = await $fetch.raw(`${apiBaseUrl}/${path}`, {
    method: event.method,
    query: getQuery(event),
    body,
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: getHeader(event, 'accept') ?? 'application/ld+json',
      ...(hasBody && { 'Content-Type': getHeader(event, 'content-type') ?? 'application/ld+json' }),
    },
    ignoreResponseError: true,
  })

  if (response.status === 401) {
    await clearUserSession(event)
  }

  setResponseStatus(event, response.status)
  const contentType = response.headers.get('content-type')
  if (contentType) setHeader(event, 'content-type', contentType)

  return response._data ?? null
})