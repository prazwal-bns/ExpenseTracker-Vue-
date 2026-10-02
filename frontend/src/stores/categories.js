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
            expenseCategories.value = [...expenseCategories.value, created]
                .sort((first, second) => first.name.localeCompare(second.name))
        } finally {
            saving.value = false
        }
    }



    // todo update category


    // todo delete category

    return {
        expenseCategories,
        loading,
        saving,
        error,
        fetchCategories,
        addCategory
    }
})