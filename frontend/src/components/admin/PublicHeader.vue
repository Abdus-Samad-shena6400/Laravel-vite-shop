<template>
  <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="container-standard">
      <div class="flex justify-between h-16 items-center">
        <!-- Logo and Main Navigation -->
        <div class="flex items-center space-x-4 sm:space-x-8">
          <router-link :to="{ name: 'home' }" class="flex items-center gap-2 sm:gap-3 hover:scale-105 transition-transform duration-300">
            <img src="@/assets/logo.png" alt="BLS Mart" class="h-20 w-20 sm:h-24 sm:w-24 object-contain" />
            <span class="text-xl sm:text-2xl font-extrabold text-indigo-600 tracking-tight">BLS Mart</span>
          </router-link>
          <div class="hidden md:flex space-x-1">
            <router-link :to="{ name: 'home' }"
              class="nav-link text-sm font-medium px-4 py-2 rounded-lg transition-all duration-300 relative group"
              :class="isActiveRoute('home') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'">
              <span class="relative z-10">Home</span>
              <span v-if="!isActiveRoute('home')" class="absolute inset-0 bg-indigo-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </router-link>
            <router-link :to="{ name: 'shop' }"
              class="nav-link text-sm font-medium px-4 py-2 rounded-lg transition-all duration-300 relative group"
              :class="isActiveRoute('shop') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'">
              <span class="relative z-10">Shop</span>
              <span v-if="!isActiveRoute('shop')" class="absolute inset-0 bg-indigo-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </router-link>
            <router-link :to="{ name: 'contact' }"
              class="nav-link text-sm font-medium px-4 py-2 rounded-lg transition-all duration-300 relative group"
              :class="isActiveRoute('contact') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'">
              <span class="relative z-10">Contact</span>
              <span v-if="!isActiveRoute('contact')" class="absolute inset-0 bg-indigo-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </router-link>
            <router-link :to="{ name: 'about' }"
              class="nav-link text-sm font-medium px-4 py-2 rounded-lg transition-all duration-300 relative group"
              :class="isActiveRoute('about') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'">
              <span class="relative z-10">About</span>
              <span v-if="!isActiveRoute('about')" class="absolute inset-0 bg-indigo-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </router-link>
          </div>
        </div>

        <!-- Icons and User Auth actions -->
        <div class="flex items-center space-x-2 sm:space-x-4">
          <button class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100" @click="toggleMobileMenu" aria-label="Toggle menu">
            <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

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
        <router-link :to="{ name: 'home' }" class="block px-3 py-3 text-sm font-medium rounded-lg transition-all duration-300" :class="isActiveRoute('home') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50'" @click="closeMobileMenu">Home</router-link>
        <router-link :to="{ name: 'shop' }" class="block px-3 py-3 text-sm font-medium rounded-lg transition-all duration-300" :class="isActiveRoute('shop') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50'" @click="closeMobileMenu">Shop</router-link>
        <router-link :to="{ name: 'contact' }" class="block px-3 py-3 text-sm font-medium rounded-lg transition-all duration-300" :class="isActiveRoute('contact') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50'" @click="closeMobileMenu">Contact</router-link>
        <router-link :to="{ name: 'about' }" class="block px-3 py-3 text-sm font-medium rounded-lg transition-all duration-300" :class="isActiveRoute('about') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50'" @click="closeMobileMenu">About</router-link>
        <div class="pt-3 border-t border-gray-100 space-y-2">
          <template v-if="isAuthenticated">
            <router-link :to="dashboardRoute" class="block px-3 py-3 text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300" @click="closeMobileMenu">Dashboard</router-link>
          </template>
          <template v-else>
            <router-link :to="{ name: 'login' }" class="block px-3 py-3 text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300" @click="closeMobileMenu">Sign in</router-link>
            <router-link :to="{ name: 'register' }" class="block px-3 py-3 text-sm font-medium text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300" @click="closeMobileMenu">Sign up</router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'

const store = useStore()
const route = useRoute()

const mobileMenuOpen = ref(false)
const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}
const closeMobileMenu = () => {
  mobileMenuOpen.value = false
}

const isAuthenticated = computed(() => !!store.getters.userToken)
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
