import { apiFetch } from './client'

export async function getExpenses(){
    return await apiFetch('/expenses')
}