import { apiFetch } from './client'

export async function getUsers(){
    return await apiFetch('/admin/users')
}

export async function createUser(user){
    return await apiFetch('/admin/users', {
        method: 'POST',
        body: JSON.stringify(user)
    })
}

export async function updateUser(id, user){
    return await apiFetch(`/admin/users/${id}`, {
        method: 'PUT',
        body: JSON.stringify(user)
    })
}

export async function deleteUser(id){
    return await apiFetch(`/admin/users/${id}`, {
        method: 'DELETE'
    })
}
