<script setup>
import { computed, onMounted, ref } from 'vue'
import AppNavbar from '../components/AppNavbar.vue'
import StatCard from '../components/StatCard.vue'
import ActionButton from '../components/ActionButton.vue'
import PaginationControls from '../components/PaginationControls.vue'
import ConfirmModal from '../components/ConfirmModal.vue'
import UserFormModal from '../components/UserFormModal.vue'
import { useAdminUserStore } from '../stores/adminUsers'
import { useAuthStore } from '../stores/auth'
import { usePagination } from '../composables/usePagination'
import { useAppToast } from '../composables/useAppToast'

const toast = useAppToast()
const auth = useAuthStore()
const adminUserStore = useAdminUserStore()

const showUserModal = ref(false)
const editingUser = ref(null)
const deletingUser = ref(null)
const deleteError = ref('')

const search = ref('')
const roleFilter = ref('all')
const roleFilters = [
  { value: 'all', label: 'All' },
  { value: 'admin', label: 'Admins' },
  { value: 'user', label: 'Users' },
]

const filteredUsers = computed(() => {
  const term = search.value.trim().toLowerCase()

  return adminUserStore.users.filter((user) => {
    const matchesRole = roleFilter.value === 'all' || user.role === roleFilter.value
    const matchesSearch = !term
      || user.name.toLowerCase().includes(term)
      || user.email.toLowerCase().includes(term)

    return matchesRole && matchesSearch
  })
})

const {
  currentPage,
  perPage,
  totalItems: totalUsers,
  paginatedItems: paginatedUsers,
} = usePagination(() => filteredUsers.value, { perPage: 10 })

const summary = computed(() => {
  const users = adminUserStore.users
  const monthKey = new Date().toISOString().slice(0, 7)

  return [
    {
      label: 'Total users',
      value: users.length,
      hint: 'Every registered account',
      tone: 'leaf',
    },
    {
      label: 'Admins',
      value: users.filter((user) => user.role === 'admin').length,
      hint: 'Can manage other users',
      tone: 'amber',
    },
    {
      label: 'Joined this month',
      value: users.filter((user) => user.created_at?.startsWith(monthKey)).length,
      hint: 'New accounts since the 1st',
      tone: 'ink',
    },
  ]
})

onMounted(() => adminUserStore.fetchUsers())

function isCurrentUser(user) {
  return user.id === auth.user?.id
}

function initials(name) {
  return name.trim().split(/\s+/).slice(0, 2).map((part) => part[0].toUpperCase()).join('')
}

function formatJoined(timestamp) {
  return timestamp
    ? new Date(timestamp).toLocaleDateString('en', { month: 'short', day: 'numeric', year: 'numeric' })
    : '—'
}

function openCreateModal() {
  editingUser.value = null
  showUserModal.value = true
}

function openEditModal(user) {
  editingUser.value = user
  showUserModal.value = true
}

function closeModal() {
  showUserModal.value = false
  editingUser.value = null
}

function handleCreated() {
  toast.success('User created')
}

function handleUpdated() {
  toast.success('User updated')
}

function openDeleteModal(user) {
  deleteError.value = ''
  deletingUser.value = user
}

function closeDeleteModal() {
  deletingUser.value = null
  deleteError.value = ''
}

async function confirmDelete() {
  deleteError.value = ''

  try {
    await adminUserStore.deleteUser(deletingUser.value.id)
    toast.success(`${deletingUser.value.name} deleted`)
    closeDeleteModal()
  } catch (error) {
    deleteError.value = error.message || 'Could not delete the user. Please try again.'
  }
}
</script>

