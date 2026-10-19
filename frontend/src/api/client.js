import { useAuthStore } from '../stores/auth'

const API_URL = import.meta.env.VITE_API_URL

export async function apiFetch(path, options = {}) {
  const auth = useAuthStore()
  const { etag, ...fetchOptions } = options
  const trackEtag = etag === true

  const res = await fetch(`${API_URL}${path}`, {
    ...fetchOptions,
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

  if (res.status === 403 && path.startsWith('/admin')) {
    await auth.fetchUser().catch(() => {})
    location.assign('/dashboard')
    throw new Error('You no longer have admin access')
  }

  if (res.status === 304) {
    return { unchanged: true, etag: res.headers.get('ETag') }
  }

  if (res.status === 204) return null

  const data = await res.json().catch(() => ({}))
  if (!res.ok) {
    const error = new Error(data.message || 'Request failed')
    error.status = res.status
    error.errors = data.errors ?? {}
    throw error
  }

  const payload = data.data ?? data

  if (trackEtag) {
    return { unchanged: false, data: payload, etag: res.headers.get('ETag') }
  }

  return payload
}
