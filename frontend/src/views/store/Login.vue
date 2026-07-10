<template>
  <GuestLayout title="Sign in to your account">
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

    <form class="space-y-4 sm:space-y-6" @submit.prevent="onSubmit">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
        <input
          type="email"
          id="email"
          required
          v-model="credentials.email"
          :class="errors.email ? 'input-error' : 'input-standard'"
          placeholder="you@example.com"
        />
        <span v-if="errors.email" class="text-xs text-red-600 mt-1 block">{{ errors.email[0] }}</span>
      </div>

      <div>
        <div class="flex items-center justify-between mb-1">
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <div class="text-sm">
            <router-link :to="{name: 'requestPassword'}" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
              Forgot password?
            </router-link>
          </div>
        </div>
        <input
          type="password"
          id="password"
          required
          v-model="credentials.password"
          :class="errors.password ? 'input-error' : 'input-standard'"
          placeholder="••••••••"
        />
        <span v-if="errors.password" class="text-xs text-red-600 mt-1 block">{{ errors.password[0] }}</span>
      </div>

      <div>
        <button type="submit" class="btn-primary w-full py-2.5 sm:py-3 text-sm sm:text-base">
          Sign in
        </button>
      </div>
    </form>

    <p class="mt-6 sm:mt-8 text-center text-xs sm:text-sm text-gray-600">
      Not a member?
      <router-link :to="{ name: 'register' }" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors ml-1">
        Create an account
      </router-link>
    </p>
  </GuestLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import GuestLayout from '../../components/GuestLayout.vue'

const router = useRouter()
const store = useStore()

const credentials = reactive({
  email: '',
  password: ''
})

const errors = ref({})

async function onSubmit() {
  errors.value = {}

  try {
    // Dispatch the login action and capture backend response data
    const response = await store.dispatch('login', credentials)
    
    // Redirect based on whether the user is an admin or a customer
    if (response.user.is_admin) {
      router.push({ name: 'dashboard' })
    } else {
      router.push({ name: 'user-dashboard' })
    }
  } catch (error) {
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