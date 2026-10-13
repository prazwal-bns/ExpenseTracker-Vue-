<script setup>
import { computed, ref, watch } from 'vue'
import { useAdminUserStore } from '../stores/adminUsers'
import { useAuthStore } from '../stores/auth'
import BaseModal from './BaseModal.vue'
import ModalActions from './ModalActions.vue'

const props = defineProps({
    open: { type: Boolean, default: false },
    editing: { type: Object, default: null },
})

const emit = defineEmits(['close', 'created', 'updated'])

const adminUserStore = useAdminUserStore()
const auth = useAuthStore()

const roles = [
    { value: 'user', label: 'User', hint: 'Manages their own expenses' },
    { value: 'admin', label: 'Admin', hint: 'Can also manage every user' },
]

const name = ref('')
const email = ref('')
const role = ref('user')
const password = ref('')
const passwordConfirmation = ref('')

const errorMessage = ref('')
const fieldErrors = ref({})

const isEditingSelf = computed(() => !!props.editing && props.editing.id === auth.user?.id)
const initials = computed(() => {
    const parts = name.value.trim().split(/\s+/).filter(Boolean)
    return parts.slice(0, 2).map((part) => part[0].toUpperCase()).join('') || '?'
})

const inputClass = 'w-full rounded-xl border bg-surface px-4 py-3 text-sm text-ink outline-none transition placeholder:text-ink-soft/60 focus:ring-2'
const validInputClass = 'border-ink/15 focus:border-leaf focus:ring-leaf/20'
const invalidInputClass = 'border-red-300 focus:border-red-400 focus:ring-red-200 dark:border-red-500/50 dark:focus:ring-red-500/30'

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) return

        name.value = props.editing?.name ?? ''
        email.value = props.editing?.email ?? ''
        role.value = props.editing?.role ?? 'user'
        password.value = ''
        passwordConfirmation.value = ''
        errorMessage.value = ''
        fieldErrors.value = {}
    },
    { immediate: true }
)

async function handleSubmit() {
    errorMessage.value = ''
    fieldErrors.value = {}

    const payload = {
        name: name.value,
        email: email.value,
        role: role.value,
    }

    if (!props.editing || password.value) {
        payload.password = password.value
        payload.password_confirmation = passwordConfirmation.value
    }

    try {
        if (props.editing) {
            await adminUserStore.updateUser(props.editing.id, payload)
            emit('updated')
        } else {
            await adminUserStore.addUser(payload)
            emit('created')
        }
        emit('close')
    } catch (error) {
        fieldErrors.value = error.errors ?? {}
        if (!Object.keys(fieldErrors.value).length) {
            errorMessage.value = error.message
                || `Could not ${props.editing ? 'update' : 'create'} the user. Please try again.`
        }
    }
}
</script>

