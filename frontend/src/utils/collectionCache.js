export function readCollectionCache(name, userId) {
    if (!userId) return null

    try {
        const parsed = JSON.parse(sessionStorage.getItem(storageKey(name, userId)) ?? 'null')
        if (!parsed || !Array.isArray(parsed.items)) return null

        return parsed
    } catch {
        return null
    }
}

export function writeCollectionCache(name, userId, items, etag) {
    if (!userId) return

    sessionStorage.setItem(storageKey(name, userId), JSON.stringify({
        items,
        etag: etag ?? '',
    }))
}

function storageKey(name, userId) {
    return `collection-cache:${name}:${userId}`
}
