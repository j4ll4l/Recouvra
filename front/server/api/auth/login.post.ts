import { z } from 'zod'

const loginSchema = z.object({
  email: z.email(),
  password: z.string().min(1),
})

export default defineEventHandler(async (event) => {
  // Body invalide → 400 automatique, avec le détail des erreurs
  const { email, password } = await readValidatedBody(event, loginSchema.parse)

  const { apiBaseUrl } = useRuntimeConfig(event)

  let token: string
  try {
    const response = await $fetch<{ token: string }>(`${apiBaseUrl}/login`, {
      method: 'POST',
      body: { email, password },
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
