<script setup>
import { ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import LogOut from './LogOut.vue'
import ThemeToggle from './ThemeToggle.vue'

const links = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        icon: 'M3.5 3.5h5v5h-5zM11.5 3.5h5v5h-5zM3.5 11.5h5v5h-5zM11.5 11.5h5v5h-5z',
    },
    {
        name: 'expenses',
        label: 'Expenses',
        icon: 'M5 2.5h10v15l-2.5-1.5-2.5 1.5-2.5-1.5L5 17.5zM8 6.5h4M8 9.5h4M8 12.5h2',
    },
    {
        name: 'expense-categories',
        label: 'Categories',
        icon: 'M10.6 2.5H16a1.5 1.5 0 0 1 1.5 1.5v5.4a1.5 1.5 0 0 1-.44 1.06l-6.6 6.6a1.5 1.5 0 0 1-2.12 0l-4.9-4.9a1.5 1.5 0 0 1 0-2.12l6.6-6.6a1.5 1.5 0 0 1 1.06-.44ZM13.75 6.25h.01',
    },
]

const route = useRoute()
const isMenuOpen = ref(false)

watch(() => route.name, () => {
    isMenuOpen.value = false
})
</script>

<template>
    <header class="relative z-30 rounded-2xl border border-ink/10 bg-surface/70 px-3 py-2.5 shadow-[0_12px_32px_rgb(16_42_36_/_0.07)] backdrop-blur-md sm:px-4">
        <div class="flex items-center justify-between gap-3">
            <RouterLink
                :to="{ name: 'dashboard' }"
                class="group flex items-center gap-3 rounded-xl pr-2"
            >
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-leaf to-leaf-deep text-white shadow-[0_8px_18px_rgb(31_111_84_/_0.35)] transition group-hover:scale-105">
                    <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6.5A2.5 2.5 0 0 1 5.5 4h9A2.5 2.5 0 0 1 17 6.5v7a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 3 13.5z" />
                        <path d="M13 10h4M3 7.5h14" />
                    </svg>
                </span>
                <span class="hidden leading-tight sm:block">
                    <span class="block font-display text-lg font-bold tracking-tight text-ink">
                        Expense Tracker
                    </span>
                    <span class="block text-[11px] font-medium tracking-wide text-ink-soft">
                        Spend smarter
                    </span>
                </span>
            </RouterLink>

            <nav
                class="hidden items-center gap-1 rounded-xl border border-ink/8 bg-fog/80 p-1 md:flex"
                aria-label="Main"
            >
                <RouterLink
                    v-for="link in links"
                    :key="link.name"
                    :to="{ name: link.name }"
                    class="flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium text-ink-soft transition hover:text-ink"
                    exact-active-class="bg-surface text-leaf! shadow-[0_2px_8px_rgb(16_42_36_/_0.08)] ring-1 ring-ink/5"
                >
                    <svg class="size-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path :d="link.icon" />
                    </svg>
                    {{ link.label }}
                </RouterLink>
            </nav>

            <div class="flex items-center gap-2">
                <ThemeToggle />

                <span class="hidden h-6 w-px bg-ink/10 md:block" aria-hidden="true" />

                <LogOut
                    class="hidden items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-red-50 hover:text-red-600 md:flex dark:hover:bg-red-500/10 dark:hover:text-red-400"
                    title="Log out"
                >
                    <svg class="size-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 17H5a1.5 1.5 0 0 1-1.5-1.5v-11A1.5 1.5 0 0 1 5 3h3M13 14l4-4-4-4M17 10H8" />
                    </svg>
                    <span class="hidden lg:inline">Log out</span>
                </LogOut>

                <button
                    type="button"
                    class="flex size-10 cursor-pointer items-center justify-center rounded-xl border border-ink/10 bg-surface text-ink-soft transition hover:text-ink md:hidden"
                    :aria-expanded="isMenuOpen"
                    aria-controls="mobile-menu"
                    aria-label="Toggle menu"
                    @click="isMenuOpen = !isMenuOpen"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                        <path v-if="isMenuOpen" d="M5 5l10 10M15 5L5 15" />
                        <path v-else d="M3.5 6h13M3.5 10h13M3.5 14h13" />
                    </svg>
                </button>
            </div>
        </div>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="-translate-y-1 opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="-translate-y-1 opacity-0"
        >
            <nav
                v-if="isMenuOpen"
                id="mobile-menu"
                class="mt-3 flex flex-col gap-1 border-t border-ink/8 pt-3 md:hidden"
                aria-label="Mobile"
            >
                <RouterLink
                    v-for="link in links"
                    :key="link.name"
                    :to="{ name: link.name }"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-ink-soft transition hover:bg-fog hover:text-ink"
                    exact-active-class="bg-leaf/10 text-leaf!"
                >
                    <svg class="size-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path :d="link.icon" />
                    </svg>
                    {{ link.label }}
                </RouterLink>

                <LogOut class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10 dark:hover:text-red-400">
                    <svg class="size-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 17H5a1.5 1.5 0 0 1-1.5-1.5v-11A1.5 1.5 0 0 1 5 3h3M13 14l4-4-4-4M17 10H8" />
                    </svg>
                    Log out
                </LogOut>
            </nav>
        </Transition>
    </header>
</template>
