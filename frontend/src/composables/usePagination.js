import { computed, ref, toValue, watch } from 'vue'

export function usePagination(items, { perPage: initialPerPage = 5 } = {}) {
    const currentPage = ref(1)
    const perPage = ref(initialPerPage)

    const totalItems = computed(() => toValue(items).length)
    const lastPage = computed(() => Math.max(1, Math.ceil(totalItems.value / perPage.value)))

    const paginatedItems = computed(() => {
        const start = (currentPage.value - 1) * perPage.value
        return toValue(items).slice(start, start + perPage.value)
    })

    watch(perPage, () => {
        currentPage.value = 1
    })

    watch(lastPage, (last) => {
        if (currentPage.value > last) currentPage.value = last
    })

    return { currentPage, perPage, totalItems, paginatedItems }
}
