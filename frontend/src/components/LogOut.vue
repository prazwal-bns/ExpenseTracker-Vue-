<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useAppToast } from '../composables/useAppToast'

const toast = useAppToast()
const router = useRouter()
const auth = useAuthStore()
async function handleLogout() {
  try {
    await auth.logout()
    toast.success('Logged out successfully')
  } catch (err) {
    console.error('Server logout failed:', err)
  } finally {
    router.push({ name: 'login' })
  }
}
</script>

<template>
    <button type="button" class="text-sm font-medium text-ink-soft cursor-pointer" @click="handleLogout">
        <slot>Log out</slot>
    </button>
</template>