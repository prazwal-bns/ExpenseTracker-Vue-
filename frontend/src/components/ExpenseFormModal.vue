<script setup>
import { computed, ref, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { useExpenseStore } from '../stores/expenses'
import { useCategoryStore } from '../stores/categories'
import BaseModal from './BaseModal.vue'
import ModalActions from './ModalActions.vue'

const props = defineProps({
    open: { type: Boolean, default: false },
    editing: { type: Object, default: null },
})

const emit = defineEmits(['close', 'created', 'updated'])

const expenseStore = useExpenseStore()
const categoryStore = useCategoryStore()

const today = () => new Date().toISOString().split('T')[0]

const title = ref('')
const amount = ref('')
const categoryId = ref(null)
const notes = ref('')
const spentAt = ref(today())
const fieldErrors = ref({})
const errorMessage = ref('')

const inputClass = 'w-full rounded-xl border bg-surface px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:ring-2'
const validInputClass = 'border-ink/15 focus:border-leaf focus:ring-leaf/20'
const invalidInputClass = 'border-red-300 focus:border-red-400 focus:ring-red-200 dark:border-red-500/50 dark:focus:ring-red-500/30'

const selectedCategory = computed(() =>
    categoryStore.expenseCategories.find((category) => category.id === categoryId.value)
)

const currency = new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 })
const previewAmount = computed(() => `Rs ${currency.format(Number(amount.value) || 0)}`)
const previewDate = computed(() =>
    spentAt.value
        ? new Date(`${spentAt.value}T00:00:00`).toLocaleDateString('en', { month: 'short', day: 'numeric', year: 'numeric' })
        : 'No date'
)

watch(() => props.open, (isOpen) => {
    if (!isOpen) return
    if (!categoryStore.expenseCategories.length) categoryStore.fetchCategories()

    if (props.editing) {
        title.value = props.editing.title ?? ''
        amount.value = props.editing.amount ?? ''
        categoryId.value = props.editing.category_id ?? null
        spentAt.value = props.editing.spent_at ?? today()
        notes.value = props.editing.notes ?? ''
    } else {
        title.value = ''
        amount.value = ''
        categoryId.value = null
        spentAt.value = today()
        notes.value = ''
    }
    fieldErrors.value = {}
    errorMessage.value = ''
}, { immediate: true })

async function handleSubmit() {
}
</script>

