<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppNavbar from '../components/AppNavbar.vue'
import ConfirmModal from '../components/ConfirmModal.vue'
import { useProfileStore } from '../stores/profile'
import { useAppToast } from '../composables/useAppToast'

const toast = useAppToast()
const router = useRouter()
const profileStore = useProfileStore()

const inputClass = 'w-full rounded-xl border bg-surface px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:ring-2'
const validInputClass = 'border-ink/15 focus:border-leaf focus:ring-leaf/20'
const invalidInputClass = 'border-red-300 focus:border-red-400 focus:ring-red-200 dark:border-red-500/50 dark:focus:ring-red-500/30'

const profile = computed(() => profileStore.profile)

const initials = computed(() =>
  (profile.value?.name ?? '')
    .trim()
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0].toUpperCase())
    .join('') || '?'
)

const memberSince = computed(() =>
  profile.value?.created_at
    ? new Date(profile.value.created_at).toLocaleDateString('en', { month: 'long', year: 'numeric' })
    : '—'
)

// Profile details
const name = ref('')
const email = ref('')
const detailErrors = ref({})
const detailMessage = ref('')

const isDetailsDirty = computed(() =>
  !!profile.value && (name.value !== profile.value.name || email.value !== profile.value.email)
)

function resetDetails() {
  name.value = profile.value?.name ?? ''
  email.value = profile.value?.email ?? ''
  detailErrors.value = {}
  detailMessage.value = ''
}

watch(profile, resetDetails, { immediate: true })

async function saveDetails() {
  detailErrors.value = {}
  detailMessage.value = ''

  try {
    await profileStore.updateProfile({ name: name.value, email: email.value })
    toast.success('Profile updated')
  } catch (error) {
    detailErrors.value = error.errors ?? {}
    if (!Object.keys(detailErrors.value).length) {
      detailMessage.value = error.message || 'Could not update your profile. Please try again.'
    }
  }
}

// Password
const currentPassword = ref('')
const newPassword = ref('')
const newPasswordConfirmation = ref('')
const passwordErrors = ref({})
const passwordMessage = ref('')

async function savePassword() {
  passwordErrors.value = {}
  passwordMessage.value = ''

  try {
    await profileStore.updatePassword({
      current_password: currentPassword.value,
      password: newPassword.value,
      password_confirmation: newPasswordConfirmation.value,
    })
    currentPassword.value = ''
    newPassword.value = ''
    newPasswordConfirmation.value = ''
    toast.success('Password changed. Other devices have been signed out.')
  } catch (error) {
    passwordErrors.value = error.errors ?? {}
    if (!Object.keys(passwordErrors.value).length) {
      passwordMessage.value = error.message || 'Could not change your password. Please try again.'
    }
  }
}

// Delete account
const showDeleteModal = ref(false)
const deletePassword = ref('')
const deleteError = ref('')

function openDeleteModal() {
  deletePassword.value = ''
  deleteError.value = ''
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
}

async function confirmDelete() {
  deleteError.value = ''

  try {
    await profileStore.deleteAccount(deletePassword.value)
    showDeleteModal.value = false
    toast.success('Your account has been deleted')
    router.push({ name: 'login' })
  } catch (error) {
    deleteError.value = error.errors?.password?.[0]
      || error.message
      || 'Could not delete your account. Please try again.'
  }
}

