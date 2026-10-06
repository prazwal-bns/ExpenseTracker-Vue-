<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import LogOut from '../components/LogOut.vue'
import ThemeToggle from '../components/ThemeToggle.vue'
import ActionButton from '../components/ActionButton.vue'
import PaginationControls from '../components/PaginationControls.vue'

const summary = [
  { label: 'Spent this month', value: 'Rs 24,380', hint: '−12% vs last month', tone: 'leaf' },
  { label: 'Transactions', value: '47', hint: 'Avg Rs 519 each', tone: 'ink' },
  { label: 'Largest expense', value: 'Rs 8,500', hint: 'Rent · Oct 1', tone: 'amber' },
]

const categories = [
  { id: 1, name: 'Food', color: '#22C55E' },
  { id: 2, name: 'Transport', color: '#0EA5E9' },
  { id: 3, name: 'Subscriptions', color: '#6366F1' },
  { id: 4, name: 'Rent', color: '#E8843A' },
  { id: 5, name: 'Health', color: '#EF4444' },
]

const expenses = [
  { id: 1, title: 'Grocery haul', notes: 'Weekly vegetables and fruits', category: categories[0], spent_at: 'Oct 6, 2026', amount: 'Rs 2,450' },
  { id: 2, title: 'Fuel top-up', notes: null, category: categories[1], spent_at: 'Oct 5, 2026', amount: 'Rs 1,200' },
  { id: 3, title: 'Streaming', notes: 'Monthly plan', category: categories[2], spent_at: 'Oct 4, 2026', amount: 'Rs 999' },
  { id: 4, title: 'Pharmacy', notes: 'Vitamins', category: categories[4], spent_at: 'Oct 3, 2026', amount: 'Rs 640' },
  { id: 5, title: 'Lunch with team', notes: null, category: categories[0], spent_at: 'Oct 2, 2026', amount: 'Rs 1,150' },
  { id: 6, title: 'October rent', notes: 'Apartment', category: categories[3], spent_at: 'Oct 1, 2026', amount: 'Rs 8,500' },
  { id: 7, title: 'Bus pass', notes: 'Monthly commuter pass', category: categories[1], spent_at: 'Oct 1, 2026', amount: 'Rs 900' },
]

