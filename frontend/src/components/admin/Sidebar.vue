<template>
  <div class="w-[200px] bg-gray-900 text-gray-300 flex flex-col transition-all duration-300 shadow-xl border-r border-gray-800">
    <!-- Brand / Header -->
    <div class="h-16 flex items-center px-4 bg-gray-950 font-semibold text-white tracking-wider border-b border-gray-800">
      <span class="text-indigo-400 mr-2 text-xl font-black">🛍️</span>
      <span class="text-sm">E-Shop Admin</span>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
      <template v-for="item in navigation" :key="item.name">
        <!-- Item with children (dropdown) -->
        <div v-if="item.children">
          <button
            @click="toggleDropdown(item.name)"
            class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group"
            :class="[
              isDropdownActive(item)
                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30'
                : 'text-gray-400 hover:bg-gray-800/60 hover:text-white'
            ]"
          >
            <div class="flex items-center">
              <component
                :is="item.icon"
                class="mr-3 h-5 w-5 flex-shrink-0 transition-colors duration-200"
                :class="[
                  isDropdownActive(item)
                    ? 'text-white'
                    : 'text-gray-400 group-hover:text-white'
                ]"
                aria-hidden="true"
              />
              <span>{{ item.name }}</span>
            </div>
            <svg
              class="h-4 w-4 transition-transform duration-200"
              :class="{ 'rotate-180': openDropdowns[item.name] }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </button>
          <!-- Dropdown children -->
          <div
            v-show="openDropdowns[item.name]"
            class="mt-1 ml-4 space-y-1"
          >
            <router-link
              v-for="child in item.children"
              :key="child.name"
              :to="child.to"
              class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 group"
              :class="[
                isRouteActive(child)
                  ? 'bg-indigo-600/50 text-white'
                  : 'text-gray-400 hover:bg-gray-800/60 hover:text-white'
              ]"
            >
              <span>{{ child.name }}</span>
            </router-link>
          </div>
        </div>
        <!-- Regular item without children -->
        <router-link
          v-else
          :to="item.to"
          class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group"
          :class="[
            isRouteActive(item)
              ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30'
              : 'text-gray-400 hover:bg-gray-800/60 hover:text-white'
          ]"
        >
          <component
            :is="item.icon"
            class="mr-3 h-5 w-5 flex-shrink-0 transition-colors duration-200"
            :class="[
              isRouteActive(item)
                ? 'text-white'
                : 'text-gray-400 group-hover:text-white'
            ]"
            aria-hidden="true"
          />
          <span>{{ item.name }}</span>
        </router-link>
      </template>
    </nav>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import {
    HomeIcon,
    ShoppingCartIcon,
    UsersIcon,
    ChartBarIcon,
    ClipboardListIcon,
    UserGroupIcon,
    CollectionIcon,
    TagIcon,
    MailIcon,
    StarIcon
} from '@heroicons/vue/outline'

const route = useRoute()

const openDropdowns = ref({})

const navigation = [
  {
    name: 'Dashboard',
    icon: HomeIcon,
    to: { name: 'dashboard' }
  },

  {
    name: 'Category',
    icon: CollectionIcon,
    children: [
      {
        name: 'All Categories',
        to: { name: 'categories' }
      },
      {
        name: 'Add Category',
        to: { name: 'category.create' }
      }
    ]
  },

   {
    name: 'Brand',
    icon: TagIcon,
    children: [
      {
        name: 'All Brands',
        to: { name: 'brands' }
      },
      {
        name: 'Add Brand',
        to: { name: 'brand.create' }
      }
    ]
  }, 

  {
    name: 'Products',
    icon: ShoppingCartIcon,
    children: [
      {
        name: 'All Products',
        to: { name: 'products' }
      },
      {
        name: 'Add Product',
        to: { name: 'product.create' }
      }
    ]
  },
   {
    name: 'Coupons',
    icon: TagIcon,
    to: { name: 'coupons' }
  },

  {
    name: 'Orders',
    icon: ClipboardListIcon,
    to: { name: 'orders' }
  },
  
  {
    name: 'Reviews',
    icon: StarIcon,
    to: { name: 'reviews' }
  },

  {
    name: 'Customers',
    icon: UserGroupIcon,
    to: { name: 'customers' }
  },

   {
    name: 'Reports',
    icon: ChartBarIcon,
    to: { name: 'reports' }
  },

  {
    name: 'Contacts',
    icon: MailIcon,
    to: { name: 'contacts' }
  },

]

watch(
  () => route.path,
  () => {
    navigation.forEach(item => {
      if (item.children) {
        openDropdowns.value[item.name] =
          item.children.some(child => isRouteActive(child))
      }
    })
  },
  { immediate: true }
)

function toggleDropdown(name) {
  openDropdowns.value[name] = !openDropdowns.value[name]
}

function isDropdownActive(item) {
  if (!item.children) return false

  const active = item.children.some(child => isRouteActive(child))

  if (active) {
    openDropdowns.value[item.name] = true
  }

  return active
}

function isRouteActive(item) {
  if (item.to?.name && route.name === item.to.name) return true
  if (typeof item.to === 'string' && route.path.startsWith(item.to)) return true
  return false
}
</script>

<style scoped>
</style>
