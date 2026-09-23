<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { logout } from '../api/auth'
import { getCategories } from '../api/category'

const expenseCategories = ref([])
const router = useRouter();

async function handleLogout() {
  try {
    await logout()
  } catch (err) {
    console.error('Server logout failed:', err)
  } finally {
    localStorage.removeItem('token')
    router.push({ name: 'login' })
  }
}

  onMounted(() => {
    fetchCategories()
  })

  async function fetchCategories(){
  try{
    expenseCategories.value = await getCategories();
  } catch (err) {
    console.error('Failed to fetch categories:', err)
  }
  
}
</script>

<template>
  <div class="relative min-h-dvh overflow-hidden bg-linear-to-br from-fog via-mist to-[#d5ebe2]">
    <div
      class="pointer-events-none absolute inset-0 opacity-30"
      style="
        background-image:
          radial-gradient(circle at 12% 18%, rgb(31 111 84 / 0.16), transparent 32%),
          linear-gradient(rgb(16 42 36 / 0.04) 1px, transparent 1px),
          linear-gradient(90deg, rgb(16 42 36 / 0.04) 1px, transparent 1px);
        background-size: auto, 48px 48px, 48px 48px;
      "
    />

    <div class="relative mx-auto max-w-3xl px-6 py-8 sm:px-10">
      <header class="flex items-center justify-between gap-4">
        <p class="font-display text-2xl font-bold tracking-tight text-ink">
          Expense Tracker
        </p>
        <button class="text-sm font-medium text-ink-soft cursor-pointer" @click="handleLogout">
          Log out
        </button>
      </header>

      <main class="mt-12">
        <p class="text-sm font-medium tracking-wide text-leaf uppercase">
          Dashboard
        </p>
        <h1 class="mt-2 font-display text-3xl font-medium text-ink sm:text-4xl">
          Your categories
        </h1>
        <p class="mt-3 max-w-lg text-sm leading-relaxed text-ink-soft sm:text-base">
          A quick look at how you group spending. More tools for expenses and totals will land here soon.
        </p>

        <div class="mt-10 rounded-2xl border border-ink/10 bg-white/70 p-6 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-8">
          <ul class="flex flex-col gap-3">
            <li
              v-for="category in expenseCategories"
              :key="category.id"
              class="flex items-center gap-3 rounded-xl border border-ink/8 bg-white/80 px-4 py-3"
            >
              <span
                class="size-3 shrink-0 rounded-full bg-leaf"
              />
              <p class="truncate text-sm font-semibold text-ink">
                {{ category.name }}
              </p>
            </li>
          </ul>
        </div>
      </main>
    </div>
  </div>
</template>
