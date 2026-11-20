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
  
    // base64url → base64, puis décodage en UTF-8 (gère les accents)
    const base64 = payload.replace(/-/g, '+').replace(/_/g, '/')
    const bytes = Uint8Array.from(atob(base64), (char) => char.charCodeAt(0))
  
    return JSON.parse(new TextDecoder().decode(bytes))
  }