<script setup>
defineProps({
    loading: { type: Boolean, default: false },
    submitLabel: { type: String, default: 'Save' },
    cancelLabel: { type: String, default: 'Cancel' },
})
const emit = defineEmits(['cancel'])
</script>

<template>
    <div class="mt-2 flex flex-col-reverse gap-2 border-t border-ink/8 pt-5 sm:flex-row sm:justify-end">
        <button
            type="button"
            class="cursor-pointer rounded-xl border border-ink/15 bg-surface px-5 py-2.5 text-sm font-semibold text-ink-soft transition hover:border-ink/30 hover:text-ink"
            @click="emit('cancel')"
        >
            {{ cancelLabel }}
        </button>
        <button
            type="submit"
            :disabled="loading"
            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-leaf px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgb(31_111_84_/_0.25)] transition hover:bg-leaf-deep disabled:cursor-not-allowed disabled:opacity-60"
        >
            <span
                v-if="loading"
                class="size-4 animate-spin rounded-full border-2 border-white/30 border-t-white"
            />
            {{ loading ? 'Saving…' : submitLabel }}
        </button>
    </div>
</template>
