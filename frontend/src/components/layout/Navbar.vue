<template>
  <header class="h-16 flex items-center justify-between bg-white shadow-sm border-b border-gray-150 px-4 sm:px-6">
    <!-- Left side: Toggle button and Logo (shown when sidebar is closed) -->
    <div class="flex items-center space-x-4">
      <button
        @click="emit('toggle-sidebar')"
        class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-700 transition-colors focus:outline-none"
      >
        <MenuIcon class="h-6 w-6" />
      </button>
      <!-- Logo and Brand Name - only show when sidebar is closed -->
      <div v-if="!sidebarOpen" class="flex items-center space-x-1">
        <img src="@/assets/logo.png" alt="BLS Mart" class="h-20 w-20 object-contain"/>
        <span class="text-xl font-bold text-gray-800">BLS Mart Admin</span>
      </div>
    </div>

    <!-- Right side: Profile Menu -->
    <div class="flex items-center space-x-4">
      <div class="relative inline-block text-left dropdown-container">
        <div>
          <!-- Toggle dropdown on click -->
          <button 
            @click="isOpen = !isOpen"
            class="flex items-center space-x-3 focus:outline-none group cursor-pointer"
          >
            <!-- Dynamic User Initials -->
            <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-sm shadow-sm ring-2 ring-transparent group-hover:ring-indigo-100 transition-all">
              {{ userInitials }}
            </div>
            <!-- Dynamic User Name -->
            <span class="hidden sm:block text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">
              {{ currentUser.name || 'User' }}
            </span>
            <ChevronDownIcon class="h-4 w-4 text-gray-400 group-hover:text-gray-600 transition-colors" />
          </button>
        </div>

        <!-- Custom Dropdown Menu -->
        <transition
          enter-active-class="transition duration-100 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-75 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <div 
            v-if="isOpen"
            class="absolute right-0 w-48 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-xl shadow-lg border border-gray-100 focus:outline-none z-50"
          >
            <div class="px-1 py-1">
              <router-link
                :to="{ name: 'profile' }"
                @click="isOpen = false"
                class="group flex items-center w-full px-3 py-2 text-sm rounded-lg text-gray-700 hover:bg-indigo-600 hover:text-white transition-colors"
              >
                <UserIcon class="mr-2 h-4 w-4 text-gray-400 group-hover:text-white transition-colors" />
                Profile
              </router-link>
            </div>
            <div class="px-1 py-1">
              <button
                @click="logout"
                class="group flex items-center w-full px-3 py-2 text-sm rounded-lg text-gray-700 hover:bg-red-600 hover:text-white transition-colors text-left"
              >
                <LogoutIcon class="mr-2 h-4 w-4 text-gray-400 group-hover:text-white transition-colors" />
                Logout
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { MenuIcon, ChevronDownIcon, UserIcon, LogoutIcon } from '@heroicons/vue/outline'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const router = useRouter()
const store = useStore()
const emit = defineEmits(['toggle-sidebar'])
const props = defineProps({
  sidebarOpen: {
    type: Boolean,
    default: true
  }
})

// Dropdown visibility state
const isOpen = ref(false)

// Fetch current user details reactively from Vuex
const currentUser = computed(() => store.getters.userData)

// Compute user initials (e.g. "John Doe" -> "JD")
const userInitials = computed(() => {
  const name = currentUser.value.name
  if (!name) return 'U'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
})

// Close dropdown when clicking outside
const closeDropdown = (event) => {
  if (isOpen.value && !event.target.closest('.dropdown-container')) {
    isOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  window.removeEventListener('click', closeDropdown)
})

async function logout() {
  isOpen.value = false
  await store.dispatch('logout')
  router.push({ name: 'login' })
}
</script>

<style scoped>
</style>
