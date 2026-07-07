<template>
  <div class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Shared Public Header -->
    <PublicHeader />

    <div class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full animate-fade-in">
      <div class="flex flex-col md:flex-row md:space-x-8">

        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-64 flex-shrink-0 mb-6 md:mb-0">
          <div class="bg-white p-4 rounded-2xl border border-gray-150 shadow-xs space-y-2">
            <div class="p-4 border-b border-gray-100 text-center">
              <div
                class="h-16 w-16 rounded-full bg-indigo-100 text-indigo-700 font-extrabold text-xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                {{ initials }}
              </div>
              <h3 class="font-bold text-gray-950 truncate">{{ currentUser.name }}</h3>
              <p class="text-xs text-gray-400 truncate">{{ currentUser.email }}</p>
            </div>

            <nav class="space-y-1">
              <button v-for="tab in ['overview', 'orders', 'profile', 'logout']" :key="tab"
                @click="tab === 'logout' ? logout() : activeTab = tab"
                class="w-full flex items-center px-4 py-2.5 text-sm font-semibold rounded-xl text-left transition-colors cursor-pointer capitalize"
                :class="activeTab === tab ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">

                <!-- Icon -->
                <svg v-if="tab === 'overview'" class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <svg v-else-if="tab === 'orders'" class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <svg v-else-if="tab === 'profile'" class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <svg v-else-if="tab === 'logout'" class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                {{ tab }}
              </button>
            </nav>
          </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-grow">
          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="space-y-8">
            <!-- Greeting Card -->
            <div
              class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-8 text-white shadow-md relative overflow-hidden">
              <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white/10 to-transparent">
              </div>
              <div class="relative z-10 space-y-2">
                <h2 class="text-3xl font-extrabold leading-tight">Welcome to your dashboard, {{ currentUser.name }}!
                </h2>
                <p class="text-indigo-100 font-light text-sm max-w-md">Track your shopping status, monitor orders, and
                  customize your profile preferences from here.</p>
              </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
              <!-- Orders -->
              <div class="bg-white p-6 rounded-2xl border border-gray-150 shadow-xs flex items-center space-x-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">My Orders</p>
                  <p class="text-2xl font-black text-gray-950 mt-1">{{ ordersCount }}</p>
                </div>
              </div>
              <!-- Cart Items -->
              <div class="bg-white p-6 rounded-2xl border border-gray-150 shadow-xs flex items-center space-x-4">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Cart Items</p>
                  <p class="text-2xl font-black text-gray-950 mt-1">{{ cartCount }}</p>
                </div>
              </div>
              <!-- Wishlist Items -->
              <div class="bg-white p-6 rounded-2xl border border-gray-150 shadow-xs flex items-center space-x-4">
                <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Wishlist Items</p>
                  <p class="text-2xl font-black text-gray-950 mt-1">{{ wishlistCount }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Orders Tab -->
          <div v-else-if="activeTab === 'orders'"
            class="bg-white rounded-2xl border border-gray-150 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
              <h2 class="text-lg font-bold text-gray-950">Purchase History</h2>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-100 text-sm text-left text-gray-700">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                  <tr>
                    <th scope="col" class="px-6 py-4">Order ID</th>
                    <th scope="col" class="px-6 py-4">Date</th>
                    <th scope="col" class="px-6 py-4">Items</th>
                    <th scope="col" class="px-6 py-4">Total</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope='col' class='px-6 py-4'>Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                  <tr v-if="ordersLoading">
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">Loading orders...</td>
                  </tr>
                  <tr v-else-if="orders.length === 0">
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No orders found</td>
                  </tr>
                  <tr v-else v-for="order in orders" :key="order.id" class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-indigo-600 font-bold">#{{ order.order_number }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                    <td class="px-6 py-4">{{ order.items?.length || 0 }} items</td>
                    <td class="px-6 py-4 font-extrabold text-gray-950">${{ Number(order.grand_total).toFixed(2) }}</td>
                    <td class="px-6 py-4">
                      <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                        :class="getStatusClass(order.order_status)">
                        {{ formatStatus(order.order_status) }}
                      </span>
                    </td>
                    <router-link :to="{ name: 'myOrderDetails', params: { id: order.id } }"
                      class="text-indigo-600 hover:text-indigo-800 inline-flex items-center px-8 py-4">
                      View
                    </router-link>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Profile Tab -->
          <div v-else-if="activeTab === 'profile'"
            class="bg-white p-6 rounded-2xl border border-gray-150 shadow-xs space-y-6">
            <div class="border-b border-gray-100 pb-4">
              <h2 class="text-lg font-bold text-gray-950">Profile Settings</h2>
              <p class="text-xs text-gray-400 mt-1">Manage your customer credentials.</p>
            </div>

            <!-- Success Alert -->
            <div v-if="successMessage" class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
              <p class="text-sm text-green-700 font-medium">{{ successMessage }}</p>
            </div>

            <form @submit.prevent="updateProfile" class="space-y-6">
              <div class="grid grid-cols-6 gap-6">
                <!-- Name -->
                <div class="col-span-6 sm:col-span-4">
                  <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                  <input type="text" id="name" v-model="profileForm.name" required :disabled="loading"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                </div>

                <!-- Email -->
                <div class="col-span-6 sm:col-span-4">
                  <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                  <input type="email" id="email" v-model="profileForm.email" required :disabled="loading"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                </div>

                <!-- Password -->
                <div class="col-span-6 sm:col-span-3">
                  <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                  <input type="password" id="password" v-model="profileForm.password" :disabled="loading"
                    placeholder="••••••••"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                </div>

                <!-- Confirm Password -->
                <div class="col-span-6 sm:col-span-3">
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New
                    Password</label>
                  <input type="password" id="password_confirmation" v-model="profileForm.password_confirmation"
                    :disabled="loading" placeholder="••••••••"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                </div>
              </div>

              <div class="flex justify-end">
                <button type="submit" :disabled="loading"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:bg-indigo-400">
                  {{ loading ? 'Saving...' : 'Save Changes' }}
                </button>
                <button type="button" @click="logout"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                  <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  Logout
                </button>
              </div>
            </form>
          </div>
        </main>

      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, reactive, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import axiosClient from '../../axios'
import PublicHeader from '../../components/admin/PublicHeader.vue'






const store = useStore()
const router = useRouter()

// State management
const activeTab = ref('overview')
const loading = ref(false)
const successMessage = ref('')
const orders = ref([])
const ordersLoading = ref(false)

const currentUser = computed(() => store.getters.userData)
const cartCount = computed(() => store.getters.cartCount)
const wishlistCount = computed(() => store.getters.wishlistCount)
const ordersCount = computed(() => orders.value.length)

// Initials helper
const initials = computed(() => {
  const name = currentUser.value.name
  if (!name) return 'U'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
})

// Profile Update Form
const profileForm = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

onMounted(() => {
  profileForm.name = currentUser.value.name || ''
  profileForm.email = currentUser.value.email || ''
  fetchOrders()
})

async function fetchOrders() {
  ordersLoading.value = true
  try {
    const { data } = await axiosClient.get('/orders')
    orders.value = data.data || data
  } catch (error) {
    console.error('Error fetching orders:', error)
  } finally {
    ordersLoading.value = false
  }
}

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-50 text-yellow-700 border border-yellow-200',
    confirmed: 'bg-blue-50 text-blue-700 border border-blue-200',
    processing: 'bg-purple-50 text-purple-700 border border-purple-200',
    shipped: 'bg-indigo-50 text-indigo-700 border border-indigo-200',
    delivered: 'bg-green-50 text-green-700 border border-green-200',
    cancelled: 'bg-red-50 text-red-700 border border-red-200'
  }
  return classes[status] || 'bg-gray-50 text-gray-700 border border-gray-200'
}

async function updateProfile() {
  successMessage.value = ''
  loading.value = true

  try {
    const response = await store.dispatch('updateProfile', profileForm)
    successMessage.value = response.message
    profileForm.password = ''
    profileForm.password_confirmation = ''
  } catch (error) {
    console.error('Profile update failed:', error)
  } finally {
    loading.value = false
  }
}

// logout
function logout() {
  store.dispatch('logout')
    .then(() => {
      // Redirect to home page
      router.push('/')
    })
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.6s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
