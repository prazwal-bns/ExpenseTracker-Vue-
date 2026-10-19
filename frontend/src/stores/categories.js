import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import * as categoryApi from '../api/category'
import { useAuthStore } from './auth'
import { useExpenseStore } from './expenses'
import { readCollectionCache, writeCollectionCache } from '../utils/collectionCache'

const CACHE_NAME = 'categories'

export const useCategoryStore = defineStore('categories', () => {
    const expenseCategories = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const error = ref('')
    const loaded = ref(false)
    const etag = ref('')
    let request = null

    watch(() => useAuthStore().user?.id, reset)

    function reset() {
        expenseCategories.value = []
        loaded.value = false
        etag.value = ''
        error.value = ''
    }

    function remember() {
        writeCollectionCache(CACHE_NAME, useAuthStore().user?.id, expenseCategories.value, etag.value)
    }

    function markStale() {
        loaded.value = false
    }

    async function fetchCategories() {
        if (loaded.value) return
        if (request) return request

        request = loadCategories().finally(() => {
            request = null
        })

        return request
    }

    async function loadCategories() {
        const userId = useAuthStore().user?.id
        const cached = readCollectionCache(CACHE_NAME, userId)

        if (cached) {
            expenseCategories.value = cached.items
            etag.value = cached.etag
        }

        loading.value = !cached
        error.value = ''

        try {
            const result = await categoryApi.getCategories(etag.value)
            if (useAuthStore().user?.id !== userId) return

            if (!result.unchanged) {
                expenseCategories.value = result.data
                etag.value = result.etag ?? ''
                remember()
            }

            loaded.value = true
        } catch (err) {
            if (useAuthStore().user?.id !== userId) return
            if (expenseCategories.value.length === 0) error.value = err.message
        } finally {
            loading.value = false
        }
    }

    async function addCategory(category) {
        saving.value = true
        try {
            const created = await categoryApi.createCategory(category)
            expenseCategories.value = [created, ...expenseCategories.value]
            etag.value = ''
            remember()
            useExpenseStore().markStale()
        } finally {
            saving.value = false
        }
    }

    async function updateCategory(id, category) {
        saving.value = true
        try {
            const updated = await categoryApi.updateCategory(id, category)
            expenseCategories.value = expenseCategories.value.map(
                (existing) => (existing.id === id ? updated : existing)
            )
            etag.value = ''
            remember()
            useExpenseStore().markStale()
        } finally {
            saving.value = false
        }
    }

    async function deleteCategory(id) {
        saving.value = true
        try {
            await categoryApi.deleteCategory(id)
            expenseCategories.value = expenseCategories.value.filter(
                (category) => category.id !== id
            )
            etag.value = ''
            remember()
            useExpenseStore().markStale()
        } finally {
            saving.value = false
        }
    }

    return {
        expenseCategories,
        loading,
        saving,
        error,
        loaded,
        fetchCategories,
        markStale,
        addCategory,
        updateCategory,
        deleteCategory,
    }
})
