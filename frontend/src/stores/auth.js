import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as authApi from '../api/auth'

function readStoredUser() {
    try {
        return JSON.parse(localStorage.getItem('user')) ?? null
    } catch {
        return null
    }
}

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('token'))
    const expiresAt = ref(localStorage.getItem('expires_at'))
    const user = ref(readStoredUser())

    const isExpired = computed(() =>
        !!expiresAt.value && Date.now() >= new Date(expiresAt.value).getTime()
      )

    const isAuthenticated = computed(() => !!token.value && !isExpired.value)
    const isAdmin = computed(() => user.value?.role === 'admin')

    function setUser(value){
        user.value = value
        localStorage.setItem('user', JSON.stringify(value))
    }

    function setSession(data){
        token.value = data.token
        expiresAt.value = data.expires_at
        localStorage.setItem('token', data.token)
        localStorage.setItem('expires_at', data.expires_at)
        setUser(data.user)
    }

    function clearSession(){
        token.value = null
        expiresAt.value = null
        user.value = null
        localStorage.removeItem('token')
        localStorage.removeItem('expires_at')
        localStorage.removeItem('user')
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'token') token.value = event.newValue
        if (event.key === 'expires_at') expiresAt.value = event.newValue
        if (event.key === 'user') user.value = readStoredUser()
        if (event.key === null) {
            token.value = null
            expiresAt.value = null
            user.value = null
        }
    })

    async function fetchUser(){
        setUser(await authApi.getUser())
    }

    async function login(email, password){
        setSession(await authApi.login(email, password))
    }

    async function register(name, email, password, passwordConfirmation){
        setSession(await authApi.register(name, email, password, passwordConfirmation))
    }

    async function logout(){
        try{
            await authApi.logout()
        } finally {
            clearSession()
        }
    }


    return { token, expiresAt, user, isAuthenticated, isAdmin, login, register, logout, clearSession, fetchUser, setUser }

})
