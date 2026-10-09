<script setup>
import { computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import AppNavbar from '../components/AppNavbar.vue'
import StatCard from '../components/StatCard.vue'
import { useExpenseStore } from '../stores/expenses'
import { useCategoryStore } from '../stores/categories'
import { useExpenseStats } from '../composables/useExpenseStats'
import { formatAmount, formatRelativeDate } from '../utils/format'

const expenseStore = useExpenseStore()
const categoryStore = useCategoryStore()
const stats = useExpenseStats(() => expenseStore.expenses)

onMounted(loadDashboard)

function loadDashboard() {
  expenseStore.fetchExpenses()
  categoryStore.fetchCategories()
}

const loading = computed(() => expenseStore.loading || categoryStore.loading)
const error = computed(() => expenseStore.error || categoryStore.error)

const overview = computed(() => {
  const change = stats.monthChange.value
  const topCategory = stats.categoryBreakdown.value[0]

  return [
    {
      label: 'Spent this month',
      value: formatAmount(stats.spentThisMonth.value),
      hint: change === null
        ? 'Nothing spent last month'
        : `${change > 0 ? '+' : '−'}${Math.abs(change).toFixed(0)}% vs last month`,
      tone: change > 0 ? 'amber' : 'leaf',
    },
    {
      label: 'Daily average',
      value: formatAmount(stats.dailyAverage.value),
      hint: `Across ${stats.daysSoFar} days so far`,
      tone: 'ink',
    },
    {
      label: 'Transactions',
      value: stats.transactionCount.value,
      hint: stats.transactionCount.value
        ? `Avg ${formatAmount(stats.averageExpense.value)} each`
        : 'Nothing logged yet',
      tone: 'ink',
    },
    {
      label: 'Categories',
      value: categoryStore.expenseCategories.length,
      hint: topCategory ? `Top: ${topCategory.name}` : 'No spending this month',
      tone: 'leaf',
    },
  ]
})

const trendMax = computed(() => Math.max(1, ...stats.monthlyTrend.value.map((month) => month.total)))
const sixMonthTotal = computed(() => stats.monthlyTrend.value.reduce((total, month) => total + month.total, 0))
</script>

<template>
  <div class="relative min-h-dvh overflow-hidden bg-linear-to-br from-fog via-mist to-glow">
    <div
      class="pointer-events-none absolute inset-0 opacity-30"
      style="
        background-image:
          radial-gradient(circle at 12% 18%, rgb(31 111 84 / 0.16), transparent 32%),
          radial-gradient(circle at 88% 12%, rgb(232 132 58 / 0.12), transparent 28%),
          linear-gradient(color-mix(in oklab, var(--color-ink) 5%, transparent) 1px, transparent 1px),
          linear-gradient(90deg, color-mix(in oklab, var(--color-ink) 5%, transparent) 1px, transparent 1px);
        background-size: auto, auto, 48px 48px, 48px 48px;
      "
    />

    <div class="relative mx-auto flex min-h-dvh max-w-7xl flex-col px-6 py-8 sm:px-10 lg:px-12">
      <AppNavbar />

      <main class="mt-10 flex-1 pb-10">
        <section>
          <p class="text-sm font-medium tracking-[0.18em] text-leaf uppercase">
            Overview
          </p>
          <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
            <div>
              <h1 class="font-display text-3xl font-medium text-ink sm:text-4xl">
                Your dashboard
              </h1>
              <p class="mt-3 max-w-lg text-sm leading-relaxed text-ink-soft sm:text-base">
                How your spending is shaping up this month, and where it's going.
              </p>
            </div>
            <p class="rounded-full bg-leaf/10 px-3 py-1 text-xs font-semibold text-leaf">
              {{ stats.monthLabel }}
            </p>
          </div>
        </section>

        <div
          v-if="loading"
          class="mt-8 flex flex-col items-center gap-3 rounded-2xl border border-ink/10 bg-surface/70 px-5 py-16 text-center backdrop-blur-sm"
        >
          <span class="size-8 animate-spin rounded-full border-2 border-leaf/20 border-t-leaf" />
          <p class="text-sm font-medium text-ink">
            Crunching your numbers…
          </p>
        </div>

        <div
          v-else-if="error"
          class="mt-8 flex flex-col items-center rounded-2xl border border-red-200 bg-red-50/80 px-5 py-12 text-center dark:border-red-500/30 dark:bg-red-500/10"
          role="alert"
        >
          <p class="font-display text-lg text-red-800 dark:text-red-200">
            Couldn't load your dashboard
          </p>
          <p class="mt-1.5 max-w-sm text-sm text-red-700/90 dark:text-red-300/90">
            {{ error }}
          </p>
          <button
            type="button"
            class="mt-5 cursor-pointer rounded-xl border border-red-200 bg-surface px-4 py-2 text-sm font-semibold text-red-700 transition hover:border-red-300 hover:bg-red-50 dark:border-red-500/30 dark:text-red-300 dark:hover:border-red-500/50 dark:hover:bg-red-500/10"
            @click="loadDashboard"
          >
            Try again
          </button>
        </div>

        <template v-else>
          <section class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard
              v-for="item in overview"
              :key="item.label"
              v-bind="item"
            />
          </section>

          <section class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <div class="rounded-2xl border border-ink/10 bg-surface/70 p-5 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
              <div class="mb-6 flex items-center justify-between gap-3 border-b border-ink/8 pb-4">
                <h2 class="text-sm font-semibold text-ink">
                  Spending trend
                </h2>
                <p class="text-xs text-ink-soft">
                  Last 6 months · <span class="font-semibold text-ink">{{ formatAmount(sixMonthTotal) }}</span>
                </p>
              </div>

              <div class="flex h-52 items-end gap-3 sm:gap-5">
                <div
                  v-for="month in stats.monthlyTrend.value"
                  :key="month.key"
                  class="flex h-full flex-1 flex-col items-center justify-end gap-2"
                  :title="`${month.label}: ${formatAmount(month.total)}`"
                >
                  <span
                    class="text-[11px] font-semibold"
                    :class="month.isCurrent ? 'text-leaf' : 'text-ink-soft'"
                  >
                    {{ month.total ? formatAmount(month.total) : '—' }}
                  </span>
                  <div
                    class="w-full max-w-14 rounded-t-lg transition-all duration-500"
                    :class="month.isCurrent ? 'bg-leaf shadow-[0_8px_20px_rgb(31_111_84_/_0.3)]' : 'bg-leaf/25'"
                    :style="{ height: `max(${(month.total / trendMax) * 100}%, 4px)` }"
                  />
                  <span
                    class="text-xs font-medium"
                    :class="month.isCurrent ? 'text-ink' : 'text-ink-soft'"
                  >
                    {{ month.label }}
                  </span>
                </div>
              </div>
            </div>

            <div class="rounded-2xl border border-ink/10 bg-surface/70 p-5 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
              <div class="mb-5 flex items-center justify-between gap-3 border-b border-ink/8 pb-4">
                <h2 class="text-sm font-semibold text-ink">
                  Top categories
                </h2>
                <p class="text-xs text-ink-soft">
                  This month
                </p>
              </div>

              <ul
                v-if="stats.categoryBreakdown.value.length"
                class="flex flex-col gap-4"
              >
                <li
                  v-for="category in stats.categoryBreakdown.value.slice(0, 5)"
                  :key="category.id"
                >
                  <div class="flex items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-2.5">
                      <span
                        class="size-3 shrink-0 rounded-full"
                        :style="{ backgroundColor: category.color }"
                      />
                      <p class="truncate text-sm font-semibold text-ink">
                        {{ category.name }}
                      </p>
                    </div>
                    <p class="shrink-0 text-sm font-semibold text-ink">
                      {{ formatAmount(category.total) }}
                      <span class="ml-1 text-xs font-medium text-ink-soft">{{ category.share.toFixed(0) }}%</span>
                    </p>
                  </div>
                  <div class="mt-2 h-2 overflow-hidden rounded-full bg-ink/6">
                    <div
                      class="h-full rounded-full transition-all duration-500"
                      :style="{ width: `${category.share}%`, backgroundColor: category.color }"
                    />
                  </div>
                </li>
              </ul>

              <div
                v-else
                class="rounded-xl border border-dashed border-ink/15 bg-fog/60 px-5 py-10 text-center"
              >
                <p class="text-sm font-medium text-ink">
                  No spending this month
                </p>
                <p class="mt-1 text-xs text-ink-soft">
                  Your category split will appear once you log expenses.
                </p>
              </div>
            </div>
          </section>

          <section class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
            <div class="rounded-2xl border border-ink/10 bg-surface/70 p-5 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
              <div class="mb-5 flex items-center justify-between gap-3 border-b border-ink/8 pb-4">
                <h2 class="text-sm font-semibold text-ink">
                  Recent activity
                </h2>
                <RouterLink
                  :to="{ name: 'expenses' }"
                  class="text-xs font-semibold text-leaf hover:text-leaf-deep"
                >
                  View all →
                </RouterLink>
              </div>

              <ul
                v-if="stats.recentExpenses.value.length"
                class="flex flex-col gap-2.5"
              >
                <li
                  v-for="expense in stats.recentExpenses.value"
                  :key="expense.id"
                  class="flex items-center justify-between gap-4 rounded-xl border border-ink/8 bg-surface/90 px-4 py-3.5"
                >
                  <div class="flex min-w-0 items-center gap-3">
                    <span
                      class="size-9 shrink-0 rounded-xl border border-ink/5 shadow-inner"
                      :style="{ backgroundColor: expense.category?.color || '#1f6f54' }"
                    />
                    <div class="min-w-0">
                      <p class="truncate text-sm font-semibold text-ink">
                        {{ expense.title }}
                      </p>
                      <p class="mt-0.5 truncate text-xs text-ink-soft">
                        {{ expense.category?.name ?? 'Uncategorized' }} · {{ formatRelativeDate(expense.spent_at) }}
                      </p>
                    </div>
                  </div>
                  <p class="shrink-0 text-sm font-semibold text-ink">
                    {{ formatAmount(expense.amount) }}
                  </p>
                </li>
              </ul>

              <div
                v-else
                class="rounded-xl border border-dashed border-ink/15 bg-fog/60 px-5 py-10 text-center"
              >
                <p class="text-sm font-medium text-ink">
                  No expenses yet
                </p>
                <p class="mt-1 text-xs text-ink-soft">
                  Your latest transactions will show up here.
                </p>
              </div>
            </div>

            <div class="flex flex-col gap-4">
              <RouterLink
                :to="{ name: 'expenses' }"
                class="group flex flex-1 flex-col justify-between rounded-2xl border border-ink/10 bg-leaf px-5 py-6 text-white shadow-[0_20px_50px_rgb(31_111_84_/_0.25)] transition hover:bg-leaf-deep sm:px-6"
              >
                <div>
                  <p class="text-xs font-medium tracking-[0.18em] text-white/70 uppercase">
                    Track
                  </p>
                  <h2 class="mt-2 font-display text-2xl font-medium">
                    Expenses
                  </h2>
                  <p class="mt-3 text-sm leading-relaxed text-white/80">
                    {{ expenseStore.expenses.length }} transactions logged. Add, edit or review your spending.
                  </p>
                </div>
                <p class="mt-6 text-sm font-semibold text-white/95 group-hover:underline">
                  Open expenses →
                </p>
              </RouterLink>

              <RouterLink
                :to="{ name: 'expense-categories' }"
                class="group rounded-2xl border border-ink/10 bg-surface/70 px-5 py-6 backdrop-blur-sm transition hover:border-leaf/30 sm:px-6"
              >
                <p class="text-xs font-medium tracking-[0.18em] text-ink-soft uppercase">
                  Manage
                </p>
                <h2 class="mt-2 font-display text-xl font-medium text-ink">
                  Categories
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">
                  {{ categoryStore.expenseCategories.length }} spending groups. Rename, recolor or add more.
                </p>
                <p class="mt-4 text-sm font-semibold text-leaf group-hover:underline">
                  Open categories →
                </p>
              </RouterLink>
            </div>
          </section>
        </template>
      </main>
    </div>
  </div>
</template>