onMounted(() => profileStore.fetchProfile())
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
            Account
          </p>
          <h1 class="mt-3 font-display text-3xl font-medium text-ink sm:text-4xl">
            Your profile
          </h1>
          <p class="mt-3 max-w-lg text-sm leading-relaxed text-ink-soft sm:text-base">
            Keep your details up to date and your account secure.
          </p>
        </section>

        <div
          v-if="profileStore.loading && !profile"
          class="mt-8 flex flex-col items-center gap-3 rounded-2xl border border-ink/10 bg-surface/70 px-5 py-16 text-center"
        >
          <span class="size-8 animate-spin rounded-full border-2 border-leaf/20 border-t-leaf" />
          <p class="text-sm font-medium text-ink">
            Loading your profile…
          </p>
        </div>

        <div
          v-else-if="profileStore.error && !profile"
          class="mt-8 flex flex-col items-center rounded-2xl border border-red-200 bg-red-50/80 px-5 py-10 text-center dark:border-red-500/30 dark:bg-red-500/10"
          role="alert"
        >
          <p class="font-display text-lg text-red-800 dark:text-red-200">
            Couldn't load your profile
          </p>
          <p class="mt-1.5 max-w-sm text-sm text-red-700/90 dark:text-red-300/90">
            {{ profileStore.error }}
          </p>
          <button
            type="button"
            class="mt-5 cursor-pointer rounded-xl border border-red-200 bg-surface px-4 py-2 text-sm font-semibold text-red-700 transition hover:border-red-300 hover:bg-red-50 dark:border-red-500/30 dark:text-red-300 dark:hover:border-red-500/50 dark:hover:bg-red-500/10"
            @click="profileStore.fetchProfile()"
          >
            Try again
          </button>
        </div>

        <div
          v-else-if="profile"
          class="mt-8 grid gap-6 lg:grid-cols-3"
        >
          <aside class="flex flex-col gap-6 lg:col-span-1">
            <section class="rounded-2xl border border-ink/10 bg-surface/70 p-6 text-center shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm">
              <span class="mx-auto flex size-20 items-center justify-center rounded-3xl bg-linear-to-br from-leaf to-leaf-deep font-display text-2xl font-bold text-white shadow-[0_12px_28px_rgb(31_111_84_/_0.35)]">
                {{ initials }}
              </span>
              <h2 class="mt-4 truncate font-display text-xl font-semibold text-ink">
                {{ profile.name }}
              </h2>
              <p class="mt-1 truncate text-sm text-ink-soft">
                {{ profile.email }}
              </p>
              <span
                class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize"
                :class="profile.role === 'admin' ? 'bg-amber/15 text-amber-deep' : 'bg-leaf/10 text-leaf'"
              >
                {{ profile.role }}
              </span>

              <dl class="mt-6 grid grid-cols-2 gap-3 border-t border-ink/8 pt-5 text-left">
                <div class="rounded-xl bg-fog/70 px-3 py-2.5">
                  <dt class="text-[11px] font-medium tracking-wide text-ink-soft uppercase">
                    Expenses
                  </dt>
                  <dd class="mt-1 font-display text-lg font-semibold text-ink">
                    {{ profile.expenses_count ?? 0 }}
                  </dd>
                </div>
                <div class="rounded-xl bg-fog/70 px-3 py-2.5">
                  <dt class="text-[11px] font-medium tracking-wide text-ink-soft uppercase">
                    Categories
                  </dt>
                  <dd class="mt-1 font-display text-lg font-semibold text-ink">
                    {{ profile.categories_count ?? 0 }}
                  </dd>
                </div>
                <div class="col-span-2 rounded-xl bg-fog/70 px-3 py-2.5">
                  <dt class="text-[11px] font-medium tracking-wide text-ink-soft uppercase">
                    Member since
                  </dt>
                  <dd class="mt-1 text-sm font-semibold text-ink">
                    {{ memberSince }}
                  </dd>
                </div>
              </dl>
            </section>
          </aside>

          <div class="flex flex-col gap-6 lg:col-span-2">
            <section class="rounded-2xl border border-ink/10 bg-surface/70 p-6 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
              <h2 class="font-display text-lg font-semibold text-ink">
                Profile details
              </h2>
              <p class="mt-1 text-sm text-ink-soft">
                Your name and the email you use to sign in.
              </p>

              <form class="mt-6 flex flex-col gap-5" @submit.prevent="saveDetails">
                <div class="grid gap-5 sm:grid-cols-2">
                  <div class="flex flex-col gap-2">
                    <label for="profile-name" class="text-sm font-medium text-ink">Name</label>
                    <input
                      id="profile-name"
                      v-model="name"
                      type="text"
                      required
                      maxlength="255"
                      autocomplete="name"
                      :class="[inputClass, detailErrors.name ? invalidInputClass : validInputClass]"
                      :aria-invalid="!!detailErrors.name"
                    >
                    <p v-if="detailErrors.name" class="text-xs font-medium text-red-600 dark:text-red-400">
                      {{ detailErrors.name[0] }}
                    </p>
                  </div>

                  <div class="flex flex-col gap-2">
                    <label for="profile-email" class="text-sm font-medium text-ink">Email</label>
                    <input
                      id="profile-email"
                      v-model="email"
                      type="email"
                      required
                      maxlength="255"
                      autocomplete="email"
                      :class="[inputClass, detailErrors.email ? invalidInputClass : validInputClass]"
                      :aria-invalid="!!detailErrors.email"
                    >
                    <p v-if="detailErrors.email" class="text-xs font-medium text-red-600 dark:text-red-400">
                      {{ detailErrors.email[0] }}
                    </p>
                  </div>
                </div>

                <p
                  v-if="detailMessage"
                  class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300"
                  role="alert"
                >
                  {{ detailMessage }}
                </p>

                <div class="flex flex-col-reverse gap-2 border-t border-ink/8 pt-5 sm:flex-row sm:justify-end">
                  <button
                    v-if="isDetailsDirty"
                    type="button"
                    class="cursor-pointer rounded-xl border border-ink/15 bg-surface px-5 py-2.5 text-sm font-semibold text-ink-soft transition hover:border-ink/30 hover:text-ink"
                    @click="resetDetails"
                  >
                    Discard changes
                  </button>
                  <button
                    type="submit"
                    :disabled="!isDetailsDirty || profileStore.savingDetails"
                    class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-leaf px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgb(31_111_84_/_0.25)] transition hover:bg-leaf-deep disabled:cursor-not-allowed disabled:opacity-60 disabled:shadow-none"
                  >
                    <span
                      v-if="profileStore.savingDetails"
                      class="size-4 animate-spin rounded-full border-2 border-white/30 border-t-white"
                    />
                    {{ profileStore.savingDetails ? 'Saving…' : 'Save changes' }}
                  </button>
                </div>
              </form>
            </section>

            <section class="rounded-2xl border border-ink/10 bg-surface/70 p-6 shadow-[0_20px_50px_rgb(16_42_36_/_0.08)] backdrop-blur-sm sm:p-7">
              <h2 class="font-display text-lg font-semibold text-ink">
                Change password
              </h2>
              <p class="mt-1 text-sm text-ink-soft">
                Changing your password signs you out on every other device.
              </p>

              <form class="mt-6 flex flex-col gap-5" @submit.prevent="savePassword">
                <div class="flex flex-col gap-2 sm:max-w-[calc(50%-0.625rem)]">
                  <label for="current-password" class="text-sm font-medium text-ink">Current password</label>
                  <input
                    id="current-password"
                    v-model="currentPassword"
                    type="password"
                    required
                    autocomplete="current-password"
                    :class="[inputClass, passwordErrors.current_password ? invalidInputClass : validInputClass]"
                    :aria-invalid="!!passwordErrors.current_password"
                  >
                  <p v-if="passwordErrors.current_password" class="text-xs font-medium text-red-600 dark:text-red-400">
                    {{ passwordErrors.current_password[0] }}
                  </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                  <div class="flex flex-col gap-2">
                    <label for="new-password" class="text-sm font-medium text-ink">New password</label>
                    <input
                      id="new-password"
                      v-model="newPassword"
                      type="password"
                      required
                      autocomplete="new-password"
                      :class="[inputClass, passwordErrors.password ? invalidInputClass : validInputClass]"
                      :aria-invalid="!!passwordErrors.password"
                    >
                    <p v-if="passwordErrors.password" class="text-xs font-medium text-red-600 dark:text-red-400">
                      {{ passwordErrors.password[0] }}
                    </p>
                  </div>

                  <div class="flex flex-col gap-2">
                    <label for="new-password-confirmation" class="text-sm font-medium text-ink">Confirm new password</label>
                    <input
                      id="new-password-confirmation"
                      v-model="newPasswordConfirmation"
                      type="password"
                      required
                      autocomplete="new-password"
                      :class="[inputClass, validInputClass]"
                    >
                  </div>
                </div>

                <p
                  v-if="passwordMessage"
                  class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300"
                  role="alert"
                >
                  {{ passwordMessage }}
                </p>

                <div class="flex justify-end border-t border-ink/8 pt-5">
                  <button
                    type="submit"
                    :disabled="profileStore.savingPassword"
                    class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-leaf px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgb(31_111_84_/_0.25)] transition hover:bg-leaf-deep disabled:cursor-not-allowed disabled:opacity-60 disabled:shadow-none"
                  >
                    <span
                      v-if="profileStore.savingPassword"
                      class="size-4 animate-spin rounded-full border-2 border-white/30 border-t-white"
                    />
                    {{ profileStore.savingPassword ? 'Updating…' : 'Update password' }}
                  </button>
                </div>
              </form>
            </section>

            <section class="rounded-2xl border border-red-200 bg-red-50/60 p-6 sm:p-7 dark:border-red-500/30 dark:bg-red-500/5">
              <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                  <h2 class="font-display text-lg font-semibold text-red-800 dark:text-red-200">
                    Delete account
                  </h2>
                  <p class="mt-1 max-w-md text-sm text-red-700/90 dark:text-red-300/90">
                    Permanently remove your account along with all of your categories and expenses.
                  </p>
                </div>
                <button
                  type="button"
                  class="shrink-0 cursor-pointer rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgb(220_38_38_/_0.22)] transition hover:bg-red-700"
                  @click="openDeleteModal"
                >
                  Delete account
                </button>
              </div>
            </section>
          </div>
        </div>
      </main>
    </div>

    <ConfirmModal
      :open="showDeleteModal"
      title="Delete your account?"
      description="This permanently removes your account, categories and expenses. This can't be undone."
      confirm-label="Delete my account"
      :loading="profileStore.deleting"
      :disabled="!deletePassword"
      :error="deleteError"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    >
      <div class="flex flex-col gap-2">
        <label for="delete-password" class="text-sm font-medium text-ink">
          Enter your password to confirm
        </label>
        <input
          id="delete-password"
          v-model="deletePassword"
          type="password"
          autocomplete="current-password"
          :class="[inputClass, validInputClass]"
          @keydown.enter.prevent="deletePassword && confirmDelete()"
        >
      </div>
    </ConfirmModal>
  </div>
</template>
