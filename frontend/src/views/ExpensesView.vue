<script setup>
import { computed, onMounted, ref } from 'vue'
import AppNavbar from '../components/AppNavbar.vue'
import StatCard from '../components/StatCard.vue'
import ActionButton from '../components/ActionButton.vue'
import PaginationControls from '../components/PaginationControls.vue'
import ExpenseFormModal from '../components/ExpenseFormModal.vue'
import ConfirmModal from '../components/ConfirmModal.vue'
import { useExpenseStore } from '../stores/expenses'
import { useAppToast } from '../composables/useAppToast'
import { usePagination } from '../composables/usePagination'
import { useExpenseStats } from '../composables/useExpenseStats'
import { formatAmount, formatDate } from '../utils/format'

const showExpenseModal = ref(false)
const editingExpense = ref(null)
const deletingExpense = ref(null)
const deleteError = ref('')

const toast = useAppToast()
const expenseStore = useExpenseStore()

const {
  currentPage,
  perPage,
  totalItems: totalExpenses,
  paginatedItems: paginatedExpenses,
} = usePagination(() => expenseStore.expenses, { perPage: 10 })

const stats = useExpenseStats(() => expenseStore.expenses)

const summary = computed(() => {
  const change = stats.monthChange.value
  const largest = stats.largestExpense.value

  return [
    {
      label: 'Spent this month',
      value: formatAmount(stats.spentThisMonth.value),
      hint: change === null
        ? 'Nothing spent last month'
        : `${change > 0 ? '+' : '−'}${Math.abs(change).toFixed(0)}% vs last month`,
      tone: change > 0 ? 'amber' : 'leaf',
    },
    {
      label: 'Transactions',
      value: stats.transactionCount.value,
      hint: stats.transactionCount.value
        ? `This month · avg ${formatAmount(stats.averageExpense.value)} each`
        : 'None this month yet',
      tone: 'ink',
    },
    {
      label: 'Largest expense',
      value: largest ? formatAmount(largest.amount) : '—',
      hint: largest ? `${largest.title} · ${formatDate(largest.spent_at)}` : 'No expenses this month',
      tone: 'amber',
    },
  ]
})
onMounted(async() => {
  await expenseStore.fetchExpenses()
  console.log(expenseStore.expenses.map(e => e.title))
})


function openCreateModal() {
  editingExpense.value = null
  showExpenseModal.value = true
}

function openEditModal(expense) {
  editingExpense.value = expense
  showExpenseModal.value = true
}

function closeModal() {
  showExpenseModal.value = false
  editingExpense.value = null
}

function handleCreatedExpense() {
  toast.success('Expense created')
}
function handleUpdatedExpense() {
  toast.success('Expense updated')
}

function openDeleteModal(expense) {
  deleteError.value = ''
  deletingExpense.value = expense
}

function closeDeleteModal() {
  deletingExpense.value = null
  deleteError.value = ''
}

