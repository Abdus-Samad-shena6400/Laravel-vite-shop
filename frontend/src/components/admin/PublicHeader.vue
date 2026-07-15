<template>
  <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="container-standard">
      <div class="flex justify-between h-16 items-center">
        <!-- Logo and Main Navigation -->
        <div class="flex items-center space-x-4 sm:space-x-8">
          <router-link :to="{ name: 'home' }"
            class="flex items-center gap-2 sm:gap-3 hover:scale-105 transition-transform duration-300">
            <img src="@/assets/logo.png" alt="BLS Mart" class="h-20 w-20 sm:h-24 sm:w-24 object-contain" />
            <span class="text-xl sm:text-2xl font-extrabold text-indigo-600 tracking-tight">BLS Mart</span>
          </router-link>
          <div class="hidden md:flex space-x-1">
            <router-link :to="{ name: 'home' }"
              class="nav-link text-sm font-medium px-4 py-2 rounded-lg transition-all duration-300 relative group"
              :class="isActiveRoute('home') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'">
              <span class="relative z-10">Home</span>
              <span v-if="!isActiveRoute('home')"
                class="absolute inset-0 bg-indigo-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </router-link>
            <router-link :to="{ name: 'shop' }"
              class="nav-link text-sm font-medium px-4 py-2 rounded-lg transition-all duration-300 relative group"
              :class="isActiveRoute('shop') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'">
              <span class="relative z-10">Shop</span>
              <span v-if="!isActiveRoute('shop')"
                class="absolute inset-0 bg-indigo-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </router-link>
            <router-link :to="{ name: 'contact' }"
              class="nav-link text-sm font-medium px-4 py-2 rounded-lg transition-all duration-300 relative group"
              :class="isActiveRoute('contact') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'">
              <span class="relative z-10">Contact</span>
              <span v-if="!isActiveRoute('contact')"
                class="absolute inset-0 bg-indigo-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </router-link>
            <router-link :to="{ name: 'about' }"
              class="nav-link text-sm font-medium px-4 py-2 rounded-lg transition-all duration-300 relative group"
              :class="isActiveRoute('about') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'">
              <span class="relative z-10">About</span>
              <span v-if="!isActiveRoute('about')"
                class="absolute inset-0 bg-indigo-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </router-link>
          </div>
        </div>

        <!-- Icons and User Auth actions -->
        <div class="flex items-center space-x-2 sm:space-x-4">
          <button class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100" @click="toggleMobileMenu"
            aria-label="Toggle menu">
            <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <div class="relative">
            <button @click="showNotifications = !showNotifications"
              class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300"
              ref="notificationButton">
              🔔
              <span v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full px-2 text-xs">
                {{ unreadCount }}
              </span>
            </button>
            
            <!-- Notification Dropdown -->
            <div v-if="showNotifications"
              class="absolute right-0 top-full mt-2 w-80 sm:w-96 md:w-96 lg:w-[28rem] bg-white shadow-2xl rounded-xl z-50 max-h-[70vh] sm:max-h-96 overflow-y-auto border border-gray-100">
              <!-- Header -->
              <div class="sticky top-0 bg-white border-b border-gray-100 px-4 py-3 sm:px-5 sm:py-4 z-10">
                <div class="flex items-center justify-between">
                  <h3 class="font-bold text-gray-800 text-base sm:text-lg">
                    Notifications
                    <span v-if="unreadCount > 0" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      {{ unreadCount }}
                    </span>
                  </h3>

                  <div class="flex items-center gap-2 sm:gap-3">
                    <button @click="clearAllNotifications" class="text-xs sm:text-sm text-red-500 hover:text-red-700 font-medium transition-colors">
                      Clear All
                    </button>

                    <button @click="showNotifications = false" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Notifications List -->
              <div class="divide-y divide-gray-50">
                <div v-for="notification in uniqueNotifications" :key="notification.id" @click="markAsRead(notification)"
                  class="p-3 sm:p-4 cursor-pointer hover:bg-gray-50 transition-all duration-200"
                  :class="{ 'bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500': !notification.is_read, 'border-l-4 border-transparent': notification.is_read }">
                  <div class="flex justify-between items-start gap-3">
                    <div class="flex-1 min-w-0">
                      <div class="font-semibold text-gray-900 text-sm sm:text-base truncate">
                        {{ notification.title }}
                      </div>

                      <div class="text-sm text-gray-600 mt-1 line-clamp-2">
                        {{ notification.message }}
                      </div>

                      <div class="flex items-center gap-2 mt-2">
                        <div class="text-xs text-gray-400">
                          {{ formatDate(notification.created_at) }}
                        </div>
                        <div v-if="!notification.is_read" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                          New
                        </div>
                      </div>
                    </div>

                    <button @click.stop="deleteNotification(notification.id)"
                      class="flex-shrink-0 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                      <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Empty State -->
              <div v-if="uniqueNotifications.length === 0" class="p-8 sm:p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                  <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                  </svg>
                </div>
                <p class="text-gray-500 font-medium">No notifications</p>
                <p class="text-gray-400 text-sm mt-1">You're all caught up!</p>
              </div>
            </div>
          </div>
          <!-- Wishlist Link -->
          <router-link :to="{ name: 'wishlist' }"
            class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300 transform hover:scale-110">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span v-if="wishlistCount > 0"
              class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white rounded-full text-2xs h-5 w-5 flex items-center justify-center font-bold animate-bounce-short">
              {{ wishlistCount }}
            </span>
          </router-link>

          <!-- Cart Link -->
          <router-link :to="{ name: 'cart' }"
            class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300 transform hover:scale-110">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span v-if="cartCount > 0"
              class="absolute -top-1.5 -right-1.5 bg-indigo-600 text-white rounded-full text-2xs h-5 w-5 flex items-center justify-center font-bold animate-bounce-short">
              {{ cartCount }}
            </span>
          </router-link>

          <!-- Divider -->
          <span class="hidden md:block h-6 w-px bg-gray-200" aria-hidden="true"></span>

          <!-- Auth States -->
          <template v-if="isAuthenticated">
            <router-link :to="dashboardRoute"
              class="hidden md:inline-flex text-sm font-medium text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-lg hover:bg-indigo-50 transition-all duration-300">
              Dashboard
            </router-link>
          </template>
          <template v-else>
            <router-link :to="{ name: 'login' }"
              class="hidden md:inline-flex text-sm font-medium text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-lg hover:bg-indigo-50 transition-all duration-300">
              Sign in
            </router-link>
            <router-link :to="{ name: 'register' }"
              class="hidden md:inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm hover:shadow-md hover:scale-105 transition-all duration-300">
              Sign up
            </router-link>
          </template>
        </div>
      </div>

      <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-100 bg-white px-3 py-4 space-y-2">
        <router-link :to="{ name: 'home' }"
          class="block px-3 py-3 text-sm font-medium rounded-lg transition-all duration-300"
          :class="isActiveRoute('home') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50'"
          @click="closeMobileMenu">Home</router-link>
        <router-link :to="{ name: 'shop' }"
          class="block px-3 py-3 text-sm font-medium rounded-lg transition-all duration-300"
          :class="isActiveRoute('shop') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50'"
          @click="closeMobileMenu">Shop</router-link>
        <router-link :to="{ name: 'contact' }"
          class="block px-3 py-3 text-sm font-medium rounded-lg transition-all duration-300"
          :class="isActiveRoute('contact') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50'"
          @click="closeMobileMenu">Contact</router-link>
        <router-link :to="{ name: 'about' }"
          class="block px-3 py-3 text-sm font-medium rounded-lg transition-all duration-300"
          :class="isActiveRoute('about') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50'"
          @click="closeMobileMenu">About</router-link>
        <div class="pt-3 border-t border-gray-100 space-y-2">
          <template v-if="isAuthenticated">
            <router-link :to="dashboardRoute"
              class="block px-3 py-3 text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300"
              @click="closeMobileMenu">Dashboard</router-link>
          </template>
          <template v-else>
            <router-link :to="{ name: 'login' }"
              class="block px-3 py-3 text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300"
              @click="closeMobileMenu">Sign in</router-link>
            <router-link :to="{ name: 'register' }"
              class="block px-3 py-3 text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300"
              @click="closeMobileMenu">Sign up</router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'
