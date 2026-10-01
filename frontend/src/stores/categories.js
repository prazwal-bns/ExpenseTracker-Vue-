import { defineStore } from 'pinia'
import { ref } from 'vue'
import * as categoryApi from '../api/category'



export const useCategoryStore = defineStore('categories', () => {
    const expenseCategories = ref([])
    const loading = ref(false)
    const error = ref('')

    async function fetchCategories(){
        loading.value = true
        error.value = ''
        try{
            expenseCategories.value = await categoryApi.getCategories();
            console.log(expenseCategories.value)
        } catch (err) {
            error.value = err.message;
            console.error('Failed to fetch categories:', err)
        } finally {
            loading.value = false
        }
    }

    // todo create category


    // todo update category


    // todo delete category

    return {
        expenseCategories,
        loading,
        error,
        fetchCategories
    }
})