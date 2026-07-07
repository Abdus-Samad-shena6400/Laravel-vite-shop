<template>
  <GuestLayout title="Request new password">
    <!-- Success Message Alert -->
    <div v-if="successMessage" class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg mb-6">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-green-700">{{ successMessage }}</p>
        </div>
      </div>
    </div>

    <!-- General Error Alert -->
    <div v-if="errors.general" class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg mb-6">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">{{ errors.general[0] }}</p>
        </div>
      </div>
    </div>

    <form class="space-y-6" @submit.prevent="onSubmit">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
        <input
          type="email"
          id="email"
          required
          v-model="email"
          class="appearance-none rounded-lg relative block w-full px-3 py-2.5 border placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:z-10 sm:text-sm transition-all w-full"
          :class="errors.email ? 'border-red-300 focus:ring-red-500 focus:border-red-500 text-red-950 bg-red-50/30' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'"
          placeholder="you@example.com"
        />
        <span v-if="errors.email" class="text-xs text-red-600 mt-1 block">{{ errors.email[0] }}</span>
      </div>

      <div>
        <button
          type="submit"
          class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-md hover:shadow-lg"
        >
          Submit
        </button>
      </div>
    </form>

    <p class="mt-8 text-center text-sm text-gray-600">
      Remember your password?
      <router-link :to="{name: 'login'}" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors ml-1">
        Sign in to your account
      </router-link>
    </p>
  </GuestLayout>
</template>

<script setup>
import { ref } from 'vue'
import GuestLayout from '../../components/GuestLayout.vue'
import axiosClient from '../../axios.js'

const email = ref('')
const errors = ref({})
const successMessage = ref('')

async function onSubmit() {
  errors.value = {}
  successMessage.value = ''

  try {
    const response = await axiosClient.post('/forgot-password', { email: email.value })
    successMessage.value = response.data.message
    email.value = '' // Clear input on success
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