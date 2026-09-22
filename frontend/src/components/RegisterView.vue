<script setup>
import { RouterLink, useRouter } from 'vue-router'
import { register } from '../api/auth';
import { ref } from 'vue';

const router = useRouter();

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const error = ref('');

async function handleRegister() {
  try {
    const data = await register(name.value, email.value, password.value, passwordConfirmation.value);
    localStorage.setItem('token', data.token);
    await router.push({ name: 'expense-categories' });
  } catch (err) {
    error.value = err.message;
  }
}
</script>

<template>
  <div class="flex min-h-dvh items-center justify-center bg-fog px-6">
    <div class="w-full max-w-md">
      <RouterLink :to="{ name: 'home' }" class="font-display text-2xl font-bold text-ink">
        Expense Tracker
      </RouterLink>
      <h1 class="mt-8 font-display text-3xl font-medium text-ink">Create account</h1>
      <form @submit.prevent="handleRegister">
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium text-ink">Name</label>
          <input type="text" id="name" name="name" placeholder="Your name" v-model="name" />
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-ink">Email</label>
          <input type="email" id="email" name="email" placeholder="Your email" v-model="email" />
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-ink">Password</label>
          <input type="password" id="password" name="password" placeholder="Your password" v-model="password" />
        </div>
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-ink">Confirm Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" v-model="passwordConfirmation" />
        </div>
        <button type="submit">Create account</button>
      </form>
      <p v-if="error" class="mt-2 text-sm text-red-500">{{ error }}</p>
      <RouterLink
        :to="{ name: 'login' }"
        class="mt-8 inline-flex text-sm font-semibold text-leaf hover:text-leaf-deep"
      >
        Already have an account? Sign in
      </RouterLink>
    </div>
  </div>
</template>
