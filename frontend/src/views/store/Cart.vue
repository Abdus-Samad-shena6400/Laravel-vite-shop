<template>
  <div class="bg-gray-50 min-h-screen flex flex-col">
    <PublicHeader />

    <div class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full animate-fade-in">
      <h1 class="text-3xl font-extrabold text-gray-950 tracking-tight mb-8">Shopping Cart</h1>

      <div v-if="cartItems.length === 0"
        class="text-center py-24 bg-white rounded-3xl border border-gray-150 shadow-sm space-y-6">
        <svg class="h-20 w-20 text-gray-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <div class="space-y-2">
          <h2 class="text-xl font-bold text-gray-950">Your cart is empty</h2>
          <p class="text-sm text-gray-500 max-w-xs mx-auto">Fill it with high-quality tech, premium clothing, and modern
            housewares!</p>
        </div>
        <div>
          <router-link :to="{ name: 'shop' }"
            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
            Continue Shopping
          </router-link>
        </div>
      </div>

      <div v-else class="lg:grid lg:grid-cols-12 lg:gap-8 lg:items-start">
        <!-- Cart Items List -->
        <section class="lg:col-span-8 space-y-4">
          <div v-for="item in cartItems" :key="item.id"
            class="bg-white p-6 rounded-2xl border border-gray-150 shadow-xs flex items-center space-x-6 hover:shadow-sm transition-all">
            <img :src="item.product.image_url || 'https://via.placeholder.com/100?text=No+Image'"
              :alt="item.product.name" class="w-20 h-20 rounded-xl object-contain bg-gray-50">
            <div class="flex-grow flex flex-col sm:flex-row sm:justify-between space-y-4 sm:space-y-0">
              <div class="space-y-1">
                <h3 class="text-sm font-bold text-gray-950">{{ item.product.name }}</h3>
                <p class="text-xs text-gray-400">{{ item.product.category_name }}</p>
                <p class="text-sm font-extrabold text-indigo-600 mt-2">${{ item.product.price }}</p>
                <p v-if="item.product.quantity > 0" class="text-green-600 text-xs font-semibold mt-1">

                  In Stock ({{ item.product.quantity }})

                </p>

                <p v-else class="text-red-600 text-xs font-semibold mt-1">

                  Out of Stock

                </p>
              </div>

              <div class="flex items-center space-x-6 self-start sm:self-center">
                <!-- Quantity controls -->
                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-xs">
                  <button @click="
                    item.quantity < item.product.stock &&
                    updateQuantity(item.id, item.quantity + 1)
                    " class="px-3 py-1.5 hover:bg-gray-50 text-gray-500 font-bold transition-colors">
                    +
                  </button>
                  <span class="px-4 py-1.5 text-sm font-bold text-gray-800">{{ item.quantity }}</span>
                  <button @click="
                    item.quantity > 1 &&
                    updateQuantity(item.id, item.quantity - 1)
                    " class="px-3 py-1.5 hover:bg-gray-50 text-gray-500 font-bold transition-colors">
                    -
                  </button>
                </div>

                <!-- Remove Button -->
                <button @click="removeItem(item.id)"
                  class="p-2 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50/50 transition-colors">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>

                <!-- Wishlist Button -->
                <button @click="moveToWishlist(item)"
                  class="p-2 text-gray-400 hover:text-rose-500 rounded-lg hover:bg-rose-50/50 transition-colors"
                  title="Move to Wishlist">
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </section>

        <!-- Order Summary Card -->
        <section
          class="mt-10 lg:mt-0 lg:col-span-4 bg-white p-6 rounded-2xl border border-gray-150 shadow-xs space-y-6">
          <p class="text-gray-500 text-sm">
            {{ cartItems.length }} item(s) in your cart
          </p>
          <h2 class="text-lg font-extrabold text-gray-950">Order Summary</h2>

          <div class="space-y-4 divide-y divide-gray-100 text-sm">
            <div class="flex justify-between text-gray-600">
              <span>Subtotal</span>
              <span class="font-semibold text-gray-950">${{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-gray-600 pt-4">
              <span>Estimated Shipping</span>
              <span class="font-semibold text-gray-950">${{ shipping.toFixed(2) }}</span>
            </div>
            <div v-if="shipping === 0" class="bg-green-100 text-green-700 rounded-lg p-3 text-sm">

              🎉 Congratulations!

              You qualified for FREE Shipping.

            </div>
            <div class="flex justify-between text-gray-600 pt-4">
              <span>Estimated Tax (10%)</span>
              <span class="font-semibold text-gray-950">${{ tax.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-base font-extrabold text-gray-950 pt-4">
              <span>Total</span>
              <span class="text-lg font-black text-indigo-600">${{ total.toFixed(2) }}</span>
            </div>
          </div>

          <div>
            <router-link :to="{ name: 'checkout' }"
              class="block w-full text-center py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold">
              Proceed to Checkout →
            </router-link>
          </div>
        </section>
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

const cartItems = computed(() => store.getters.cartItems)
const subtotal = computed(() => store.getters.cartTotal)

// Mock Shipping & Taxes
const shipping = computed(() => (subtotal.value > 150 ? 0 : 15))
const tax = computed(() => subtotal.value * 0.1)
const total = computed(() => subtotal.value + shipping.value + tax.value)

// Mutate Store Actions
const removeItem = async (cartItemId) => {
    try {
        const response = await store.dispatch('removeFromCart', cartItemId)
        showToast(response.message || 'Item removed from cart successfully.')
    } catch (error) {
        console.error(error)
        showToast(
            error.response?.data?.message ||
            'Unable to remove item from cart.',
            'error'
        )
    }
}

const updateQuantity = async (cartItemId, quantity) => {
    try {
        const response = await store.dispatch('updateCartQuantity', {
            cartItemId,
            quantity
        })
        showToast(response.message || 'Cart updated successfully.')
    } catch (error) {
        console.error(error)
        showToast(
            error.response?.data?.message ||
            'Unable to update cart.',
            'error'
        )
    }
}

const moveToWishlist = async (item) => {
    try {
        // Add to wishlist first
        const wishlistResponse = await store.dispatch('addToWishlist', item.product.id)
        showToast(wishlistResponse.message || 'Item moved to wishlist successfully.')

        // Then remove from cart
        const cartResponse = await store.dispatch('removeFromCart', item.id)
    } catch (error) {
        console.error(error)
        showToast(
            error.response?.data?.message ||
            error.message ||
            'Unable to move item to wishlist.',
            'error'
        )
    }
}

onMounted(() => {
  if (store.getters.userToken) {
    store.dispatch('loadCart')
  }
})
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
