<template>
  <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8 animate-fade-in">
    <div class="md:grid md:grid-cols-3 md:gap-6">
      <div class="md:col-span-1">
        <div class="px-4 sm:px-0">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Profile Information</h3>
          <p class="mt-1 text-sm text-gray-600 font-normal">Update your account's profile details and password.</p>
        </div>
      </div>

      <div class="mt-5 md:mt-0 md:col-span-2">
        <form @submit.prevent="onSubmit">
          <div class="shadow sm:rounded-md sm:overflow-hidden border border-gray-100">
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
              
              <!-- Success Alert -->
              <div v-if="successMessage" class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                <p class="text-sm text-green-700 font-medium">{{ successMessage }}</p>
              </div>

              <!-- General Error Alert -->
              <div v-if="errors.general" class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                <p class="text-sm text-red-700 font-medium">{{ errors.general[0] }}</p>
              </div>

              <div class="grid grid-cols-6 gap-6">
                <!-- Name -->
                <div class="col-span-6 sm:col-span-4">
                  <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                  <input
                    type="text"
                    id="name"
                    v-model="form.name"
                    required
                    :disabled="loading"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all disabled:bg-gray-50 disabled:text-gray-500"
                    :class="{'border-red-300 focus:ring-red-500 focus:border-red-500': errors.name}"
                  />
                  <span v-if="errors.name" class="text-xs text-red-600 mt-1 block">{{ errors.name[0] }}</span>
                </div>

                <!-- Email -->
                <div class="col-span-6 sm:col-span-4">
                  <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                  <input
                    type="email"
                    id="email"
                    v-model="form.email"
                    required
                    :disabled="loading"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all disabled:bg-gray-50 disabled:text-gray-500"
                    :class="{'border-red-300 focus:ring-red-500 focus:border-red-500': errors.email}"
                  />
                  <span v-if="errors.email" class="text-xs text-red-600 mt-1 block">{{ errors.email[0] }}</span>
                </div>

                <div class="col-span-6">
                  <hr class="border-gray-200 my-2" />
                  <p class="text-sm font-medium text-gray-500">Change Password (Leave blank to keep current)</p>
                </div>

                <!-- Password -->
                <div class="col-span-6 sm:col-span-3">
                  <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                  <input
                    type="password"
                    id="password"
                    v-model="form.password"
                    :disabled="loading"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all disabled:bg-gray-50 disabled:text-gray-500"
                    :class="{'border-red-300 focus:ring-red-500 focus:border-red-500': errors.password}"
                    placeholder="••••••••"
                  />
                  <span v-if="errors.password" class="text-xs text-red-600 mt-1 block">{{ errors.password[0] }}</span>
                </div>

                <!-- Confirm Password -->
                <div class="col-span-6 sm:col-span-3">
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                  <input
                    type="password"
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :disabled="loading"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all disabled:bg-gray-50 disabled:text-gray-500"
                    :class="{'border-red-300 focus:ring-red-500 focus:border-red-500': errors.password_confirmation}"
                    placeholder="••••••••"
                  />
                  <span v-if="errors.password_confirmation" class="text-xs text-red-600 mt-1 block">{{ errors.password_confirmation[0] }}</span>
                </div>
              </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
              <button
                type="submit"
                :disabled="loading"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:bg-indigo-400 disabled:cursor-not-allowed"
              >
                <!-- Dynamic Button Text -->
                {{ loading ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

const store = useStore()
const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const errors = ref({})
const successMessage = ref('')
const loading = ref(false) // <--- Add loading state

onMounted(() => {
  const user = store.getters.userData
  form.name = user.name || ''
  form.email = user.email || ''
})

async function onSubmit() {
  errors.value = {}
  successMessage.value = ''

  if (form.password) {
    if (form.password.length < 8) {
      errors.value.password = ['Password must be at least 8 characters long.']
    }
    if (form.password !== form.password_confirmation) {
      errors.value.password_confirmation = ['Passwords do not match.']
    }
    if (Object.keys(errors.value).length > 0) {
      return
    }
  }

  // Set loading to true before sending the request
  loading.value = true

  try {
    const response = await store.dispatch('updateProfile', form)
    successMessage.value = response.message
    form.password = ''
    form.password_confirmation = ''
  } catch (error) {
    if (error.response?.status === 401) {
      errors.value = { general: ['Your session has expired. Please log in again.'] }
      store.dispatch('logout')
      router.push({ name: 'login' })
    } else if (error.response?.status === 422) {
      errors.value = error.response.data.errors
    } else {
      console.error('An unexpected error occurred:', error)
      errors.value = { general: ['An unexpected error occurred. Please try again.'] }
    }
  } finally {
    // Reset loading state to false
    loading.value = false
  }
}
</script>

<style scoped>
</style>
