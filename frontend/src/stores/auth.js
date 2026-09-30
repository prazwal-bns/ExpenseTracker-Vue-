import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as authApi from '../api/auth'



export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('token'))
    const expiresAt = ref(localStorage.getItem('expires_at'))

    const isExpired = computed(() =>
        !!expiresAt.value && Date.now() >= new Date(expiresAt.value).getTime()
      )

    const isAuthenticated = computed(() => !!token.value && !isExpired.value)

    function setSession(data){
        token.value = data.token
        expiresAt.value = data.expires_at
        localStorage.setItem('token', data.token)
        localStorage.setItem('expires_at', data.expires_at)
    }

    function clearSession(){
        token.value = null
        expiresAt.value = null
        localStorage.removeItem('token')
        localStorage.removeItem('expires_at')
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


    return { token, expiresAt, isAuthenticated, login, register, logout, clearSession }

})