import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import ExpenseCategories from '../views/ExpenseCategories.vue'

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
      path: '/expense-categories',
      name: 'expense-categories',
      component: ExpenseCategories,
      meta: {requiresAuth: true,}
    }
  ],
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token');

  if(to.meta.requiresAuth && !token) {
    return {name: 'login'}
  }

  if(to.meta.guestOnly && token) {
    return {name: 'expense-categories'}
  }

  return true;
})

export default router
