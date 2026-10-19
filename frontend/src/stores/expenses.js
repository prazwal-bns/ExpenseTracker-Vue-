import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import * as expenseApi from '../api/expense'
import { useAuthStore } from './auth'
import { useCategoryStore } from './categories'
import { readCollectionCache, writeCollectionCache } from '../utils/collectionCache'

const CACHE_NAME = 'expenses'

export const useExpenseStore = defineStore('expenses', () => {
    const expenses = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const error = ref('')
    const loaded = ref(false)
    const etag = ref('')
    let request = null

    watch(() => useAuthStore().user?.id, reset)

    function reset() {
        expenses.value = []
        loaded.value = false
        etag.value = ''
        error.value = ''
    }

    function remember() {
        writeCollectionCache(CACHE_NAME, useAuthStore().user?.id, expenses.value, etag.value)
    }

    function markStale() {
        loaded.value = false
    }

    async function fetchExpenses() {
        if (loaded.value) return
        if (request) return request

        request = loadExpenses().finally(() => {
            request = null
        })

        return request
    }

    async function loadExpenses() {
        const userId = useAuthStore().user?.id
        const cached = readCollectionCache(CACHE_NAME, userId)

        if (cached) {
            expenses.value = cached.items
            etag.value = cached.etag
        }

        loading.value = !cached
        error.value = ''

        try {
            const result = await expenseApi.getExpenses(etag.value)
            if (useAuthStore().user?.id !== userId) return

            if (!result.unchanged) {
                expenses.value = sortNewestFirst(result.data)
                etag.value = result.etag ?? ''
                remember()
            }

            loaded.value = true
        } catch (err) {
            if (useAuthStore().user?.id !== userId) return
            if (expenses.value.length === 0) error.value = err.message
        } finally {
            loading.value = false
        }
    }

    function sortNewestFirst(list) {
        return [...list].sort(
            (first, second) => second.spent_at.localeCompare(first.spent_at) || second.id - first.id
        )
    }

    async function addExpense(expense) {
        saving.value = true
        try {
            const created = await expenseApi.createExpense(expense)
            expenses.value = sortNewestFirst([created, ...expenses.value])
            etag.value = ''
            remember()
            useCategoryStore().markStale()
        } finally {
            saving.value = false
        }
    }

    async function updateExpense(id, expense) {
        saving.value = true
        try {
            const updated = await expenseApi.updateExpense(id, expense)
            expenses.value = sortNewestFirst(
                expenses.value.map((existing) => (existing.id === id ? updated : existing))
            )
            etag.value = ''
            remember()
            useCategoryStore().markStale()
        } finally {
            saving.value = false
        }
    }

    async function deleteExpense(id) {
        saving.value = true
        try {
            await expenseApi.deleteExpense(id)
            expenses.value = expenses.value.filter((expense) => expense.id !== id)
            etag.value = ''
            remember()
            useCategoryStore().markStale()
        } finally {
            saving.value = false
        }
    }

    return {
        expenses,
        loading,
        saving,
        error,
        loaded,
        fetchExpenses,
        markStale,
        addExpense,
        updateExpense,
        deleteExpense,
    }
})
