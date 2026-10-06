import { createApp } from 'vue'
import App from './App.vue'
import './main.css'
import router from './router/index.js'
import 'vue-toast-notification/dist/theme-sugar.css'
import {createPinia } from 'pinia'
import { useThemeStore } from './stores/theme'

const app = createApp(App)
app.use(createPinia())
useThemeStore()
app.use(router)
app.mount('#app')