<template>
    <BaseModal
        :open="open"
        size="lg"
        eyebrow="Admin"
        :title="editing ? 'Edit user' : 'New user'"
        :description="editing
            ? 'Update this account\'s details, role or password.'
            : 'Create an account and choose what it can access.'"
        @close="emit('close')"
    >
        <form class="flex flex-col gap-5" @submit.prevent="handleSubmit">
            <div class="flex items-center gap-4 rounded-2xl border border-ink/8 bg-surface/90 px-4 py-3.5">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-leaf to-leaf-deep text-sm font-bold text-white">
                    {{ initials }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-ink">
                        {{ name || 'Full name' }}
                    </p>
                    <p class="mt-0.5 truncate text-xs text-ink-soft">
                        {{ email || 'email@example.com' }}
                    </p>
                </div>
                <span
                    class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold tracking-wide uppercase"
                    :class="role === 'admin' ? 'bg-amber/15 text-amber-deep' : 'bg-leaf/10 text-leaf'"
                >
                    {{ role }}
                </span>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="user-name" class="text-sm font-medium text-ink">Name</label>
                    <input
                        id="user-name"
                        v-model="name"
                        type="text"
                        required
                        maxlength="255"
                        autocomplete="off"
                        placeholder="e.g. Maya Patel"
                        :class="[inputClass, fieldErrors.name ? invalidInputClass : validInputClass]"
                        :aria-invalid="!!fieldErrors.name"
                    >
                    <p v-if="fieldErrors.name" class="text-xs font-medium text-red-600 dark:text-red-400">
                        {{ fieldErrors.name[0] }}
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="user-email" class="text-sm font-medium text-ink">Email</label>
                    <input
                        id="user-email"
                        v-model="email"
                        type="email"
                        required
                        maxlength="255"
                        autocomplete="off"
                        placeholder="maya@example.com"
                        :class="[inputClass, fieldErrors.email ? invalidInputClass : validInputClass]"
                        :aria-invalid="!!fieldErrors.email"
                    >
                    <p v-if="fieldErrors.email" class="text-xs font-medium text-red-600 dark:text-red-400">
                        {{ fieldErrors.email[0] }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <span class="text-sm font-medium text-ink">Role</span>
                <div class="grid gap-2.5 sm:grid-cols-2" role="radiogroup" aria-label="User role">
                    <button
                        v-for="option in roles"
                        :key="option.value"
                        type="button"
                        role="radio"
                        :aria-checked="role === option.value"
                        :disabled="isEditingSelf && option.value !== 'admin'"
                        class="flex cursor-pointer items-start gap-3 rounded-xl border px-4 py-3 text-left transition disabled:cursor-not-allowed disabled:opacity-50"
                        :class="role === option.value
                            ? 'border-leaf bg-leaf/10 ring-2 ring-leaf/20'
                            : 'border-ink/15 bg-surface hover:border-ink/30'"
                        @click="role = option.value"
                    >
                        <span
                            class="mt-0.5 flex size-4 shrink-0 items-center justify-center rounded-full border-2"
                            :class="role === option.value ? 'border-leaf' : 'border-ink/25'"
                        >
                            <span v-if="role === option.value" class="size-1.5 rounded-full bg-leaf" />
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-ink">{{ option.label }}</span>
                            <span class="block text-xs text-ink-soft">{{ option.hint }}</span>
                        </span>
                    </button>
                </div>
                <p v-if="isEditingSelf" class="text-xs text-ink-soft">
                    You can't remove your own admin role.
                </p>
                <p v-if="fieldErrors.role" class="text-xs font-medium text-red-600 dark:text-red-400">
                    {{ fieldErrors.role[0] }}
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="user-password" class="flex items-baseline justify-between text-sm font-medium text-ink">
                        Password
                        <span v-if="editing" class="text-xs font-normal text-ink-soft">Leave blank to keep</span>
                    </label>
                    <input
                        id="user-password"
                        v-model="password"
                        type="password"
                        :required="!editing"
                        autocomplete="new-password"
                        placeholder="••••••••"
                        :class="[inputClass, fieldErrors.password ? invalidInputClass : validInputClass]"
                        :aria-invalid="!!fieldErrors.password"
                    >
                    <p v-if="fieldErrors.password" class="text-xs font-medium text-red-600 dark:text-red-400">
                        {{ fieldErrors.password[0] }}
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="user-password-confirmation" class="text-sm font-medium text-ink">
                        Confirm password
                    </label>
                    <input
                        id="user-password-confirmation"
                        v-model="passwordConfirmation"
                        type="password"
                        :required="!editing || !!password"
                        autocomplete="new-password"
                        placeholder="••••••••"
                        :class="[inputClass, validInputClass]"
                    >
                </div>
            </div>

            <div
                v-if="errorMessage"
                class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300"
                role="alert"
            >
                <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                </svg>
                <p>{{ errorMessage }}</p>
            </div>

            <ModalActions
                :loading="adminUserStore.saving"
                :submit-label="editing ? 'Update user' : 'Create user'"
                @cancel="emit('close')"
            />
        </form>
    </BaseModal>
</template>
