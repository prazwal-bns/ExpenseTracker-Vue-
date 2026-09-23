import { createApp } from 'vue'
import App from './App.vue'
import './main.css'
import router from './router/index.js'
import ToastPlugin from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-sugar.css'

const app = createApp(App)
app.use(router)
app.use(ToastPlugin, {
    position: 'top-right',
    duration: 3000,
    theme: 'sugar',
})
app.mount('#app')
