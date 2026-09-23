const API_URL = import.meta.env.VITE_API_URL

export async function login(email, password) {
    const response = await fetch(`${API_URL}/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            email,
            password,
        }),
    })
    const data = await response.json()

    if (!response.ok) {
        throw new Error(data.message || 'Login failed')
    }

    return data
}

export async function register(name, email, password, passwordConfirmation) {
    const response = await fetch(`${API_URL}/register`, {
        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },

        body: JSON.stringify({
            name,
            email,
            password,
            password_confirmation: passwordConfirmation,
        }),
    })

    const data = await response.json()

    if (!response.ok) {
        throw new Error(data.message || 'Registration failed')
    }

    return data
}

export async function logout() {
    const token = localStorage.getItem('token')
    const response = await fetch(`${API_URL}/logout`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    })

    if (!response.ok) {
        throw new Error('Logout failed')
    }

    if (response.status === 204) return null
    return response.json()
}