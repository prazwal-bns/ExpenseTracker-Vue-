import { useAuthStore } from '../stores/auth'

const API_URL = import.meta.env.VITE_API_URL

export async function apiFetch(path, options = {}) {
  const auth = useAuthStore()

  const res = await fetch(`${API_URL}${path}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      ...(auth.token && { Authorization: `Bearer ${auth.token}` }),
      ...options.headers,
    },
  })

  if (res.status === 401) {
    auth.clearSession()
    location.assign('/login')
    throw new Error('Session expired')
  }

  if (res.status === 204) return null

  const data = await res.json().catch(() => ({}))
  if (!res.ok) {
    const error = new Error(data.message || 'Request failed')
    error.status = res.status
    error.errors = data.errors ?? {}
    throw error
  }

  return data.data ?? data
}
