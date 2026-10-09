const currency = new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 })

export function formatAmount(amount) {
    return `Rs ${currency.format(Number(amount) || 0)}`
}

export function parseDate(date) {
    const [year, month, day] = date.split('-').map(Number)
    return new Date(year, month - 1, day)
}

export function formatDate(date) {
    return parseDate(date).toLocaleDateString('en', { month: 'short', day: 'numeric' })
}

export function formatRelativeDate(date) {
    const today = new Date()
    const startOfToday = new Date(today.getFullYear(), today.getMonth(), today.getDate())
    const daysAgo = Math.round((startOfToday - parseDate(date)) / 86_400_000)

    if (daysAgo === 0) return 'Today'
    if (daysAgo === 1) return 'Yesterday'
    return formatDate(date)
}