async function confirmDelete() {
  deleteError.value = ''

  try {
    await expenseStore.deleteExpense(deletingExpense.value.id)
    toast.success(`${deletingExpense.value.title} deleted`)
    closeDeleteModal()
  } catch (error) {
    deleteError.value = error.message || 'Could not delete the expense. Please try again.'
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
          <p class="text-sm font-medium tracking-[0.18em] text-leaf uppercase">
            Expenses
          </p>
          <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
            <div>
              <h1 class="font-display text-3xl font-medium text-ink sm:text-4xl">
                Your expenses
              </h1>
              <p class="mt-3 max-w-lg text-sm leading-relaxed text-ink-soft sm:text-base">
                Every transaction in one place. Search, filter and keep your spending honest.
              </p>
            </div>
            <p class="rounded-full bg-leaf/10 px-3 py-1 text-xs font-semibold text-leaf">
              {{ expenseStore.expenses.length }} total
            </p>
          </div>
        </section>

        <section class="mt-8 grid gap-4 sm:grid-cols-3">
          <StatCard
            v-for="item in summary"
            :key="item.label"
            v-bind="item"
          />
        </section>

        <section class="mt-6 rounded-2xl border border-ink/10 bg-surface/70 p-5 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
          <div class="mb-5 flex items-center justify-between gap-3 border-b border-ink/8 pb-4">
            <h2 class="text-sm font-semibold text-ink">
              Transactions
            </h2>
            <button
                type="button"
                class="rounded-full bg-leaf px-3 py-1 text-sm font-medium text-white cursor-pointer"
                @click="openCreateModal"
              >
                Add Expense
              </button>

              <ExpenseFormModal
                :open="showExpenseModal"
                :editing="editingExpense"
                @close="closeModal"
                @created="handleCreatedExpense"
                @updated="handleUpdatedExpense"
              />

              <ConfirmModal
                :open="!!deletingExpense"
                title="Delete expense?"
                description="This permanently removes the expense from your records. This can't be undone."
                confirm-label="Delete expense"
                :loading="expenseStore.saving"
                :error="deleteError"
                @close="closeDeleteModal"
                @confirm="confirmDelete"
              >
                <div
                  v-if="deletingExpense"
                  class="flex items-center gap-4 rounded-2xl border border-ink/8 bg-surface/90 px-4 py-3.5"
                >
                  <span
                    class="size-11 shrink-0 rounded-xl border border-ink/5 shadow-inner"
                    :style="{ backgroundColor: deletingExpense.category?.color || '#1f6f54' }"
                  />
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-ink">
                      {{ deletingExpense.title }}
                    </p>
                    <p class="mt-0.5 truncate text-xs text-ink-soft">
                      {{ deletingExpense.category?.name ?? 'Uncategorized' }} · {{ formatDate(deletingExpense.spent_at) }}
                    </p>
                  </div>
                  <span class="shrink-0 font-display text-lg font-semibold text-ink">
                    {{ formatAmount(deletingExpense.amount) }}
                  </span>
                </div>
              </ConfirmModal>
          </div>

          <div class="flex flex-col gap-5">
            <div class="overflow-hidden rounded-xl border border-ink/8 bg-surface/90">
              <table class="w-full table-fixed text-left">
                <thead class="bg-fog/80">
                  <tr class="text-[11px] font-semibold tracking-[0.14em] text-ink-soft uppercase">
                    <th scope="col" class="px-4 py-3 lg:px-6">
                      Expense
                    </th>
                    <th scope="col" class="hidden w-44 px-4 py-3 md:table-cell lg:px-6">
                      Category
                    </th>
                    <th scope="col" class="hidden w-36 px-4 py-3 sm:table-cell lg:px-6">
                      Date
                    </th>
                    <th scope="col" class="w-28 px-4 py-3 text-right lg:w-36 lg:px-6">
                      Amount
                    </th>
                    <th scope="col" class="w-24 px-4 py-3 text-right lg:w-52 lg:px-6">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-ink/6">
                  <tr
                    v-for="expense in paginatedExpenses"
                    :key="expense.id"
                    class="transition hover:bg-mist/60"
                  >
                    <td class="px-4 py-3.5 lg:px-6">
                      <div class="flex min-w-0 items-center gap-3">
                        <span
                          class="size-9 shrink-0 rounded-xl border border-ink/5 shadow-inner"
                          :style="{ backgroundColor: expense.category.color }"
                        />
                        <div class="min-w-0">
                          <p class="truncate text-sm font-semibold text-ink">
                            {{ expense.title }}
                          </p>
                          <p
                            class="mt-0.5 truncate text-xs"
                            :class="expense.notes ? 'text-ink-soft' : 'text-ink-soft/60 italic'"
                          >
                            <span class="md:hidden">{{ expense.category.name }} · </span>
                            <span class="sm:hidden">{{ formatDate(expense.spent_at) }} · </span>
                            {{ expense.notes || 'No notes' }}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="hidden px-4 py-3.5 md:table-cell lg:px-6">
                      <span class="inline-flex max-w-full items-center gap-2 rounded-full bg-fog px-2.5 py-1 text-xs font-semibold text-ink-soft">
                        <span
                          class="size-2 shrink-0 rounded-full"
                          :style="{ backgroundColor: expense.category.color }"
                        />
                        <span class="truncate">{{ expense.category.name }}</span>
                      </span>
                    </td>
                    <td class="hidden px-4 py-3.5 text-sm text-ink-soft sm:table-cell lg:px-6">
                      {{ formatDate(expense.spent_at) }}
                    </td>
                    <td class="px-4 py-3.5 text-right text-sm font-semibold whitespace-nowrap text-ink lg:px-6">
                      {{ formatAmount(expense.amount) }}
                    </td>
                    <td class="px-4 py-3.5 lg:px-6">
                      <div class="flex items-center justify-end gap-1 lg:gap-2">
                        <ActionButton
                          label="Edit"
                          icon="edit"
                          :aria-label="`Edit ${expense.title}`"
                          @click="openEditModal(expense)"
                        />
                        <ActionButton
                          label="Delete"
                          icon="delete"
                          variant="danger"
                          :aria-label="`Delete ${expense.title}`"
                          @click="openDeleteModal(expense)"
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
              :total-items="totalExpenses"
              item-label="expenses"
            />
          </div>

          <!-- Empty state (show when there are no expenses) -->
          <div
            v-if="false"
            class="rounded-xl border border-dashed border-ink/15 bg-fog/60 px-5 py-10 text-center"
          >
            <p class="font-display text-lg text-ink">
              No expenses yet
            </p>
            <p class="mt-2 text-sm text-ink-soft">
              Add your first expense to start tracking where your money goes.
            </p>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>
