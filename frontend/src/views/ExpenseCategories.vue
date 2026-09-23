<script setup>
import { onMounted, ref } from 'vue'
import { getCategories } from '../api/category'
import LogOut from '../components/LogOut.vue';

const expenseCategories = ref([])
const loading = ref(false)
const error = ref('')


  onMounted(() => {
    fetchCategories()
  })

  async function fetchCategories(){
  loading.value = true
  error.value = ''
  try{
    expenseCategories.value = await getCategories();
    console.log(expenseCategories.value)
  } catch (err) {
    error.value = err.message;
    console.error('Failed to fetch categories:', err)
  } finally {
    loading.value = false
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
          radial-gradient(circle at 88% 12%, rgb(232 132 58 / 0.12), transparent 28%),
          linear-gradient(rgb(16 42 36 / 0.04) 1px, transparent 1px),
          linear-gradient(90deg, rgb(16 42 36 / 0.04) 1px, transparent 1px);
        background-size: auto, auto, 48px 48px, 48px 48px;
      "
    />

    <div class="relative mx-auto flex min-h-dvh max-w-3xl flex-col px-6 py-8 sm:px-10">
      <header class="flex items-center justify-between gap-4 rounded-2xl border border-ink/10 bg-white/55 px-5 py-4 shadow-[0_10px_30px_rgb(16_42_36_/_0.05)] backdrop-blur-sm">
        <div>
          <p class="font-display text-2xl font-bold tracking-tight text-ink">
            Expense Tracker
          </p>
          <p class="mt-0.5 text-xs text-ink-soft">
            Signed in
          </p>
        </div>
        <LogOut class="rounded-lg px-3 py-2 transition hover:bg-white/80 hover:text-ink" />
      </header>

      <main class="mt-10 flex-1 pb-10">
        <section>
          <p class="text-sm font-medium tracking-[0.18em] text-leaf uppercase">
            Dashboard
          </p>
          <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
            <div>
              <h1 class="font-display text-3xl font-medium text-ink sm:text-4xl">
                Your categories
              </h1>
              <p class="mt-3 max-w-lg text-sm leading-relaxed text-ink-soft sm:text-base">
                A quick look at how you group spending. Expense tools come next.
              </p>
            </div>
            <p
              v-if="!loading"
              class="rounded-full bg-leaf/10 px-3 py-1 text-xs font-semibold text-leaf"
            >
              {{ expenseCategories.length }} total
            </p>
            <p
              v-else
              class="rounded-full bg-ink/5 px-3 py-1 text-xs font-semibold text-ink-soft"
            >
              Loading…
            </p>
          </div>
        </section>

        <section class="mt-8 rounded-2xl border border-ink/10 bg-white/70 p-5 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
          <div class="mb-5 flex items-center justify-between gap-3 border-b border-ink/8 pb-4">
            <h2 class="text-sm font-semibold text-ink">
              Categories
            </h2>
            <p class="text-xs text-ink-soft">
              Coming soon: add & edit
            </p>
          </div>

          <div
            v-if="loading"
            class="flex flex-col items-center gap-3 rounded-xl border border-ink/8 bg-fog/50 px-5 py-12 text-center"
          >
            <span class="size-8 animate-spin rounded-full border-2 border-leaf/20 border-t-leaf" />
            <p class="text-sm font-medium text-ink">
              Loading categories…
            </p>
            <p class="text-xs text-ink-soft">
              Fetching your spending groups
            </p>
          </div>

          <div
            v-else-if="error"
            class="rounded-xl border border-red-200 bg-red-50 px-5 py-8 text-center"
            role="alert"
          >
            <p class="font-display text-lg text-red-800">
              Something went wrong
            </p>
          </div>

          <ul
            v-else-if="expenseCategories.length"
            class="flex flex-col gap-2.5"
          >
            <li
              v-for="category in expenseCategories"
              :key="category.id"
              class="group flex items-center gap-4 rounded-xl border border-ink/8 bg-white/90 px-4 py-3.5 transition hover:border-leaf/25 hover:bg-white"
            >
              <span
                class="size-10 shrink-0 rounded-xl border border-ink/5 shadow-inner"
                :style="{ backgroundColor: category.color || '#1f6f54' }"
              />
              <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-ink">
                  {{ category.name }}
                </p>
                <p
                  v-if="category.description"
                  class="mt-0.5 truncate text-xs text-ink-soft"
                >
                  {{ category.description }}
                </p>
                <p
                  v-else
                  class="mt-0.5 text-xs text-ink-soft/70"
                >
                  No description
                </p>
              </div>
              <span
                v-if="category.expenses_count !== undefined"
                class="shrink-0 rounded-full bg-fog px-2.5 py-1 text-xs font-medium text-ink-soft"
              >
                {{ category.expenses_count }}
              </span>
            </li>
          </ul>

          <div
            v-else
            class="rounded-xl border border-dashed border-ink/15 bg-fog/60 px-5 py-10 text-center"
          >
            <p class="font-display text-lg text-ink">
              No categories yet
            </p>
            <p class="mt-2 text-sm text-ink-soft">
              Your spending groups will show up here once you add them.
            </p>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>
