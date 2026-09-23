const API_URL = import.meta.env.VITE_API_URL        
export async function getCategories(){
    const token = localStorage.getItem('token')
    const response = await fetch(`${API_URL}/categories`, {
        headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
        }
    });
    if (response.status === 204) return null

    const data = await response.json()

    if (!response.ok) {
        throw new Error(data.message || 'Failed to fetch categories')
    }


    return data.data
}