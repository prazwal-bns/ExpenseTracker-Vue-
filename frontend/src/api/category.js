import { apiFetch } from './client'     

export async function getCategories(){
    return await apiFetch('/categories')
}

export async function createCategory(category){
    return await apiFetch('/categories', {
        method: 'POST',
        body: JSON.stringify(category)
    })
}