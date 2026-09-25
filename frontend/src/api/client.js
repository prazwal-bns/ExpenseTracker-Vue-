const API_URL = import.meta.env.VITE_API_URL

export async function apiFetch(path, options = {}) {
  const token = localStorage.getItem('token')

  const res = await fetch(`${API_URL}${path}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      ...(token && { Authorization: `Bearer ${token}` }),
      ...options.headers,
    },
  })

  if (res.status === 401) {
    localStorage.removeItem('token')
    location.assign('/login')
    throw new Error('Session expired')
  }

  if (res.status === 204) return null

  const data = await res.json().catch(() => ({}))
  if (!res.ok) throw new Error(data.message || 'Request failed')

  return data.data ?? data
}
