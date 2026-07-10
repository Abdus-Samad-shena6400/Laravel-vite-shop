<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 sm:space-y-8 card p-6 sm:p-8">
      <div>
        <h2 class="mt-4 sm:mt-6 text-center text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
          Create your account
        </h2>
        <p class="mt-2 text-center text-xs sm:text-sm text-gray-600">
          Or
          <router-link :to="{ name: 'login' }" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
            sign in to your existing account
          </router-link>
        </p>
      </div>

      <!-- General Error Alert -->
      <div v-if="errors.general" class="bg-red-50 border-l-4 border-red-400 p-3 sm:p-4 rounded-lg mb-4 sm:mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-xs sm:text-sm text-red-700">{{ errors.general[0] }}</p>
          </div>
        </div>
      </div>
      
      <form class="mt-6 sm:mt-8 space-y-4 sm:space-y-6" @submit.prevent="onSubmit">
        <div class="rounded-md shadow-sm space-y-4">
          <!-- Full Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input
              id="name"
              type="text"
              required
              v-model="user.name"
              :class="errors.name ? 'input-error' : 'input-standard'"
              placeholder="Abdus samad"
            />
            <span v-if="errors.name" class="text-xs text-red-600 mt-1 block">{{ errors.name[0] }}</span>
          </div>

          <!-- Email Address -->
          <div>
            <label for="email-address" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input
              id="email-address"
              type="email"
              required
              v-model="user.email"
              :class="errors.email ? 'input-error' : 'input-standard'"
              placeholder="Abdus.samad1@example.com"
            />
            <span v-if="errors.email" class="text-xs text-red-600 mt-1 block">{{ errors.email[0] }}</span>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input
              id="password"
              type="password"
              required
              v-model="user.password"
              :class="errors.password ? 'input-error' : 'input-standard'"
              placeholder="••••••••"
            />
            <span v-if="errors.password" class="text-xs text-red-600 mt-1 block">{{ errors.password[0] }}</span>
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password-confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input
              id="password-confirm"
              type="password"
              required
              v-model="user.password_confirmation"
              :class="errors.password_confirmation ? 'input-error' : 'input-standard'"
              placeholder="••••••••"
            />
            <span v-if="errors.password_confirmation" class="text-xs text-red-600 mt-1 block">{{ errors.password_confirmation[0] }}</span>
          </div>
        </div>

        <div>
          <button type="submit" class="btn-primary w-full py-2.5 sm:py-3 text-sm sm:text-base">
            Sign up
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const router = useRouter()
const store = useStore()

// Reactive state for the registration form
const user = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

// Reactive state to hold validation errors
const errors = ref({})

// Submit handler
async function onSubmit() {
  // 1. Clear any previous errors
  errors.value = {}

  // 2. Perform client-side validation
  if (user.password.length < 8) {
    errors.value.password = ['Password must be at least 8 characters long.']
  }

  if (user.password !== user.password_confirmation) {
    errors.value.password_confirmation = ['Passwords do not match.']
  }

  // If there are client-side errors, stop here
  if (Object.keys(errors.value).length > 0) {
    return
  }

  try {
    // Dispatch the register action and capture backend response data
    const response = await store.dispatch('register', user)
    
    // Redirect based on whether the user is an admin or a customer
    if (response.user.is_admin) {
      router.push({ name: 'dashboard' })
    } else {
      router.push({ name: 'user-dashboard' })
    }
  } catch (error) {
    // Handle backend validation errors
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors
    } else {
      console.error('An unexpected error occurred:', error)
      errors.value = { general: ['An unexpected error occurred. Please try again.'] }
    }
  }
}
</script>

<style scoped>
</style>
