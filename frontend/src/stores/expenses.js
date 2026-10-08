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

    function sortNewestFirst(list){
        return [...list].sort(
            (first, second) => second.spent_at.localeCompare(first.spent_at) || second.id - first.id
        )
    }

    async function addExpense(expense){
        saving.value = true
        try{
            const created = await expenseApi.createExpense(expense)
            expenses.value = sortNewestFirst([created, ...expenses.value])
        } finally {
            saving.value = false
        }
    }

    async function updateExpense(id, expense){
        saving.value = true
        try{
            const updated = await expenseApi.updateExpense(id, expense)
            expenses.value = sortNewestFirst(
                expenses.value.map((existing) => (existing.id === id ? updated : existing))
            )
        } finally {
            saving.value = false
        }
    }

    return {
        expenses,
        loading,
        saving,
        error,
        fetchExpenses,
        addExpense,
        updateExpense,
    }
})