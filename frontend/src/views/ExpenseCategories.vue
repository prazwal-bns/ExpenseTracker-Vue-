<script setup>
import { RouterLink } from 'vue-router'
import LogOut from '../components/LogOut.vue';
import { useCategoryStore } from '../stores/categories'
import { computed, onMounted, ref, watch } from 'vue';
import CategoryFormModal  from '../components/CategoryFormModal.vue';
import PaginationControls from '../components/PaginationControls.vue';

const categoryStore = useCategoryStore()
const showCategoryModal = ref(false)

const perPage = 5
const currentPage = ref(1)

const paginatedCategories = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return categoryStore.expenseCategories.slice(start, start + perPage)
})

watch(() => categoryStore.expenseCategories.length, (total) => {
  const lastPage = Math.max(1, Math.ceil(total / perPage))
  if (currentPage.value > lastPage) currentPage.value = lastPage
})

onMounted(() => categoryStore.fetchCategories())
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
      <header class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-ink/10 bg-white/55 px-5 py-4 shadow-[0_10px_30px_rgb(16_42_36_/_0.05)] backdrop-blur-sm">
        <div>
          <p class="font-display text-2xl font-bold tracking-tight text-ink">
            Expense Tracker
          </p>
          <p class="mt-0.5 text-xs text-ink-soft">
            Signed in
          </p>
        </div>
        <nav class="flex items-center gap-2 sm:gap-3">
          <RouterLink
            :to="{ name: 'dashboard' }"
            class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft transition hover:bg-white/80 hover:text-ink"
          >
            Dashboard
          </RouterLink>
          <LogOut class="rounded-lg px-3 py-2 transition hover:bg-white/80 hover:text-ink" />
        </nav>
      </header>

      <main class="mt-10 flex-1 pb-10">
        <section>
          <p class="text-sm font-medium tracking-[0.18em] text-leaf uppercase">
            Categories
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
              v-if="!categoryStore.loading"
              class="rounded-full bg-leaf/10 px-3 py-1 text-xs font-semibold text-leaf"
            >
              {{ categoryStore.expenseCategories.length }} total
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
            <div>
              <button
                type="button"
                class="rounded-full bg-leaf px-3 py-1 text-sm font-medium text-white cursor-pointer"
                @click="showCategoryModal = true"
              >
                Add Category
              </button>

              <CategoryFormModal
                :open="showCategoryModal"
                @close="showCategoryModal = false"
                @created="showCategoryModal = false"
              />
            </div>
          </div>

          <div
            v-if="categoryStore.loading"
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
            v-else-if="categoryStore.error"
            class="flex flex-col items-center rounded-xl border border-red-200 bg-red-50/80 px-5 py-10 text-center"
            role="alert"
          >
            <span class="flex size-12 items-center justify-center rounded-full bg-red-100 text-red-600">
              <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
              </svg>
            </span>
            <p class="mt-4 font-display text-lg text-red-800">
              Couldn't load your categories
            </p>
            <p class="mt-1.5 max-w-sm text-sm text-red-700/90">
              {{ categoryStore.error }}
            </p>
            <button
              type="button"
              class="mt-5 cursor-pointer rounded-xl border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-700 transition hover:border-red-300 hover:bg-red-50"
              @click="categoryStore.fetchCategories()"
            >
              Try again
            </button>
          </div>

          <div
            v-else-if="categoryStore.expenseCategories.length"
            class="flex flex-col gap-5"
          >
            <div class="overflow-hidden rounded-xl border border-ink/8 bg-white/90">
              <table class="w-full table-fixed text-left">
                <thead class="bg-fog/80">
                  <tr class="text-[11px] font-semibold tracking-[0.14em] text-ink-soft uppercase">
                    <th scope="col" class="px-4 py-3 sm:w-2/5">
                      Category
                    </th>
                    <th scope="col" class="hidden px-4 py-3 sm:table-cell">
                      Description
                    </th>
                    <th scope="col" class="w-24 px-4 py-3 text-right">
                      Expenses
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-ink/6">
                  <tr
                    v-for="category in paginatedCategories"
                    :key="category.id"
                    class="transition hover:bg-mist/60"
                  >
                    <td class="px-4 py-3.5">
                      <div class="flex min-w-0 items-center gap-3">
                        <span
                          class="size-9 shrink-0 rounded-xl border border-ink/5 shadow-inner"
                          :style="{ backgroundColor: category.color || '#1f6f54' }"
                        />
                        <div class="min-w-0">
                          <p class="truncate text-sm font-semibold text-ink">
                            {{ category.name }}
                          </p>
                          <p class="mt-0.5 truncate text-xs text-ink-soft sm:hidden">
                            {{ category.description || 'No description' }}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="hidden px-4 py-3.5 sm:table-cell">
                      <p
                        class="truncate text-sm"
                        :class="category.description ? 'text-ink-soft' : 'text-ink-soft/60 italic'"
                      >
                        {{ category.description || 'No description' }}
                      </p>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                      <span class="inline-flex min-w-8 justify-center rounded-full bg-fog px-2.5 py-1 text-xs font-semibold text-ink-soft">
                        {{ category.expenses_count ?? 0 }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <PaginationControls
              v-model:page="currentPage"
              :total-items="categoryStore.expenseCategories.length"
              :per-page="perPage"
              item-label="categories"
            />
          </div>

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
