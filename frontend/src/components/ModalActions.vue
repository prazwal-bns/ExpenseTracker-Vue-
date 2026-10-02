<script setup>
defineProps({
    loading: { type: Boolean, default: false },
    submitLabel: { type: String, default: 'Save' },
    cancelLabel: { type: String, default: 'Cancel' },
})
const emit = defineEmits(['cancel'])
</script>

<template>
    <div class="mt-2 flex justify-end gap-2">
        <button type="button" class="rounded-xl px-4 py-2 text-sm font-medium text-ink-soft hover:text-ink"
            @click="emit('cancel')">
            {{ cancelLabel }}
        </button>
        <button type="submit" :disabled="loading"
            class="rounded-xl bg-leaf px-4 py-2 text-sm font-semibold text-white transition hover:bg-leaf-deep disabled:cursor-not-allowed disabled:opacity-60">
            {{ loading ? 'Saving…' : submitLabel }}
        </button>
    </div>
</template>