<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="flex min-h-dvh items-center justify-center bg-fog px-6">
    <div class="w-full max-w-md">
      <RouterLink :to="{ name: 'home' }" class="font-display text-2xl font-bold text-ink">
        Expense Tracker
      </RouterLink>
      <h1 class="mt-8 font-display text-3xl font-medium text-ink">Sign in</h1>
      <p class="mt-2 text-ink-soft">Login form coming next.</p>
      <RouterLink
        :to="{ name: 'register' }"
        class="mt-8 inline-flex text-sm font-semibold text-leaf hover:text-leaf-deep"
      >
        Need an account? Register
      </RouterLink>
    </div>
  </div>
</template>
