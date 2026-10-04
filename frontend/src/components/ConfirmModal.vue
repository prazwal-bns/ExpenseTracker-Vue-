<script setup>
import BaseModal from './BaseModal.vue'

defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: 'Are you sure?' },
  description: { type: String, default: '' },
  confirmLabel: { type: String, default: 'Confirm' },
  loadingLabel: { type: String, default: 'Deleting…' },
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
})

const emit = defineEmits(['close', 'confirm'])
</script>

<template>
  <BaseModal :open="open" @close="emit('close')">
    <div class="flex flex-col gap-5">
      <div class="flex items-start gap-4">
        <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 ring-1 ring-red-100">
          <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
          </svg>
        </span>
        <div class="min-w-0 pt-0.5">
          <h2 class="font-display text-2xl font-medium text-ink">
            {{ title }}
          </h2>
          <p
            v-if="description"
            class="mt-2 text-sm leading-relaxed text-ink-soft"
          >
            {{ description }}
          </p>
        </div>
      </div>

      <slot />

      <div
        v-if="error"
        class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
        role="alert"
      >
        <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
        </svg>
        <p>{{ error }}</p>
      </div>

      <div class="mt-1 flex flex-col-reverse gap-2 border-t border-ink/8 pt-5 sm:flex-row sm:justify-end">
        <button
          type="button"
          class="cursor-pointer rounded-xl border border-ink/15 bg-white px-5 py-2.5 text-sm font-semibold text-ink-soft transition hover:border-ink/30 hover:text-ink"
          @click="emit('close')"
        >
          Cancel
        </button>
        <button
          type="button"
          :disabled="loading || disabled"
          class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgb(220_38_38_/_0.22)] transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none"
          @click="emit('confirm')"
        >
          <span
            v-if="loading"
            class="size-4 animate-spin rounded-full border-2 border-white/30 border-t-white"
          />
          {{ loading ? loadingLabel : confirmLabel }}
        </button>
      </div>
    </div>
  </BaseModal>
</template>
