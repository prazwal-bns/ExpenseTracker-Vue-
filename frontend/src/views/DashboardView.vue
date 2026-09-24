<script setup>
import { RouterLink } from 'vue-router'
import LogOut from '../components/LogOut.vue'

const overview = [
  { label: 'Spent this month', value: 'Rs 24,380', hint: '−12% vs last month', tone: 'leaf' },
  { label: 'Budget remaining', value: 'Rs 15,620', hint: 'Of Rs 40,000', tone: 'amber' },
  { label: 'Transactions', value: '47', hint: 'Across 6 categories', tone: 'ink' },
]

const recent = [
  { name: 'Grocery haul', category: 'Food', amount: 'Rs 2,450', when: 'Today' },
  { name: 'Fuel top-up', category: 'Transport', amount: 'Rs 1,200', when: 'Yesterday' },
  { name: 'Streaming', category: 'Subscriptions', amount: 'Rs 999', when: 'Mar 18' },
]
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

    <div class="relative mx-auto flex min-h-dvh max-w-5xl flex-col px-6 py-8 sm:px-10">
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
            :to="{ name: 'expense-categories' }"
            class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft transition hover:bg-white/80 hover:text-ink"
          >
            Categories
          </RouterLink>
          <LogOut class="rounded-lg px-3 py-2 transition hover:bg-white/80 hover:text-ink" />
        </nav>
      </header>

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
                A snapshot of this month’s spending. Live totals and expense tools come next.
              </p>
            </div>
            <p class="rounded-full bg-amber/15 px-3 py-1 text-xs font-semibold text-amber-deep">
              Sample data
            </p>
          </div>
        </section>

        <section class="mt-8 grid gap-4 sm:grid-cols-3">
          <article
            v-for="item in overview"
            :key="item.label"
            class="rounded-2xl border border-ink/10 bg-white/70 p-5 shadow-[0_12px_32px_rgb(16_42_36_/_0.06)] backdrop-blur-sm"
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

        <section class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
          <div class="rounded-2xl border border-ink/10 bg-white/70 p-5 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
            <div class="mb-5 flex items-center justify-between gap-3 border-b border-ink/8 pb-4">
              <h2 class="text-sm font-semibold text-ink">
                Recent activity
              </h2>
              <p class="text-xs text-ink-soft">
                Preview only
              </p>
            </div>

            <ul class="flex flex-col gap-2.5">
              <li
                v-for="row in recent"
                :key="row.name"
                class="flex items-center justify-between gap-4 rounded-xl border border-ink/8 bg-white/90 px-4 py-3.5"
              >
                <div class="min-w-0">
                  <p class="truncate text-sm font-semibold text-ink">
                    {{ row.name }}
                  </p>
                  <p class="mt-0.5 text-xs text-ink-soft">
                    {{ row.category }} · {{ row.when }}
                  </p>
                </div>
                <p class="shrink-0 text-sm font-semibold text-ink">
                  {{ row.amount }}
                </p>
              </li>
            </ul>
          </div>

          <div class="flex flex-col gap-4">
            <RouterLink
              :to="{ name: 'expense-categories' }"
              class="group flex flex-1 flex-col justify-between rounded-2xl border border-ink/10 bg-leaf px-5 py-6 text-white shadow-[0_20px_50px_rgb(31_111_84_/_0.25)] transition hover:bg-leaf-deep sm:px-6"
            >
              <div>
                <p class="text-xs font-medium tracking-[0.18em] text-white/70 uppercase">
                  Manage
                </p>
                <h2 class="mt-2 font-display text-2xl font-medium">
                  Categories
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-white/80">
                  Review how you group spending and prepare for full expense tracking.
                </p>
              </div>
              <p class="mt-8 text-sm font-semibold text-white/95 group-hover:underline">
                Open categories →
              </p>
            </RouterLink>

            <div class="rounded-2xl border border-dashed border-ink/15 bg-white/50 px-5 py-6 backdrop-blur-sm">
              <p class="text-sm font-semibold text-ink">
                Expenses
              </p>
              <p class="mt-2 text-sm leading-relaxed text-ink-soft">
                Add, edit, and filter transactions will live here soon.
              </p>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>