<template>
  <div class="relative min-h-dvh overflow-hidden bg-linear-to-br from-fog via-mist to-glow">
    <div
      class="pointer-events-none absolute inset-0 opacity-30"
      style="
        background-image:
          radial-gradient(circle at 12% 18%, rgb(31 111 84 / 0.16), transparent 32%),
          radial-gradient(circle at 88% 12%, rgb(232 132 58 / 0.12), transparent 28%),
          linear-gradient(color-mix(in oklab, var(--color-ink) 5%, transparent) 1px, transparent 1px),
          linear-gradient(90deg, color-mix(in oklab, var(--color-ink) 5%, transparent) 1px, transparent 1px);
        background-size: auto, auto, 48px 48px, 48px 48px;
      "
    />

    <div class="relative mx-auto flex min-h-dvh max-w-7xl flex-col px-6 py-8 sm:px-10 lg:px-12">
      <AppNavbar />

      <main class="mt-10 flex-1 pb-10">
        <section>
          <p class="text-sm font-medium tracking-[0.18em] text-amber-deep uppercase">
            Admin
          </p>
          <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
            <div>
              <h1 class="font-display text-3xl font-medium text-ink sm:text-4xl">
                Manage users
              </h1>
              <p class="mt-3 max-w-lg text-sm leading-relaxed text-ink-soft sm:text-base">
                Create accounts, change roles and remove users who no longer need access.
              </p>
            </div>
            <button
              type="button"
              class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-leaf px-4 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgb(31_111_84_/_0.25)] transition hover:bg-leaf-deep"
              @click="openCreateModal"
            >
              <svg class="size-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M10 4.5v11M4.5 10h11" />
              </svg>
              Add user
            </button>
          </div>
        </section>

        <section class="mt-8 grid gap-4 sm:grid-cols-3">
          <StatCard
            v-for="item in summary"
            :key="item.label"
            v-bind="item"
          />
        </section>

        <section class="mt-8 rounded-2xl border border-ink/10 bg-surface/70 p-5 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
          <div class="mb-5 flex flex-col gap-3 border-b border-ink/8 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <label class="flex h-10 w-full items-center gap-2.5 rounded-xl border border-ink/15 bg-surface px-3 transition focus-within:border-leaf focus-within:ring-2 focus-within:ring-leaf/20 sm:w-72">
              <svg class="size-4 shrink-0 text-ink-soft" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                <path d="M9 15.5a6.5 6.5 0 1 0 0-13 6.5 6.5 0 0 0 0 13ZM17.5 17.5l-3.9-3.9" />
              </svg>
              <input
                v-model="search"
                type="text"
                placeholder="Search name or email"
                aria-label="Search users"
                class="h-full min-w-0 flex-1 bg-transparent text-sm text-ink outline-none placeholder:text-ink-soft/60"
              >
              <button
                v-if="search"
                type="button"
                class="flex size-6 shrink-0 cursor-pointer items-center justify-center rounded-md text-ink-soft transition hover:bg-fog hover:text-ink"
                aria-label="Clear search"
                @click="search = ''"
              >
                <svg class="size-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <path d="M5 5l10 10M15 5L5 15" />
                </svg>
              </button>
            </label>

            <div class="flex h-10 items-center gap-1 self-start rounded-xl border border-ink/8 bg-fog/80 p-1 sm:self-auto" role="radiogroup" aria-label="Filter by role">
              <button
                v-for="filter in roleFilters"
                :key="filter.value"
                type="button"
                role="radio"
                :aria-checked="roleFilter === filter.value"
                class="cursor-pointer rounded-lg px-3 py-1.5 text-sm font-medium transition"
                :class="roleFilter === filter.value
                  ? 'bg-surface text-leaf shadow-[0_2px_8px_rgb(16_42_36_/_0.08)] ring-1 ring-ink/5'
                  : 'text-ink-soft hover:text-ink'"
                @click="roleFilter = filter.value"
              >
                {{ filter.label }}
              </button>
            </div>
          </div>

          <div
            v-if="adminUserStore.loading"
            class="flex flex-col items-center gap-3 rounded-xl border border-ink/8 bg-fog/50 px-5 py-12 text-center"
          >
            <span class="size-8 animate-spin rounded-full border-2 border-leaf/20 border-t-leaf" />
            <p class="text-sm font-medium text-ink">
              Loading users…
            </p>
          </div>

          <div
            v-else-if="adminUserStore.error"
            class="flex flex-col items-center rounded-xl border border-red-200 bg-red-50/80 px-5 py-10 text-center dark:border-red-500/30 dark:bg-red-500/10"
            role="alert"
          >
            <p class="font-display text-lg text-red-800 dark:text-red-200">
              Couldn't load users
            </p>
            <p class="mt-1.5 max-w-sm text-sm text-red-700/90 dark:text-red-300/90">
              {{ adminUserStore.error }}
            </p>
            <button
              type="button"
              class="mt-5 cursor-pointer rounded-xl border border-red-200 bg-surface px-4 py-2 text-sm font-semibold text-red-700 transition hover:border-red-300 hover:bg-red-50 dark:border-red-500/30 dark:text-red-300 dark:hover:border-red-500/50 dark:hover:bg-red-500/10"
              @click="adminUserStore.fetchUsers()"
            >
              Try again
            </button>
          </div>

          <div
            v-else-if="filteredUsers.length"
            class="flex flex-col gap-5"
          >
            <div class="overflow-hidden rounded-xl border border-ink/8 bg-surface/90">
              <table class="w-full table-fixed text-left">
                <thead class="bg-fog/80">
                  <tr class="text-[11px] font-semibold tracking-[0.14em] text-ink-soft uppercase">
                    <th scope="col" class="px-4 py-3 lg:px-6">
                      User
                    </th>
                    <th scope="col" class="w-24 px-4 py-3 lg:w-28 lg:px-6">
                      Role
                    </th>
                    <th scope="col" class="hidden w-28 px-4 py-3 text-right md:table-cell lg:px-6">
                      Expenses
                    </th>
                    <th scope="col" class="hidden w-36 px-4 py-3 lg:table-cell lg:px-6">
                      Joined
                    </th>
                    <th scope="col" class="w-24 px-4 py-3 text-right lg:w-52 lg:px-6">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-ink/6">
                  <tr
                    v-for="user in paginatedUsers"
                    :key="user.id"
                    class="transition hover:bg-mist/60"
                  >
                    <td class="px-4 py-3.5 lg:px-6">
                      <div class="flex min-w-0 items-center gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-leaf to-leaf-deep text-xs font-bold text-white">
                          {{ initials(user.name) }}
                        </span>
                        <div class="min-w-0">
                          <p class="flex items-center gap-2 truncate text-sm font-semibold text-ink">
                            <span class="truncate">{{ user.name }}</span>
                            <span
                              v-if="isCurrentUser(user)"
                              class="shrink-0 rounded-full bg-ink/5 px-2 py-0.5 text-[10px] font-semibold tracking-wide text-ink-soft uppercase"
                            >
                              You
                            </span>
                          </p>
                          <p class="mt-0.5 truncate text-xs text-ink-soft">
                            {{ user.email }}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3.5 lg:px-6">
                      <span
                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize"
                        :class="user.role === 'admin' ? 'bg-amber/15 text-amber-deep' : 'bg-leaf/10 text-leaf'"
                      >
                        {{ user.role }}
                      </span>
                    </td>
                    <td class="hidden px-4 py-3.5 text-right md:table-cell lg:px-6">
                      <span class="inline-flex min-w-8 justify-center rounded-full bg-fog px-2.5 py-1 text-xs font-semibold text-ink-soft">
                        {{ user.expenses_count ?? 0 }}
                      </span>
                    </td>
                    <td class="hidden px-4 py-3.5 text-sm text-ink-soft lg:table-cell lg:px-6">
                      {{ formatJoined(user.created_at) }}
                    </td>
                    <td class="px-4 py-3.5 lg:px-6">
                      <div class="flex items-center justify-end gap-1 lg:gap-2">
                        <ActionButton
                          label="Edit"
                          icon="edit"
                          :aria-label="`Edit ${user.name}`"
                          @click="openEditModal(user)"
                        />
                        <ActionButton
                          label="Delete"
                          icon="delete"
                          variant="danger"
                          :disabled="isCurrentUser(user)"
                          :aria-label="`Delete ${user.name}`"
                          @click="openDeleteModal(user)"
                        />
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <PaginationControls
              v-model:page="currentPage"
              v-model:per-page="perPage"
              :total-items="totalUsers"
              item-label="users"
            />
          </div>

          <div
            v-else
            class="rounded-xl border border-dashed border-ink/15 bg-fog/60 px-5 py-10 text-center"
          >
            <p class="font-display text-lg text-ink">
              No users found
            </p>
            <p class="mt-2 text-sm text-ink-soft">
              Try a different search or role filter.
            </p>
          </div>
        </section>
      </main>
    </div>

    <UserFormModal
      :open="showUserModal"
      :editing="editingUser"
      @close="closeModal"
      @created="handleCreated"
      @updated="handleUpdated"
    />

    <ConfirmModal
      :open="!!deletingUser"
      title="Delete user?"
      description="This permanently removes the account along with all of its categories and expenses. This can't be undone."
      confirm-label="Delete user"
      :loading="adminUserStore.saving"
      :error="deleteError"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    >
      <div
        v-if="deletingUser"
        class="flex items-center gap-4 rounded-2xl border border-ink/8 bg-surface/90 px-4 py-3.5"
      >
        <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-leaf to-leaf-deep text-sm font-bold text-white">
          {{ initials(deletingUser.name) }}
        </span>
        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-semibold text-ink">
            {{ deletingUser.name }}
          </p>
          <p class="mt-0.5 truncate text-xs text-ink-soft">
            {{ deletingUser.email }}
          </p>
        </div>
        <span class="shrink-0 rounded-full bg-fog px-2.5 py-1 text-xs font-semibold text-ink-soft">
          {{ deletingUser.expenses_count ?? 0 }} expenses
        </span>
      </div>
    </ConfirmModal>
  </div>
</template>
