import { defineStore } from 'pinia'
import { ref } from 'vue'
import * as categoryApi from '../api/category'



export const useCategoryStore = defineStore('categories', () => {
    const expenseCategories = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const error = ref('')

    async function fetchCategories(){
        loading.value = true
        error.value = ''
        try{
            expenseCategories.value = await categoryApi.getCategories();
        } catch (err) {
            error.value = err.message;
        } finally {
            loading.value = false
        }
    }

    async function addCategory(category){
        saving.value = true
        try{
            const created = await categoryApi.createCategory(category)
            expenseCategories.value = [created, ...expenseCategories.value]
        } finally {
            saving.value = false
        }
    }

    async function updateCategory(id, category){
        saving.value = true
        try{
            const updated = await categoryApi.updateCategory(id, category)
            expenseCategories.value = expenseCategories.value.map(
                (existing) => (existing.id === id ? updated : existing)
            )
        } finally {
            saving.value = false
        }
    }


    async function deleteCategory(id){
        saving.value = true
        error.value = ''
        try{
            await categoryApi.deleteCategory(id)
            expenseCategories.value = expenseCategories.value.filter(
                (category) => category.id !== id
                )          
        } catch (err) {
            error.value = err.message;
            throw err;
        } finally {
            saving.value = false
        }
    }

    return {
        expenseCategories,
        loading,
        saving,
        error,
        fetchCategories,
        addCategory,
        updateCategory,
        deleteCategory
    }
})