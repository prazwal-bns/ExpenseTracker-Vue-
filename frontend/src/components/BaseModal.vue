<script setup>
import { onMounted, onUnmounted } from 'vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    title: { type: String, default: '' },
    eyebrow: { type: String, default: '' },
    description: { type: String, default: '' },
})

const emit = defineEmits(['close']);

function handleEscape(event) {
    if (event.key === 'Escape' && props.open) emit('close');
}

onMounted(() => document.addEventListener('keydown', handleEscape));
onUnmounted(() => document.removeEventListener('keydown', handleEscape));

</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 px-4 py-8 backdrop-blur-sm"
                @click.self="emit('close')"
            >
                <div
                    class="relative w-full max-w-lg overflow-hidden rounded-3xl border border-ink/10 bg-fog shadow-[0_30px_80px_rgb(16_42_36_/_0.25)]"
                    role="dialog"
                    aria-modal="true"
                    :aria-label="title"
                >
                    <div class="h-1.5 bg-linear-to-r from-leaf via-leaf-deep to-amber" />

                    <div class="flex items-start justify-between gap-4 px-6 pt-6 sm:px-8 sm:pt-7">
                        <div>
                            <p
                                v-if="eyebrow"
                                class="text-xs font-medium tracking-[0.18em] text-leaf uppercase"
                            >
                                {{ eyebrow }}
                            </p>
                            <h2
                                v-if="title"
                                class="font-display text-2xl font-medium text-ink"
                                :class="eyebrow ? 'mt-1.5' : ''"
                            >
                                {{ title }}
                            </h2>
                            <p
                                v-if="description"
                                class="mt-2 text-sm leading-relaxed text-ink-soft"
                            >
                                {{ description }}
                            </p>
                        </div>

                        <button
                            type="button"
                            class="-mr-2 -mt-1 shrink-0 cursor-pointer rounded-xl p-2 text-ink-soft transition hover:bg-white hover:text-ink"
                            aria-label="Close"
                            @click="emit('close')"
                        >
                            <svg
                                class="size-5"
                                viewBox="0 0 20 20"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.75"
                                stroke-linecap="round"
                            >
                                <path d="M5 5l10 10M15 5L5 15" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 pt-6 pb-6 sm:px-8 sm:pb-8">
                        <slot />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