import axiosClient from '../../axios'

const store = useStore()
const route = useRoute()

const notifications = ref([])
const showNotifications = ref(false)

const isAuthenticated = computed(() => !!store.getters.userToken)

const fetchNotifications = async () => {
  if (!isAuthenticated.value) return
  const response = await axiosClient.get('/notifications')
  notifications.value = response.data
}

const uniqueNotifications = computed(() => {
  const seen = new Set()
  return notifications.value.filter(notification => {
    const key = `${notification.title}-${notification.message}`
    if (seen.has(key)) {
      return false
    }
    seen.add(key)
    return true
  })
})

const unreadCount = computed(() => {
  return uniqueNotifications.value.filter(
    notification => !notification.is_read
  ).length
})

const markAsRead = async (notification) => {
  if (!notification.is_read) {
    await axiosClient.put(`/notifications/${notification.id}/read`)
    notification.is_read = true
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleString()
}

onMounted(() => {
  fetchNotifications()

  setInterval(() => {
    fetchNotifications()
  }, 1000)
})

const clearAllNotifications = async () => {
  try {
    await axiosClient.delete('/notifications');

    notifications.value = [];
  } catch (error) {
    console.error(error);
  }
}

const deleteNotification = async (notificationId) => {
  try {
    await axiosClient.delete(
      `/notifications/${notificationId}`
    );

    notifications.value = notifications.value.filter(
      notification => notification.id !== notificationId
    );
  } catch (error) {
    console.error(error);
  }
};


const mobileMenuOpen = ref(false)
const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}
const closeMobileMenu = () => {
  mobileMenuOpen.value = false
}

const cartCount = computed(() => store.getters.cartCount)
const wishlistCount = computed(() => store.getters.wishlistCount)

const currentUser = computed(() => store.getters.userData)
// Dynamically compute the route destination depending on user roles
const dashboardRoute = computed(() => {
  return currentUser.value.is_admin ? { name: 'dashboard' } : { name: 'user-dashboard' }
})

const isActiveRoute = (routeName) => {
  return route.name === routeName
}
</script>

<style scoped>
.text-2xs {
  font-size: 0.65rem;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.animate-bounce-short {
  animation: bounce 1s ease-in-out 1;
}

@keyframes bounce {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-4px);
  }
}
</style>
