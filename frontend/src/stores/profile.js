import { defineStore } from 'pinia'
import { ref } from 'vue'
import * as profileApi from '../api/profile'
import { useAuthStore } from './auth'

export const useProfileStore = defineStore('profile', () => {
    const profile = ref(null)
    const loading = ref(false)
    const savingDetails = ref(false)
    const savingPassword = ref(false)
    const deleting = ref(false)
    const error = ref('')

    async function fetchProfile(){
        loading.value = true
        error.value = ''
        try{
            profile.value = await profileApi.getProfile()
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    async function updateProfile(details){
        savingDetails.value = true
        try{
            profile.value = await profileApi.updateProfile(details)
            useAuthStore().setUser(profile.value)
        } finally {
            savingDetails.value = false
        }
    }

    async function updatePassword(passwords){
        savingPassword.value = true
        try{
            await profileApi.updatePassword(passwords)
        } finally {
            savingPassword.value = false
        }
    }

    async function deleteAccount(password){
        deleting.value = true
        try{
            await profileApi.deleteAccount(password)
            profile.value = null
            useAuthStore().clearSession()
        } finally {
            deleting.value = false
        }
    }

    return {
        profile,
        loading,
        savingDetails,
        savingPassword,
        deleting,
        error,
        fetchProfile,
        updateProfile,
        updatePassword,
        deleteAccount
    }
})
