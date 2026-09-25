import { apiFetch } from './client'     

export async function getCategories(){
    return await apiFetch('/categories')
}