<script setup>
import { watch, ref } from 'vue';
import { useCategoryStore } from '../stores/categories';
import BaseModal from './BaseModal.vue';

const props = defineProps({
    open: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'created'])

const categoryStore = useCategoryStore();

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
    <BaseModal :open="open" title="New category" @close="emit('close')">
        <form class="flex flex-col gap-4" @submit.prevent="handleSubmit">
            <div>
                <label for="name" class="block text-sm font-medium text-ink">Name</label>
                <input type="text" id="name" v-model="name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-leaf focus:ring-leaf sm:text-sm"
                    placeholder="e.g. Groceries">
            </div>
            <div>
                <label for="color" class="block text-sm font-medium text-ink">Color</label>
                <input type="color" id="color" v-model="color"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-leaf focus:ring-leaf sm:text-sm">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-ink">Description</label>
                <textarea id="description" v-model="description"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-leaf focus:ring-leaf sm:text-sm"></textarea>
            </div>

            <div class="mt-2 flex justify-end gap-2">
                <button type="button" class="..." @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="categoryStore.loading" class="...">
                    {{ categoryStore.loading ? 'Saving…' : 'Create' }}
                </button>
            </div>
        </form>
    </BaseModal>
</template>