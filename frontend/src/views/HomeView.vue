<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="relative min-h-dvh overflow-hidden bg-linear-to-br from-fog via-mist to-[#d5ebe2]">
    <div
      class="animate-wash pointer-events-none absolute inset-0 opacity-40"
      style="
        background-image:
          radial-gradient(circle at 18% 22%, rgb(31 111 84 / 0.18), transparent 34%),
          radial-gradient(circle at 82% 18%, rgb(232 132 58 / 0.16), transparent 28%),
          linear-gradient(rgb(16 42 36 / 0.04) 1px, transparent 1px),
          linear-gradient(90deg, rgb(16 42 36 / 0.04) 1px, transparent 1px);
        background-size: auto, auto, 48px 48px, 48px 48px;
      "
    />

    <div class="relative mx-auto flex min-h-dvh max-w-6xl flex-col px-6 py-8 sm:px-10 lg:px-12">
      <header class="animate-rise flex items-center justify-between gap-4">
        <p class="font-display text-2xl font-bold tracking-tight text-ink sm:text-3xl">
          Expense Tracker
        </p>
        <nav class="flex items-center gap-3 sm:gap-4">
          <RouterLink
            :to="{ name: 'login' }"
            class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft transition hover:text-ink"
          >
            Login
          </RouterLink>
          <RouterLink
            :to="{ name: 'register' }"
            class="rounded-lg bg-leaf px-4 py-2 text-sm font-semibold text-white transition hover:bg-leaf-deep"
          >
            Register
          </RouterLink>
        </nav>
      </header>

      <main class="grid flex-1 items-center gap-12 py-16 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16">
        <section class="max-w-xl">
          <p class="animate-rise font-display text-5xl font-bold tracking-tight text-ink sm:text-6xl lg:text-7xl">
            Expense Tracker
          </p>
          <h1 class="animate-rise-delay mt-5 font-display text-3xl font-medium leading-tight text-ink sm:text-4xl">
            See where every rupee goes.
          </h1>
          <p class="animate-rise-delay-2 mt-5 max-w-md text-base leading-relaxed text-ink-soft sm:text-lg">
            Track spending by category, keep monthly habits clear, and stay in control without spreadsheet clutter.
          </p>

          <div class="animate-rise-delay-2 mt-10 flex flex-wrap items-center gap-4">
            <RouterLink
              :to="{ name: 'register' }"
              class="rounded-xl bg-amber px-6 py-3 text-sm font-semibold text-white shadow-[0_12px_30px_rgb(232_132_58_/_0.28)] transition hover:bg-amber-deep"
            >
              Create account
            </RouterLink>
            <RouterLink
              :to="{ name: 'login' }"
              class="rounded-xl border border-ink/15 bg-white/50 px-6 py-3 text-sm font-semibold text-ink backdrop-blur-sm transition hover:border-ink/30 hover:bg-white/80"
            >
              Sign in
            </RouterLink>
          </div>
        </section>

        <aside class="animate-drift relative hidden min-h-80 lg:block" aria-hidden="true">
          <div class="absolute inset-0 rounded-[2rem] bg-linear-to-br from-leaf/90 via-leaf-deep to-ink shadow-[0_30px_80px_rgb(16_42_36_/_0.25)]" />
          <div class="absolute inset-6 rounded-[1.5rem] border border-white/15 bg-white/5 backdrop-blur-[2px]" />
          <div class="absolute inset-x-10 top-14 space-y-5 text-white">
            <div class="flex items-end justify-between gap-4">
              <div>
                <p class="text-xs uppercase tracking-[0.2em] text-white/60">This month</p>
                <p class="mt-2 font-display text-4xl font-semibold">Rs 24,380</p>
              </div>
              <p class="rounded-full bg-amber/90 px-3 py-1 text-xs font-semibold text-ink">
                −12% vs last
              </p>
            </div>
            <div class="grid grid-cols-3 gap-3 pt-4">
              <div class="h-28 rounded-2xl bg-white/10" />
              <div class="mt-8 h-20 rounded-2xl bg-white/15" />
              <div class="mt-4 h-24 rounded-2xl bg-amber/80" />
            </div>
            <div class="flex gap-3 pt-2">
              <span class="rounded-full bg-white/10 px-3 py-1 text-xs text-white/80">Groceries</span>
              <span class="rounded-full bg-white/10 px-3 py-1 text-xs text-white/80">Transport</span>
              <span class="rounded-full bg-white/10 px-3 py-1 text-xs text-white/80">Rent</span>
            </div>
          </div>
        </aside>
      </main>
    </div>
  </div>
</template>
