<template>
  <div class="min-h-screen bg-gray-200 flex">
    <!-- Sidebar -->
    <Sidebar :class="{ '-ml-[200px]': !sidebarOpen }" />

    <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
      <!-- Navbar -->
      <Navbar @toggle-sidebar="toggleSidebar"></Navbar>

      <!-- Main Content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from '../components/admin/Sidebar.vue'
import Navbar from './layout/Navbar.vue'

const sidebarOpen = ref(true)

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}
</script>

<style scoped>
</style>