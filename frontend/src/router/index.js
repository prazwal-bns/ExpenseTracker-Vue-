import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../components/HomeView.vue'
import LoginView from '../components/LoginView.vue'
import RegisterView from '../components/RegisterView.vue'
import ExpenseCategories from '../components/ExpenseCategories.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
    {
      path: '/expense-categories',
      name: 'expense-categories',
      component: ExpenseCategories,
    }
  ],
})

export default router
