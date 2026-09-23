<script setup>
import { RouterLink, useRouter } from 'vue-router'
import { register } from '../api/auth'
import { ref } from 'vue'
import { useToast } from 'vue-toast-notification'
const toast = useToast()
const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const error = ref('')
const loading = ref(false)

async function handleRegister() {
  loading.value = true
  error.value = ''

  try {
    const data = await register(
      name.value,
      email.value,
      password.value,
      passwordConfirmation.value,
    )

    localStorage.setItem('token', data.token)
    await router.push({ name: 'expense-categories' })
    toast.success('Account created successfully')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="relative flex min-h-dvh items-center justify-center overflow-hidden bg-linear-to-br from-fog via-mist to-[#d5ebe2] px-6 py-12">
    <div
      class="pointer-events-none absolute inset-0 opacity-40"
      style="
        background-image:
          radial-gradient(circle at 18% 22%, rgb(31 111 84 / 0.18), transparent 34%),
          radial-gradient(circle at 82% 18%, rgb(232 132 58 / 0.16), transparent 28%),
          linear-gradient(rgb(16 42 36 / 0.04) 1px, transparent 1px),
          linear-gradient(90deg, rgb(16 42 36 / 0.04) 1px, transparent 1px);
        background-size: auto, auto, 48px 48px, 48px 48px;
      "
    />

    <div class="relative w-full max-w-md">
      <RouterLink
        :to="{ name: 'home' }"
        class="font-display text-2xl font-bold tracking-tight text-ink transition hover:text-leaf"
      >
        Expense Tracker
      </RouterLink>

      <div class="mt-10 rounded-2xl border border-ink/10 bg-white/70 p-8 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm">
        <h1 class="font-display text-3xl font-medium text-ink">
          Create account
        </h1>
        <p class="mt-2 text-sm text-ink-soft">
          Enter your details to get started.
        </p>

        <form class="mt-8 flex flex-col gap-5" @submit.prevent="handleRegister">
          <div class="flex flex-col gap-2">
            <label for="name" class="text-sm font-medium text-ink">
              Name
            </label>
            <input
              id="name"
              v-model="name"
              type="text"
              required
              autocomplete="name"
              placeholder="Alex Rivera"
              class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:border-leaf focus:ring-2 focus:ring-leaf/20"
            >
          </div>

          <div class="flex flex-col gap-2">
            <label for="email" class="text-sm font-medium text-ink">
              Email
            </label>
            <input
              id="email"
              v-model="email"
              type="email"
              required
              autocomplete="email"
              placeholder="alex@example.com"
              class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:border-leaf focus:ring-2 focus:ring-leaf/20"
            >
          </div>

          <div class="flex flex-col gap-2">
            <label for="password" class="text-sm font-medium text-ink">
              Password
            </label>
            <input
              id="password"
              v-model="password"
              type="password"
              required
              autocomplete="new-password"
              placeholder="Enter your password"
              class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:border-leaf focus:ring-2 focus:ring-leaf/20"
            >
          </div>

          <div class="flex flex-col gap-2">
            <label for="password_confirmation" class="text-sm font-medium text-ink">
              Confirm password
            </label>
            <input
              id="password_confirmation"
              v-model="passwordConfirmation"
              type="password"
              required
              autocomplete="new-password"
              placeholder="Enter your password again"
              class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:border-leaf focus:ring-2 focus:ring-leaf/20"
            >
          </div>

          <p
            v-if="error"
            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            role="alert"
          >
            {{ error }}
          </p>

          <button
            type="submit"
            :disabled="loading"
            class="mt-1 rounded-xl bg-leaf px-4 py-3 text-sm font-semibold text-white transition hover:bg-leaf-deep disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ loading ? 'Creating account…' : 'Create account' }}
          </button>
        </form>
      </div>

      <p class="mt-6 text-center text-sm text-ink-soft">
        Already have an account?
        <RouterLink
          :to="{ name: 'login' }"
          class="font-semibold text-leaf transition hover:text-leaf-deep"
        >
          Sign in
        </RouterLink>
      </p>
    </div>
  </div>
</template>