<template>
    <BaseModal
        :open="open"
        size="lg"
        eyebrow="Expenses"
        :title="editing ? 'Edit expense' : 'New expense'"
        :description="editing
            ? 'Update the details of this expense.'
            : 'Log what you spent so your totals stay accurate.'"
        @close="emit('close')"
    >
        <form class="flex flex-col gap-5" @submit.prevent="handleSubmit">
            <div class="flex items-center gap-4 rounded-2xl border border-ink/8 bg-surface/90 px-4 py-3.5">
                <span
                    class="size-11 shrink-0 rounded-xl border border-ink/5 shadow-inner transition-colors"
                    :style="{ backgroundColor: selectedCategory?.color || '#1f6f54' }"
                />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-ink">
                        {{ title || 'Expense title' }}
                    </p>
                    <p class="mt-0.5 truncate text-xs text-ink-soft">
                        {{ selectedCategory?.name || 'No category' }} · {{ previewDate }}
                    </p>
                </div>
                <p class="shrink-0 font-display text-lg font-semibold text-ink">
                    {{ previewAmount }}
                </p>
            </div>

            <div class="flex flex-col gap-2">
                <label for="expense-title" class="text-sm font-medium text-ink">
                    Title
                </label>
                <input
                    id="expense-title"
                    v-model="title"
                    type="text"
                    required
                    maxlength="255"
                    autocomplete="off"
                    placeholder="e.g. Grocery haul"
                    :class="[inputClass, fieldErrors.title ? invalidInputClass : validInputClass]"
                    :aria-invalid="!!fieldErrors.title"
                >
                <p
                    v-if="fieldErrors.title"
                    class="text-xs font-medium text-red-600 dark:text-red-400"
                >
                    {{ fieldErrors.title[0] }}
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="expense-amount" class="text-sm font-medium text-ink">
                        Amount
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-sm font-semibold text-ink-soft">
                            Rs
                        </span>
                        <input
                            id="expense-amount"
                            v-model="amount"
                            type="number"
                            required
                            min="0.01"
                            step="0.01"
                            inputmode="decimal"
                            placeholder="0.00"
                            class="pl-11!"
                            :class="[inputClass, fieldErrors.amount ? invalidInputClass : validInputClass]"
                            :aria-invalid="!!fieldErrors.amount"
                        >
                    </div>
                    <p
                        v-if="fieldErrors.amount"
                        class="text-xs font-medium text-red-600 dark:text-red-400"
                    >
                        {{ fieldErrors.amount[0] }}
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="expense-spent-at" class="text-sm font-medium text-ink">
                        Date
                    </label>
                    <input
                        id="expense-spent-at"
                        v-model="spentAt"
                        type="date"
                        required
                        :max="today()"
                        :class="[inputClass, fieldErrors.spent_at ? invalidInputClass : validInputClass]"
                        :aria-invalid="!!fieldErrors.spent_at"
                    >
                    <p
                        v-if="fieldErrors.spent_at"
                        class="text-xs font-medium text-red-600 dark:text-red-400"
                    >
                        {{ fieldErrors.spent_at[0] }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <span class="text-sm font-medium text-ink">Category</span>

                <div
                    v-if="categoryStore.loading"
                    class="flex items-center gap-2 text-xs text-ink-soft"
                >
                    <span class="size-4 animate-spin rounded-full border-2 border-leaf/20 border-t-leaf" />
                    Loading categories…
                </div>

                <div
                    v-else-if="categoryStore.expenseCategories.length"
                    class="flex flex-wrap gap-2"
                    role="radiogroup"
                    aria-label="Expense category"
                >
                    <button
                        v-for="category in categoryStore.expenseCategories"
                        :key="category.id"
                        type="button"
                        role="radio"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-3 py-1.5 text-sm font-medium transition"
                        :class="categoryId === category.id
                            ? 'border-leaf bg-leaf/10 text-leaf ring-2 ring-leaf/20'
                            : 'border-ink/15 bg-surface text-ink-soft hover:border-ink/30 hover:text-ink'"
                        :aria-checked="categoryId === category.id"
                        @click="categoryId = category.id"
                    >
                        <span
                            class="size-2.5 shrink-0 rounded-full"
                            :style="{ backgroundColor: category.color || '#1f6f54' }"
                        />
                        {{ category.name }}
                    </button>
                </div>

                <div
                    v-else
                    class="rounded-xl border border-dashed border-ink/15 bg-fog/60 px-4 py-3 text-sm text-ink-soft"
                >
                    No categories yet.
                    <RouterLink
                        :to="{ name: 'expense-categories' }"
                        class="font-semibold text-leaf hover:text-leaf-deep"
                    >
                        Create one first →
                    </RouterLink>
                </div>

                <p
                    v-if="fieldErrors.category_id"
                    class="text-xs font-medium text-red-600 dark:text-red-400"
                >
                    {{ fieldErrors.category_id[0] }}
                </p>
            </div>

            <div class="flex flex-col gap-2">
                <label for="expense-notes" class="flex items-baseline justify-between text-sm font-medium text-ink">
                    Notes
                    <span class="text-xs font-normal text-ink-soft">Optional</span>
                </label>
                <textarea
                    id="expense-notes"
                    v-model="notes"
                    rows="3"
                    placeholder="Add any additional details"
                    class="resize-none"
                    :class="[inputClass, fieldErrors.notes ? invalidInputClass : validInputClass]"
                />
                <p
                    v-if="fieldErrors.notes"
                    class="text-xs font-medium text-red-600 dark:text-red-400"
                >
                    {{ fieldErrors.notes[0] }}
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
                :loading="expenseStore.saving"
                :submit-label="editing ? 'Update expense' : 'Create expense'"
                @cancel="emit('close')"
            />
        </form>
    </BaseModal>
</template>
