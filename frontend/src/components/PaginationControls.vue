<script setup>
import { computed } from 'vue'

const props = defineProps({
    totalItems: { type: Number, required: true },
    itemLabel: { type: String, default: 'items' },
    perPageOptions: { type: Array, default: () => [5, 10, 15, 20] },
})

const page = defineModel('page', { type: Number, default: 1 })
const perPage = defineModel('perPage', { type: Number, default: 5 })

const totalPages = computed(() => Math.max(1, Math.ceil(props.totalItems / perPage.value)))
const rangeStart = computed(() => (props.totalItems === 0 ? 0 : (page.value - 1) * perPage.value + 1))
const rangeEnd = computed(() => Math.min(page.value * perPage.value, props.totalItems))

const pageNumbers = computed(() => {
    const total = totalPages.value
    const current = page.value

    if (total <= 7) {
        return Array.from({ length: total }, (_, index) => index + 1)
    }

    const pages = [1]
    const start = Math.max(2, current - 1)
    const end = Math.min(total - 1, current + 1)

    if (start > 2) pages.push('…')
    for (let number = start; number <= end; number++) pages.push(number)
    if (end < total - 1) pages.push('…')
    pages.push(total)

    return pages
})

function goTo(number) {
    page.value = Math.min(Math.max(1, number), totalPages.value)
}
</script>

<template>
    <nav
        class="flex flex-col items-center justify-between gap-3 sm:flex-row"
        aria-label="Pagination"
    >
        <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
            <label class="flex items-center gap-2 text-xs text-ink-soft">
                Rows per page
                <span class="relative">
                    <select
                        v-model.number="perPage"
                        class="cursor-pointer appearance-none rounded-lg border border-ink/10 bg-surface py-1.5 pr-7 pl-2.5 text-xs font-semibold text-ink transition hover:border-ink/20 focus:border-leaf focus:ring-2 focus:ring-leaf/20 focus:outline-none"
                    >
                        <option
                            v-for="option in perPageOptions"
                            :key="option"
                            :value="option"
                        >
                            {{ option }}
                        </option>
                    </select>
                    <svg class="pointer-events-none absolute top-1/2 right-2 size-3 -translate-y-1/2 text-ink-soft" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 7.5l5 5 5-5" />
                    </svg>
                </span>
            </label>

            <p class="text-xs text-ink-soft">
                Showing
                <span class="font-semibold text-ink">{{ rangeStart }}–{{ rangeEnd }}</span>
                of
                <span class="font-semibold text-ink">{{ totalItems }}</span>
                {{ itemLabel }}
            </p>
        </div>

        <div
            v-if="totalPages > 1"
            class="flex items-center gap-1"
        >
            <button
                type="button"
                class="flex size-9 cursor-pointer items-center justify-center rounded-lg text-ink-soft transition hover:bg-surface hover:text-ink disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-transparent"
                :disabled="page === 1"
                aria-label="Previous page"
                @click="goTo(page - 1)"
            >
                <svg class="size-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12.5 15l-5-5 5-5" />
                </svg>
            </button>

            <template v-for="(number, index) in pageNumbers" :key="`${number}-${index}`">
                <span
                    v-if="number === '…'"
                    class="flex size-9 items-center justify-center text-xs text-ink-soft"
                >…</span>
                <button
                    v-else
                    type="button"
                    class="size-9 cursor-pointer rounded-lg text-sm font-semibold transition"
                    :class="number === page
                        ? 'bg-leaf text-white shadow-[0_6px_16px_rgb(31_111_84_/_0.25)]'
                        : 'text-ink-soft hover:bg-surface hover:text-ink'"
                    :aria-current="number === page ? 'page' : undefined"
                    @click="goTo(number)"
                >
                    {{ number }}
                </button>
            </template>

            <button
                type="button"
                class="flex size-9 cursor-pointer items-center justify-center rounded-lg text-ink-soft transition hover:bg-surface hover:text-ink disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-transparent"
                :disabled="page === totalPages"
                aria-label="Next page"
                @click="goTo(page + 1)"
            >
                <svg class="size-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7.5 5l5 5-5 5" />
                </svg>
            </button>
        </div>
    </nav>
</template>
