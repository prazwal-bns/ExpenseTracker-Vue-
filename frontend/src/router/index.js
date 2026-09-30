import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import DashboardView from '../views/DashboardView.vue'
import ExpenseCategories from '../views/ExpenseCategories.vue'
import { useAuthStore } from '../stores/auth'

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
      meta: {guestOnly: true,}
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: {guestOnly: true,}
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: {requiresAuth: true,}
    },
    {
      path: '/expense-categories',
      name: 'expense-categories',
      component: ExpenseCategories,
      meta: {requiresAuth: true,}
    }
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if(auth.token && !auth.isAuthenticated) {
    auth.clearSession()
  }

  if(to.meta.requiresAuth && !auth.isAuthenticated) {
    return {name: 'login'}
  }

  if(to.meta.guestOnly && auth.isAuthenticated) {
    return {name: 'dashboard'}
  }

  return true;
})

export default router
