<script setup>
import { useRouter } from 'vue-router'
import { logout } from '../api/auth'
import { useToast } from 'vue-toast-notification'
const toast = useToast()
const router = useRouter()

async function handleLogout() {
  try {
    await logout()
    toast.success('Logged out successfully')
  } catch (err) {
    console.error('Server logout failed:', err)
  } finally {
    localStorage.removeItem('token')
    router.push({ name: 'login' })
  }
}
</script>

<template>
    <button class="text-sm font-medium text-ink-soft cursor-pointer" @click="handleLogout">
        Log out
    </button>
</template>