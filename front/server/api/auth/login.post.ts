export default defineEventHandler(async (event) => {
  const body = await readBody<{ email?: unknown, password?: unknown }>(event)

  if (typeof body?.email !== 'string' || typeof body?.password !== 'string') {
    throw createError({ statusCode: 400, statusMessage: 'Email et mot de passe requis' })
  }

  const { apiBaseUrl } = useRuntimeConfig(event)

  let token: string
  try {
    const response = await $fetch<{ token: string }>(`${apiBaseUrl}/login`, {
      method: 'POST',
      body: { email: body.email, password: body.password },
    })
    token = response.token
  }
  catch (error) {
    const status = (error as { statusCode?: number }).statusCode
    if (status === 401) {
      throw createError({ statusCode: 401, statusMessage: 'Identifiants invalides' })
    }
    throw createError({ statusCode: 502, statusMessage: 'API indisponible' })
  }

  const payload = decodeJwtPayload(token)

  await setUserSession(event, {
    user: { email: payload.username, roles: payload.roles },
    secure: { token, expiresAt: payload.exp * 1000 },
  })

  return { ok: true }
})
