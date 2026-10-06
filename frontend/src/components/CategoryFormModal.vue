<script setup>
import { computed, watch, ref } from 'vue';
import { useCategoryStore } from '../stores/categories';
import BaseModal from './BaseModal.vue';
import ModalActions from './ModalActions.vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    editing: { type: Object, default: null },
})

const emit = defineEmits(['close', 'created', 'updated'])

const categoryStore = useCategoryStore();

const colorPresets = ['#1F6F54', '#22C55E', '#0EA5E9', '#6366F1', '#E8843A', '#EF4444', '#EC4899', '#A16207'];
const DEFAULT_COLOR = '#22C55E';
const HEX_PATTERN = /^#[0-9A-F]{6}$/;

const name = ref('');
const color = ref(DEFAULT_COLOR);
const hexInput = ref(DEFAULT_COLOR);
const description = ref('');

const isCustomColor = computed(() => !colorPresets.includes(color.value));
const isHexInputValid = computed(() => HEX_PATTERN.test(normalizeHex(hexInput.value)));

function normalizeHex(value) {
    const hex = value.trim().toUpperCase();
    return hex.startsWith('#') ? hex : `#${hex}`;
}

function setColor(value) {
    color.value = normalizeHex(value);
    hexInput.value = color.value;
}

function handleHexInput() {
    if (isHexInputValid.value) color.value = normalizeHex(hexInput.value);
}

function resetHexInput() {
    hexInput.value = color.value;
}
const errorMessage = ref('');
const fieldErrors = ref({});

const inputClass = 'rounded-xl border bg-surface px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:ring-2';
const validInputClass = 'border-ink/15 focus:border-leaf focus:ring-leaf/20';
const invalidInputClass = 'border-red-300 focus:border-red-400 focus:ring-red-200 dark:border-red-500/50 dark:focus:ring-red-500/30';

watch(
  () => props.open,
  (isOpen) => {
    if (!isOpen) return

    if (props.editing) {
      name.value = props.editing.name ?? ''
      setColor(props.editing.color ?? DEFAULT_COLOR)
      description.value = props.editing.description ?? ''
    } else {
      name.value = ''
      setColor(DEFAULT_COLOR)
      description.value = ''
    }
    errorMessage.value = ''
    fieldErrors.value = {}
  },
  { immediate: true }
)

async function handleSubmit() {
    errorMessage.value = '';
    fieldErrors.value = {};

  const payload = {
    name: name.value,
    color: color.value,
    description: description.value,
  }

  try {
    if (props.editing) {
      await categoryStore.updateCategory(props.editing.id, payload)
      emit('updated')
    } else {
      await categoryStore.addCategory(payload)
      emit('created')
    }
    emit('close')
  } catch (error) {
        fieldErrors.value = error.errors ?? {};
    if (!Object.keys(fieldErrors.value).length) {
            errorMessage.value = error.message
                || `Could not ${props.editing ? 'update' : 'create'} the category. Please try again.`;
    }
  }
}
</script>

