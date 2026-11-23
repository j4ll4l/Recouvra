import type { ApiViolationList } from '../types'

/**
 * Transforme le 422 d'API Platform en { champ: message }.
 * Renvoie null si ce n'est pas une erreur de validation.
 */
export function violationsByField(error: unknown): Record<string, string> | null {
  const { statusCode, data } = error as { statusCode?: number, data?: Partial<ApiViolationList> }
  const violations = data?.violations

  if (statusCode !== 422 || !Array.isArray(violations)) {
    return null
  }

  return Object.fromEntries(violations.map(v => [v.propertyPath, v.message]))
}
