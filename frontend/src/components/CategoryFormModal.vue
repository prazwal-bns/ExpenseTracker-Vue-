<script setup>
import { watch, ref } from 'vue';
import { useCategoryStore } from '../stores/categories';
import BaseModal from './BaseModal.vue';
import ModalActions from './ModalActions.vue';

const props = defineProps({
    open: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'created'])

const categoryStore = useCategoryStore();

const colorPresets = ['#1F6F54', '#22C55E', '#0EA5E9', '#6366F1', '#E8843A', '#EF4444', '#EC4899', '#A16207'];

const name = ref('');
const color = ref('#22C55E');
const description = ref('');

watch(() => props.open, (isOpen) => {
    if (!isOpen) {
        name.value = '';
        color.value = '#22C55E';
        description.value = '';
        categoryStore.error = '';
    }
})

async function handleSubmit() {
    try {
        await categoryStore.addCategory({
            name: name.value,
            color: color.value,
            description: description.value,
        })
        emit('created');
        emit('close');
    } catch (error) {
        categoryStore.error = error.message;
    }
}
</script>

<template>
    <BaseModal
        :open="open"
        eyebrow="Categories"
        title="New category"
        description="Group similar spending together so it's easier to track."
        @close="emit('close')"
    >
        <form class="flex flex-col gap-5" @submit.prevent="handleSubmit">
            <div class="flex items-center gap-4 rounded-2xl border border-ink/8 bg-white/90 px-4 py-3.5">
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
                    class="rounded-xl border border-ink/15 bg-white px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:border-leaf focus:ring-2 focus:ring-leaf/20"
                >
            </div>

            <div class="flex flex-col gap-2">
                <span class="text-sm font-medium text-ink">Color</span>
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-for="preset in colorPresets"
                        :key="preset"
                        type="button"
                        class="size-8 cursor-pointer rounded-full border-2 border-white shadow-sm ring-offset-2 ring-offset-fog transition hover:scale-110"
                        :class="color.toUpperCase() === preset ? 'ring-2 ring-ink' : ''"
                        :style="{ backgroundColor: preset }"
                        :aria-label="`Use color ${preset}`"
                        @click="color = preset"
                    />

                    <label
                        for="category-color"
                        class="ml-1 flex cursor-pointer items-center gap-2 rounded-full border border-ink/15 bg-white py-1 pr-3 pl-1 text-xs font-medium text-ink-soft transition hover:border-ink/30"
                    >
                        <input
                            id="category-color"
                            v-model="color"
                            type="color"
                            class="size-6 cursor-pointer appearance-none rounded-full border-0 bg-transparent p-0 [&::-webkit-color-swatch]:rounded-full [&::-webkit-color-swatch]:border-0 [&::-webkit-color-swatch-wrapper]:p-0"
                        >
                        <span class="font-mono uppercase">{{ color }}</span>
                    </label>
                </div>
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
                    class="resize-none rounded-xl border border-ink/15 bg-white px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:border-leaf focus:ring-2 focus:ring-leaf/20"
                />
            </div>

            <p
                v-if="categoryStore.error"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                role="alert"
            >
                {{ categoryStore.error }}
            </p>

            <ModalActions
                :loading="categoryStore.loading"
                submit-label="Create category"
                @cancel="emit('close')"
            />
        </form>
    </BaseModal>
</template>
