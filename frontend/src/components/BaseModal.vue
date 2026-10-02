<script setup>
import { onMounted, onUnmounted } from 'vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    title: { type: String, default: '' },
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
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
            @click.self="emit('close')">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 v-if="title" class="font-display text-xl font-semibold text-ink">
                    {{ title }}
                </h2>

                <div :class="title ? 'mt-6' : ''">
                    <slot />
                </div>
            </div>
        </div>
    </Teleport>
</template>