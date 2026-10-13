import { defineStore } from 'pinia'
import { ref } from 'vue'
import * as adminUserApi from '../api/adminUsers'
import { useAuthStore } from './auth'

export const useAdminUserStore = defineStore('adminUsers', () => {
    const users = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const error = ref('')

    function sortByName(list){
        return [...list].sort((a, b) => a.name.localeCompare(b.name))
    }

    async function fetchUsers(){
        loading.value = true
        error.value = ''
        try{
            users.value = await adminUserApi.getUsers()
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    async function addUser(user){
        saving.value = true
        try{
            const created = await adminUserApi.createUser(user)
            users.value = sortByName([...users.value, created])
        } finally {
            saving.value = false
        }
    }

    async function updateUser(id, user){
        saving.value = true
        try{
            const updated = await adminUserApi.updateUser(id, user)
            users.value = sortByName(users.value.map(
                (existing) => (existing.id === id ? updated : existing)
            ))

            const auth = useAuthStore()
            if (auth.user?.id === id) {
                auth.setUser({ ...auth.user, name: updated.name, email: updated.email, role: updated.role })
            }
        } finally {
            saving.value = false
        }
    }

    async function deleteUser(id){
        saving.value = true
        try{
            await adminUserApi.deleteUser(id)
            users.value = users.value.filter((user) => user.id !== id)
        } finally {
            saving.value = false
        }
    }

    return {
        users,
        loading,
        saving,
        error,
        fetchUsers,
        addUser,
        updateUser,
        deleteUser
    }
})
