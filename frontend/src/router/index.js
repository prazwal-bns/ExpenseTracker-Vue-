import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import DashboardView from '../views/DashboardView.vue'
import ExpenseCategories from '../views/ExpenseCategories.vue'
import ExpensesView from '../views/ExpensesView.vue'
import AdminUsersView from '../views/AdminUsersView.vue'
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
    },
    {
      path: '/expenses',
      name: 'expenses',
      component: ExpensesView,
      meta: {requiresAuth: true,}
    },
    {
      path: '/admin/users',
      name: 'admin-users',
      component: AdminUsersView,
      meta: {requiresAuth: true, requiresAdmin: true,}
    }
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  const isProtectedPage = to.meta.requiresAuth
  const isAdminPage = to.meta.requiresAdmin
  const isGuestOnlyPage = to.meta.guestOnly

  // A token exists but has expired: throw the old session away.
  if(auth.token && !auth.isAuthenticated) {
    auth.clearSession()
  }

  // Protected pages need a logged-in user.
  if(isProtectedPage && !auth.isAuthenticated) {
    return {name: 'login'}
  }

  // The user (and their role) only lives in memory, so ask the server for it when:
  //  - we don't have it yet (first page after a reload or after another tab logged in), or
  //  - this is an admin page, which must always use the latest role.
  const needsFreshUser = !auth.user || isAdminPage

  if(isProtectedPage && needsFreshUser) {
    try {
      // Replaces auth.user, which makes auth.isAdmin recalculate from the new role.
      await auth.fetchUser()
    } catch {
      // If we can't confirm the role, don't risk showing an admin page.
      if(isAdminPage) {
        return {name: 'dashboard'}
      }
    }
  }

  // The actual role check: only admins may open admin pages.
  if(isAdminPage && !auth.isAdmin) {
    return {name: 'dashboard'}
  }

  // Logged-in users don't need the login/register pages.
  if(isGuestOnlyPage && auth.isAuthenticated) {
    return {name: 'dashboard'}
  }

  return true;
})

export default router
