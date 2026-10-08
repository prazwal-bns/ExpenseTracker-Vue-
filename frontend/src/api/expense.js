import { apiFetch } from './client'

export async function getExpenses(){
    return await apiFetch('/expenses')
}

export async function createExpense(expense){
    return await apiFetch('/expenses', {
        method: 'POST',
        body: JSON.stringify(expense)
    })
}

export async function updateExpense(id, expense){
    return await apiFetch(`/expenses/${id}`, {
        method: 'PUT',
        body: JSON.stringify(expense)
    })
}

export async function deleteExpense(id){
    return await apiFetch(`/expenses/${id}`, {
        method: 'DELETE'
    })
}
