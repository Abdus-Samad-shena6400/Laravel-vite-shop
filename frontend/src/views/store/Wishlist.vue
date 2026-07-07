<template>
  <div class="bg-gray-50 min-h-screen flex flex-col">
    <PublicHeader />

    <div class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full animate-fade-in">
      <h1 class="text-3xl font-extrabold text-gray-950 tracking-tight mb-8">My Wishlist</h1>

      <div v-if="wishlistItems.length === 0" class="text-center py-24 bg-white rounded-3xl border border-gray-150 shadow-sm space-y-6">
        <svg class="h-20 w-20 text-gray-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        <div class="space-y-2">
          <h2 class="text-xl font-bold text-gray-950">Your wishlist is empty</h2>
          <p class="text-sm text-gray-500 max-w-xs mx-auto">Save items you like to buy later!</p>
        </div>
        <div>
          <router-link 
            :to="{ name: 'shop' }" 
            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors"
          >
            Explore Shop
          </router-link>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-4 gap-8">
        <div 
          v-for="item in wishlistItems"
          :key="item.id"
          class="bg-white rounded-2xl border border-gray-150 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col"
        >
          <div class="relative h-48 bg-gray-50 overflow-hidden">
            <img 
              :src="item.product.image_url || 'https://via.placeholder.com/300?text=No+Image'" 
              :alt="item.product.name" 
              class="w-full h-full object-contain bg-gray-50"
            />
          </div>
          <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
            <div>
              <span class="text-xs font-semibold text-indigo-600 tracking-wider uppercase">{{ item.product.category_name }}</span>
              <h3 class="text-sm font-bold text-gray-950 mt-1 line-clamp-2">{{ item.product.name }}</h3>
            </div>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-lg font-black text-gray-900">${{ item.product.price }}</span>
              </div>
              <div class="flex space-x-2 pt-2 border-t border-gray-100">
                <!-- Move to Cart -->
                <button 
                  @click="moveToCart(item)"
                  class="flex-grow py-2 px-3 text-center text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors cursor-pointer"
                >
                  Add to Cart
                </button>
                <!-- Remove from wishlist -->
                <button 
                  @click="remove(item.id)"
                  class="p-2 text-gray-400 hover:text-red-500 rounded-lg border border-gray-200 hover:border-red-100 transition-colors"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <transition enter-active-class="transition duration-300" leave-active-class="transition duration-300"
      enter-from-class="opacity-0 translate-y-4" leave-to-class="opacity-0 translate-y-4">
      <div v-if="toast.show" class="fixed top-5 right-5 z-50">
        <div class="px-6 py-4 rounded-lg shadow-xl text-white" :class="toast.type === 'success'
          ? 'bg-green-600'
          : 'bg-red-600'">
          {{ toast.message }}
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useStore } from 'vuex'
import PublicHeader from '../../components/admin/PublicHeader.vue'

const store = useStore()

const toast = ref({
    show: false,
    message: '',
    type: 'success'
})

const showToast = (message, type = 'success') => {
    toast.value = {
        show: true,
        message: message,
        type: type
    }
    setTimeout(() => {
        toast.value.show = false
    }, 3000)
}

onMounted(() => {
    if (store.getters.userToken) {
        store.dispatch('loadWishlist')
    }
})

const wishlistItems = computed(() => store.getters.wishlistItems)
const isInWishlist = computed(() => (productId) => {
    return store.getters.wishlistItems.some(
        item => item.product.id === productId
    )
})

const remove = async (wishlistId) => {
    try {
        const response = await store.dispatch('removeFromWishlist', wishlistId)
        showToast(response.message || 'Item removed from wishlist successfully.')
    } catch (error) {
        console.error(error)
        showToast(
            error.response?.data?.message ||
            'Unable to remove item from wishlist.',
            'error'
        )
    }
}

const moveToCart = async (item) => {
    try {
        const cartResponse = await store.dispatch('addToCart', {
            productId: item.product.id,
            quantity: 1
        })
        showToast(cartResponse.message || 'Product added to cart successfully.')

        const wishlistResponse = await store.dispatch('removeFromWishlist', item.id)
    } catch (error) {
        console.error(error)
        showToast(
            error.response?.data?.message ||
            'Unable to move item to cart.',
            'error'
        )
    }
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
