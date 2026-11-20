export interface JwtPayload {
    username: string // l'email 
    roles: string[]
    iat: number
    exp: number // expiration, 'en secondes'
  }
  
  /**
   * Décode le payload sans vérifier la signature.
   * Le token vient directement de l'API,
   * appelée côté serveur.
   */
  export function decodeJwtPayload(token: string): JwtPayload {
    const payload = token.split('.')[1]
    if (!payload) {
      throw createError({ statusCode: 502, statusMessage: 'Token reçu invalide' })
    }
    return JSON.parse(Buffer.from(payload, 'base64url').toString('utf8'))
  }