<script setup>
import { useThemeStore } from '../stores/theme'

const themeStore = useThemeStore()

const options = [
    { value: 'light', label: 'Light' },
    { value: 'dark', label: 'Dark' },
    { value: 'system', label: 'System' },
]
</script>

<template>
    <div
        class="inline-flex items-center gap-0.5 rounded-xl border border-ink/10 bg-surface/70 p-1 backdrop-blur-sm"
        role="radiogroup"
        aria-label="Color theme"
    >
        <button
            v-for="option in options"
            :key="option.value"
            type="button"
            role="radio"
            class="flex size-8 cursor-pointer items-center justify-center rounded-lg transition"
            :class="themeStore.preference === option.value
                ? 'bg-leaf text-white shadow-[0_4px_12px_rgb(31_111_84_/_0.25)]'
                : 'text-ink-soft hover:bg-surface hover:text-ink'"
            :aria-checked="themeStore.preference === option.value"
            :aria-label="`${option.label} theme`"
            :title="`${option.label} theme`"
            @click="themeStore.setTheme(option.value)"
        >
            <svg
                v-if="option.value === 'light'"
                class="size-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <circle cx="12" cy="12" r="4" />
                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
            </svg>
            <svg
                v-else-if="option.value === 'dark'"
                class="size-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
            </svg>
            <svg
                v-else
                class="size-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <rect x="2" y="4" width="20" height="13" rx="2" />
                <path d="M8 21h8M12 17v4" />
            </svg>
        </button>
    </div>
</template>
