<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    hint: { type: String, default: '' },
    tone: {
        type: String,
        default: 'ink',
        validator: (value) => ['leaf', 'amber', 'ink'].includes(value),
    },
})

const toneClasses = {
    leaf: 'text-leaf',
    amber: 'text-amber-deep',
    ink: 'text-ink-soft',
}
</script>

<template>
    <article class="rounded-2xl border border-ink/10 bg-surface/70 p-5 shadow-[0_12px_32px_rgb(16_42_36_/_0.06)] backdrop-blur-sm">
        <p class="text-xs font-medium tracking-wide text-ink-soft uppercase">
            {{ label }}
        </p>
        <p class="mt-3 truncate font-display text-2xl font-semibold text-ink sm:text-3xl">
            {{ value }}
        </p>
        <p
            v-if="hint"
            class="mt-2 truncate text-xs font-medium"
            :class="toneClasses[tone]"
        >
            {{ hint }}
        </p>
    </article>
</template>
