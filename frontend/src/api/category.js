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

export async function updateCategory(id, category){
    return await apiFetch(`/categories/${id}`, {
        method: 'PUT',
        body: JSON.stringify(category)
    })
}

export async function deleteCategory(id){
    return await apiFetch(`/categories/${id}`, {
        method: 'DELETE'
    })
}