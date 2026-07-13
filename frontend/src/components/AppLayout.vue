<template>
  <div class="min-h-screen bg-gray-200 lg:flex">
    <div
      class="fixed inset-y-0 left-0 z-40 w-[220px] transform transition-transform duration-300"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <Sidebar />
    </div>

    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-30 bg-black/40 lg:hidden"
      @click="closeSidebar"
    ></div>

    <div class="flex-1 flex flex-col min-h-screen overflow-hidden transition-all duration-300" :class="sidebarOpen ? 'lg:ml-[220px]' : 'lg:ml-0'">
      <Navbar @toggle-sidebar="toggleSidebar" :sidebar-open="sidebarOpen"></Navbar>

      <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from '../components/admin/Sidebar.vue'
import Navbar from './layout/Navbar.vue'

const sidebarOpen = ref(typeof window !== 'undefined' ? window.innerWidth >= 1024 : true)

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}

function closeSidebar() {
  sidebarOpen.value = false
}
</script>

<style scoped>
</style>