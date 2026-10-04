<script setup>
import { computed } from 'vue'

const props = defineProps({
    label: { type: String, required: true },
    icon: {
        type: String,
        default: '',
        validator: (value) => ['', 'edit', 'delete', 'view', 'add'].includes(value),
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'danger'].includes(value),
    },
    ariaLabel: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
})

const iconPaths = {
    edit: 'M13.5 3.5l3 3L7 16H4v-3l9.5-9.5Z',
    delete: 'M3.5 5.5h13M8 5.5V4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1.5M5 5.5l.8 10.6a1 1 0 0 0 1 .9h6.4a1 1 0 0 0 1-.9L15 5.5M8.5 9v4.5M11.5 9v4.5',
    view: 'M1.75 10S4.5 4.5 10 4.5 18.25 10 18.25 10 15.5 15.5 10 15.5 1.75 10 1.75 10ZM10 12.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z',
    add: 'M10 4.5v11M4.5 10h11',
}

const variantClasses = {
    default: 'hover:bg-leaf/10 hover:text-leaf lg:hover:border-leaf/30',
    danger: 'hover:bg-red-50 hover:text-red-600 lg:hover:border-red-200',
}

const iconPath = computed(() => iconPaths[props.icon] ?? '')
</script>

<template>
    <button
        type="button"
        class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg p-2 text-sm font-medium text-ink-soft transition disabled:cursor-not-allowed disabled:opacity-50 lg:border lg:border-ink/10 lg:bg-white lg:px-3 lg:py-1.5"
        :class="variantClasses[variant]"
        :aria-label="ariaLabel || label"
        :title="label"
        :disabled="disabled"
    >
        <svg
            v-if="iconPath"
            class="size-4 shrink-0"
            viewBox="0 0 20 20"
            fill="none"
            stroke="currentColor"
            stroke-width="1.75"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path :d="iconPath" />
        </svg>
        <span :class="iconPath ? 'hidden lg:inline' : ''">{{ label }}</span>
    </button>
</template>
