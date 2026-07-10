<template>
  <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="container-standard">
      <div class="flex justify-between h-16 items-center">
        <!-- Logo and Main Navigation -->
        <div class="flex items-center space-x-4 sm:space-x-8">
          <router-link :to="{ name: 'home' }" class="text-xl sm:text-2xl font-extrabold text-indigo-600 tracking-tight">
            E-Shop
          </router-link>
          <div class="hidden md:flex space-x-6">
            <router-link :to="{ name: 'home' }"
              class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">Home</router-link>
            <router-link :to="{ name: 'shop' }"
              class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">Shop</router-link>
            <router-link :to="{ name: 'contact' }"
              class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">Contact</router-link>
            <router-link :to="{ name: 'about' }"
              class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">About</router-link>
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
            class="relative p-1 text-gray-500 hover:text-indigo-600 transition-colors">
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
            class="relative p-1 text-gray-500 hover:text-indigo-600 transition-colors">
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
              class="hidden md:inline-flex text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
              Dashboard
            </router-link>
          </template>
          <template v-else>
            <router-link :to="{ name: 'login' }"
              class="hidden md:inline-flex text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
              Sign in
            </router-link>
            <router-link :to="{ name: 'register' }"
              class="hidden md:inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
              Sign up
            </router-link>
          </template>
        </div>
      </div>

      <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-100 bg-white px-3 py-4 space-y-3">
        <router-link :to="{ name: 'home' }" class="block px-2 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600" @click="closeMobileMenu">Home</router-link>
        <router-link :to="{ name: 'shop' }" class="block px-2 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600" @click="closeMobileMenu">Shop</router-link>
        <router-link :to="{ name: 'contact' }" class="block px-2 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600" @click="closeMobileMenu">Contact</router-link>
        <router-link :to="{ name: 'about' }" class="block px-2 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600" @click="closeMobileMenu">About</router-link>
        <div class="pt-2 border-t border-gray-100">
          <template v-if="isAuthenticated">
            <router-link :to="dashboardRoute" class="block px-2 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600" @click="closeMobileMenu">Dashboard</router-link>
          </template>
          <template v-else>
            <router-link :to="{ name: 'login' }" class="block px-2 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600" @click="closeMobileMenu">Sign in</router-link>
            <router-link :to="{ name: 'register' }" class="block px-2 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600" @click="closeMobileMenu">Sign up</router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

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