<template>
    <BaseModal
    :open="open"
    size="lg"
    eyebrow="Categories"
    :title="editing ? 'Edit category' : 'New category'"
    :description="editing
        ? 'Update the details of this category.'
        : 'Group similar spending together so it\'s easier to track.'"
    @close="emit('close')"
    >
        <form class="flex flex-col gap-5" @submit.prevent="handleSubmit">
            <div class="flex items-center gap-4 rounded-2xl border border-ink/8 bg-surface/90 px-4 py-3.5">
                <span
                    class="size-11 shrink-0 rounded-xl border border-ink/5 shadow-inner transition-colors"
                    :style="{ backgroundColor: color }"
                />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-ink">
                        {{ name || 'Category name' }}
                    </p>
                    <p class="mt-0.5 truncate text-xs text-ink-soft">
                        {{ description || 'No description' }}
                    </p>
                </div>
                <span class="shrink-0 rounded-full bg-fog px-2.5 py-1 text-[11px] font-medium tracking-wide text-ink-soft uppercase">
                    Preview
                </span>
            </div>

            <div class="flex flex-col gap-2">
                <label for="category-name" class="text-sm font-medium text-ink">
                    Name
                </label>
                <input
                    id="category-name"
                    v-model="name"
                    type="text"
                    required
                    maxlength="255"
                    autocomplete="off"
                    placeholder="e.g. Groceries"
                    :class="[inputClass, fieldErrors.name ? invalidInputClass : validInputClass]"
                    :aria-invalid="!!fieldErrors.name"
                    aria-describedby="category-name-error"
                >
                <p
                    v-if="fieldErrors.name"
                    id="category-name-error"
                    class="text-xs font-medium text-red-600 dark:text-red-400"
                >
                    {{ fieldErrors.name[0] }}
                </p>
            </div>

            <div class="flex flex-col gap-3">
                <span class="text-sm font-medium text-ink">Color</span>

                <div class="flex flex-wrap items-center gap-2.5" role="radiogroup" aria-label="Category color">
                    <button
                        v-for="preset in colorPresets"
                        :key="preset"
                        type="button"
                        role="radio"
                        class="flex size-9 cursor-pointer items-center justify-center rounded-full shadow-sm ring-offset-2 ring-offset-fog transition hover:scale-110 focus-visible:ring-2 focus-visible:ring-leaf/60 focus-visible:outline-none"
                        :class="color === preset ? 'scale-105 ring-2 ring-leaf' : ''"
                        :style="{ backgroundColor: preset }"
                        :aria-checked="color === preset"
                        :aria-label="`Use color ${preset}`"
                        @click="setColor(preset)"
                    >
                        <svg
                            v-if="color === preset"
                            class="size-4 text-white drop-shadow-[0_1px_1px_rgb(0_0_0_/_0.35)]"
                            viewBox="0 0 20 20"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M4.5 10.5l3.5 3.5 7.5-8" />
                        </svg>
                    </button>

                    <span class="mx-1 h-6 w-px bg-ink/10" aria-hidden="true" />

                    <label
                        class="relative flex size-9 cursor-pointer items-center justify-center rounded-full shadow-sm ring-offset-2 ring-offset-fog transition hover:scale-110 focus-within:ring-2 focus-within:ring-leaf/60"
                        :class="isCustomColor ? 'scale-105 ring-2 ring-leaf' : ''"
                        :style="{
                            background: isCustomColor
                                ? color
                                : 'conic-gradient(#ef4444, #f59e0b, #22c55e, #0ea5e9, #6366f1, #ec4899, #ef4444)',
                        }"
                        title="Pick a custom color"
                    >
                        <input
                            type="color"
                            :value="color"
                            class="absolute inset-0 size-full cursor-pointer rounded-full opacity-0"
                            aria-label="Pick a custom color"
                            @input="setColor($event.target.value)"
                        >
                        <svg
                            class="pointer-events-none size-4 text-white drop-shadow-[0_1px_1px_rgb(0_0_0_/_0.35)]"
                            viewBox="0 0 20 20"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path v-if="isCustomColor" d="M4.5 10.5l3.5 3.5 7.5-8" />
                            <path v-else d="M10 4.5v11M4.5 10h11" />
                        </svg>
                    </label>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative w-36">
                        <span
                            class="pointer-events-none absolute top-1/2 left-3 size-5 -translate-y-1/2 rounded-md border border-ink/10"
                            :style="{ backgroundColor: color }"
                        />
                        <input
                            v-model="hexInput"
                            type="text"
                            maxlength="7"
                            spellcheck="false"
                            autocomplete="off"
                            aria-label="Hex color code"
                            class="w-full rounded-xl border bg-surface py-2.5 pr-3 pl-10 font-mono text-sm text-ink uppercase outline-none transition focus:ring-2"
                            :class="isHexInputValid ? validInputClass : invalidInputClass"
                            @input="handleHexInput"
                            @blur="resetHexInput"
                        >
                    </div>
                    <p class="text-xs text-ink-soft">
                        {{ isHexInputValid ? 'Pick a preset, choose a custom shade, or type a hex code.' : 'Use a 6-digit hex code like #22C55E.' }}
                    </p>
                </div>
                <p
                    v-if="fieldErrors.color"
                    class="text-xs font-medium text-red-600 dark:text-red-400"
                >
                    {{ fieldErrors.color[0] }}
                </p>
            </div>

            <div class="flex flex-col gap-2">
                <label for="category-description" class="flex items-baseline justify-between text-sm font-medium text-ink">
                    Description
                    <span class="text-xs font-normal text-ink-soft">Optional</span>
                </label>
                <textarea
                    id="category-description"
                    v-model="description"
                    rows="3"
                    placeholder="What kind of spending goes here?"
                    class="resize-none"
                    :class="[inputClass, fieldErrors.description ? invalidInputClass : validInputClass]"
                />
                <p
                    v-if="fieldErrors.description"
                    class="text-xs font-medium text-red-600 dark:text-red-400"
                >
                    {{ fieldErrors.description[0] }}
                </p>
            </div>

            <div
                v-if="errorMessage"
                class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300"
                role="alert"
            >
                <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                </svg>
                <p>{{ errorMessage }}</p>
            </div>

            <ModalActions
                :loading="categoryStore.saving"
                :submit-label="editing ? 'Update category' : 'Create category'"
                @cancel="emit('close')"
            />
        </form>
    </BaseModal>
</template>
