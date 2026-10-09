import { computed, toValue } from 'vue'

const monthKey = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`
const sumAmounts = (expenses) => expenses.reduce((total, expense) => total + Number(expense.amount), 0)

export function useExpenseStats(expenses) {
    const today = new Date()
    const thisMonthKey = monthKey(today)
    const lastMonthKey = monthKey(new Date(today.getFullYear(), today.getMonth() - 1, 1))

    const expensesInMonth = (key) => toValue(expenses).filter((expense) => expense.spent_at.startsWith(key))

    const thisMonthExpenses = computed(() => expensesInMonth(thisMonthKey))
    const spentThisMonth = computed(() => sumAmounts(thisMonthExpenses.value))
    const spentLastMonth = computed(() => sumAmounts(expensesInMonth(lastMonthKey)))
    const transactionCount = computed(() => thisMonthExpenses.value.length)
    const averageExpense = computed(() => (transactionCount.value ? spentThisMonth.value / transactionCount.value : 0))
    const dailyAverage = computed(() => spentThisMonth.value / today.getDate())

    const monthChange = computed(() => {
        if (spentLastMonth.value === 0) return null
        return ((spentThisMonth.value - spentLastMonth.value) / spentLastMonth.value) * 100
    })

    const largestExpense = computed(() =>
        thisMonthExpenses.value.reduce(
            (largest, expense) => (!largest || Number(expense.amount) > Number(largest.amount) ? expense : largest),
            null
        )
    )

    const categoryBreakdown = computed(() => {
        const totals = new Map()

        for (const expense of thisMonthExpenses.value) {
            const entry = totals.get(expense.category_id) ?? {
                id: expense.category_id,
                name: expense.category?.name ?? 'Uncategorized',
                color: expense.category?.color || '#1f6f54',
                total: 0,
            }
            entry.total += Number(expense.amount)
            totals.set(expense.category_id, entry)
        }

        return [...totals.values()]
            .sort((first, second) => second.total - first.total)
            .map((entry) => ({ ...entry, share: (entry.total / spentThisMonth.value) * 100 }))
    })

    const monthlyTrend = computed(() =>
        Array.from({ length: 6 }, (_, index) => {
            const date = new Date(today.getFullYear(), today.getMonth() - (5 - index), 1)
            const key = monthKey(date)

            return {
                key,
                label: date.toLocaleString('en', { month: 'short' }),
                total: sumAmounts(expensesInMonth(key)),
                isCurrent: key === thisMonthKey,
            }
        })
    )

    const recentExpenses = computed(() => toValue(expenses).slice(0, 5))

    return {
        monthLabel: today.toLocaleString('en', { month: 'long', year: 'numeric' }),
        daysSoFar: today.getDate(),
        spentThisMonth,
        spentLastMonth,
        monthChange,
        transactionCount,
        averageExpense,
        dailyAverage,
        largestExpense,
        categoryBreakdown,
        monthlyTrend,
        recentExpenses,
    }
}
