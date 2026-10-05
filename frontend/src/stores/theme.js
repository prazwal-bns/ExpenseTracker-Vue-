import { defineStore } from 'pinia'
import { computed, ref, watch } from 'vue'

const STORAGE_KEY = 'theme'
const THEMES = ['light', 'dark', 'system']

export const useThemeStore = defineStore('theme', () => {
    const stored = localStorage.getItem(STORAGE_KEY)
    const preference = ref(THEMES.includes(stored) ? stored : 'system')

    const systemQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const systemPrefersDark = ref(systemQuery.matches)
    systemQuery.addEventListener('change', (event) => {
        systemPrefersDark.value = event.matches
    })

    const isDark = computed(() =>
        preference.value === 'system' ? systemPrefersDark.value : preference.value === 'dark'
    )

    watch(isDark, (dark) => {
        document.documentElement.classList.toggle('dark', dark)
    }, { immediate: true })

    function setTheme(theme) {
        preference.value = theme
        localStorage.setItem(STORAGE_KEY, theme)
    }

    return { preference, isDark, setTheme }
})
