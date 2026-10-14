import { apiFetch } from './client'

export async function getProfile(){
    return await apiFetch('/profile')
}

export async function updateProfile(profile){
    return await apiFetch('/profile', {
        method: 'PUT',
        body: JSON.stringify(profile)
    })
}

export async function updatePassword(passwords){
    return await apiFetch('/profile/password', {
        method: 'PUT',
        body: JSON.stringify(passwords)
    })
}

export async function deleteAccount(password){
    return await apiFetch('/profile', {
        method: 'DELETE',
        body: JSON.stringify({ password })
    })
}