const currentPage = ref(1)
const perPage = ref(10)
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
      <header class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-ink/10 bg-surface/55 px-5 py-4 shadow-[0_10px_30px_rgb(16_42_36_/_0.05)] backdrop-blur-sm">
        <div>
          <p class="font-display text-2xl font-bold tracking-tight text-ink">
            Expense Tracker
          </p>
          <p class="mt-0.5 text-xs text-ink-soft">
            Signed in
          </p>
        </div>
        <nav class="flex items-center gap-2 sm:gap-3">
          <ThemeToggle />
          <RouterLink
            :to="{ name: 'dashboard' }"
            class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft transition hover:bg-surface/80 hover:text-ink"
          >
            Dashboard
          </RouterLink>
          <RouterLink
            :to="{ name: 'expense-categories' }"
            class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft transition hover:bg-surface/80 hover:text-ink"
          >
            Categories
          </RouterLink>
          <LogOut class="rounded-lg px-3 py-2 transition hover:bg-surface/80 hover:text-ink" />
        </nav>
      </header>

      <main class="mt-10 flex-1 pb-10">
        <section>
          <p class="text-sm font-medium tracking-[0.18em] text-leaf uppercase">
            Expenses
          </p>
          <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
            <div>
              <h1 class="font-display text-3xl font-medium text-ink sm:text-4xl">
                Your expenses
              </h1>
              <p class="mt-3 max-w-lg text-sm leading-relaxed text-ink-soft sm:text-base">
                Every transaction in one place. Search, filter and keep your spending honest.
              </p>
            </div>
            <p class="rounded-full bg-leaf/10 px-3 py-1 text-xs font-semibold text-leaf">
              {{ expenses.length }} total
            </p>
          </div>
        </section>

        <section class="mt-8 grid gap-4 sm:grid-cols-3">
          <article
            v-for="item in summary"
            :key="item.label"
            class="rounded-2xl border border-ink/10 bg-surface/70 p-5 shadow-[0_12px_32px_rgb(16_42_36_/_0.06)] backdrop-blur-sm"
          >
            <p class="text-xs font-medium tracking-wide text-ink-soft uppercase">
              {{ item.label }}
            </p>
            <p class="mt-3 font-display text-2xl font-semibold text-ink sm:text-3xl">
              {{ item.value }}
            </p>
            <p
              class="mt-2 text-xs font-medium"
              :class="{
                'text-leaf': item.tone === 'leaf',
                'text-amber-deep': item.tone === 'amber',
                'text-ink-soft': item.tone === 'ink',
              }"
            >
              {{ item.hint }}
            </p>
          </article>
        </section>

        <section class="mt-6 rounded-2xl border border-ink/10 bg-surface/70 p-5 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
          <div class="mb-5 flex items-center justify-between gap-3 border-b border-ink/8 pb-4">
            <h2 class="text-sm font-semibold text-ink">
              Transactions
            </h2>
            <button
              type="button"
              class="inline-flex cursor-pointer items-center gap-1.5 rounded-full bg-leaf px-3.5 py-1.5 text-sm font-medium text-white transition hover:bg-leaf-deep"
            >
              <svg class="size-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M10 4.5v11M4.5 10h11" />
              </svg>
              Add Expense
            </button>
          </div>

          <div class="mb-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_12rem_11rem_11rem]">
            <label class="relative">
              <span class="sr-only">Search expenses</span>
              <svg class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-ink-soft" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                <circle cx="9" cy="9" r="5.5" />
                <path d="M13.5 13.5 17 17" />
              </svg>
              <input
                type="search"
                placeholder="Search by title or notes…"
                class="w-full rounded-xl border border-ink/15 bg-surface py-2.5 pr-4 pl-10 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:border-leaf focus:ring-2 focus:ring-leaf/20"
              >
            </label>

            <label class="relative">
              <span class="sr-only">Filter by category</span>
              <select class="w-full cursor-pointer appearance-none rounded-xl border border-ink/15 bg-surface py-2.5 pr-9 pl-4 text-sm text-ink outline-none transition focus:border-leaf focus:ring-2 focus:ring-leaf/20">
                <option value="">All categories</option>
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-ink-soft" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 7.5l5 5 5-5" />
              </svg>
            </label>

            <label>
              <span class="sr-only">Filter by month</span>
              <input
                type="month"
                value="2026-10"
                class="w-full rounded-xl border border-ink/15 bg-surface px-4 py-2.5 text-sm text-ink outline-none transition focus:border-leaf focus:ring-2 focus:ring-leaf/20"
              >
            </label>

            <label class="relative">
              <span class="sr-only">Sort expenses</span>
              <select class="w-full cursor-pointer appearance-none rounded-xl border border-ink/15 bg-surface py-2.5 pr-9 pl-4 text-sm text-ink outline-none transition focus:border-leaf focus:ring-2 focus:ring-leaf/20">
                <option value="newest">Newest first</option>
                <option value="oldest">Oldest first</option>
                <option value="highest">Highest amount</option>
                <option value="lowest">Lowest amount</option>
              </select>
              <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-ink-soft" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 7.5l5 5 5-5" />
              </svg>
            </label>
          </div>

          <div class="flex flex-col gap-5">
            <div class="overflow-hidden rounded-xl border border-ink/8 bg-surface/90">
              <table class="w-full table-fixed text-left">
                <thead class="bg-fog/80">
                  <tr class="text-[11px] font-semibold tracking-[0.14em] text-ink-soft uppercase">
                    <th scope="col" class="px-4 py-3 lg:px-6">
                      Expense
                    </th>
                    <th scope="col" class="hidden w-44 px-4 py-3 md:table-cell lg:px-6">
                      Category
                    </th>
                    <th scope="col" class="hidden w-36 px-4 py-3 sm:table-cell lg:px-6">
                      Date
                    </th>
                    <th scope="col" class="w-28 px-4 py-3 text-right lg:w-36 lg:px-6">
                      Amount
                    </th>
                    <th scope="col" class="w-24 px-4 py-3 text-right lg:w-52 lg:px-6">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-ink/6">
                  <tr
                    v-for="expense in expenses"
                    :key="expense.id"
                    class="transition hover:bg-mist/60"
                  >
                    <td class="px-4 py-3.5 lg:px-6">
                      <div class="flex min-w-0 items-center gap-3">
                        <span
                          class="size-9 shrink-0 rounded-xl border border-ink/5 shadow-inner"
                          :style="{ backgroundColor: expense.category.color }"
                        />
                        <div class="min-w-0">
                          <p class="truncate text-sm font-semibold text-ink">
                            {{ expense.title }}
                          </p>
                          <p
                            class="mt-0.5 truncate text-xs"
                            :class="expense.notes ? 'text-ink-soft' : 'text-ink-soft/60 italic'"
                          >
                            <span class="md:hidden">{{ expense.category.name }} · </span>
                            <span class="sm:hidden">{{ expense.spent_at }} · </span>
                            {{ expense.notes || 'No notes' }}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="hidden px-4 py-3.5 md:table-cell lg:px-6">
                      <span class="inline-flex max-w-full items-center gap-2 rounded-full bg-fog px-2.5 py-1 text-xs font-semibold text-ink-soft">
                        <span
                          class="size-2 shrink-0 rounded-full"
                          :style="{ backgroundColor: expense.category.color }"
                        />
                        <span class="truncate">{{ expense.category.name }}</span>
                      </span>
                    </td>
                    <td class="hidden px-4 py-3.5 text-sm text-ink-soft sm:table-cell lg:px-6">
                      {{ expense.spent_at }}
                    </td>
                    <td class="px-4 py-3.5 text-right text-sm font-semibold whitespace-nowrap text-ink lg:px-6">
                      {{ expense.amount }}
                    </td>
                    <td class="px-4 py-3.5 lg:px-6">
                      <div class="flex items-center justify-end gap-1 lg:gap-2">
                        <ActionButton
                          label="Edit"
                          icon="edit"
                          :aria-label="`Edit ${expense.title}`"
                        />
                        <ActionButton
                          label="Delete"
                          icon="delete"
                          variant="danger"
                          :aria-label="`Delete ${expense.title}`"
                        />
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <PaginationControls
              v-model:page="currentPage"
              v-model:per-page="perPage"
              :total-items="expenses.length"
              item-label="expenses"
            />
          </div>

          <!-- Empty state (show when there are no expenses) -->
          <div
            v-if="false"
            class="rounded-xl border border-dashed border-ink/15 bg-fog/60 px-5 py-10 text-center"
          >
            <p class="font-display text-lg text-ink">
              No expenses yet
            </p>
            <p class="mt-2 text-sm text-ink-soft">
              Add your first expense to start tracking where your money goes.
            </p>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>
