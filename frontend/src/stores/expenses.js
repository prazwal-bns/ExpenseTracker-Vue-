import { defineStore } from "pinia";
import { ref } from "vue";
import * as expenseApi from '../api/expense'


export const useExpenseStore = defineStore('expenses', () => {
    const expenses = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const error = ref('')

    async function fetchExpenses(){
        loading.value = true
        error.value = ''
        try{
            expenses.value = await expenseApi.getExpenses()
        } catch (err) {
            error.value = err.message;
        } finally {
            loading.value = false
        }
    }



    return {
        expenses,
        loading,
        saving,
        error,
        fetchExpenses,
    }
})