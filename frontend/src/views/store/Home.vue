<template>
  <div class="bg-white min-h-screen flex flex-col">
    <!-- Shared Public Header -->
    <PublicHeader />

    
    
    <HotDealBanner/>
    <!-- Categories Section -->
    <CategoriesSection />
    <!-- Brands Section -->
    <BrandsSection />
    <!-- Featured Products Section -->
    <FeaturedProducts />
    <!-- Latest Products Section -->
    <LatestProducts />
    <!-- Why Choose Us Section -->
    <WhyChooseUs />
    <!-- Newsletter Section -->
    <Newsletter />

    <!-- Footer -->
    <footer class="bg-amber-100 text-black py-12 border-t border-gray-850">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <div class="flex items-center justify-center gap-3">
          <img src="@/assets/logo.png" alt="BLS Mart" class="h-20 w-20 object-contain" />
          <p class="text-sm">&copy; 2026 BLS Mart. All rights reserved.</p>
        </div>
        <div class="flex justify-center space-x-6 text-xs">
          <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
          <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
          <a href="#" class="hover:text-white transition-colors">Contact Support</a>
        </div>
      </div>
    </footer>
    <transition enter-active-class="transition duration-300" leave-active-class="transition duration-300"
      enter-from-class="opacity-0 translate-y-4" leave-to-class="opacity-0 translate-y-4">
      <div v-if="toast.show" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50">
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

import CategoriesSection from '../../components/home/CategoriesSection.vue'
import axiosClient from '../../axios'
import FeaturedProducts from '../../components/home/FeaturedProducts.vue'
import LatestProducts from '../../components/home/LatestProducts.vue'
import BrandsSection from '../../components/home/BrandsSection.vue'
import WhyChooseUs from '../../components/home/WhyChooseUs.vue'
import Newsletter from '../../components/home/Newsletter.vue'
import HotDeal from '../admin/HotDeal.vue'
import HotDealBanner from '@/components/home/HotDealBanner.vue'
const store = useStore()

const toast = ref({
    show: false,
    message: '',
    type: 'success'
})

const loading = ref(false)

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

// Get first 8 products as featured items
const featuredProducts = computed(() =>
  store.getters.allProducts.slice(0, 8)
)

const latestProducts = computed(() =>
  store.getters.allProducts.slice(8, 16)
)

const brands = computed(() =>
  store.getters.allBrands
)

// Helpers to check if item is in wishlist
const isInWishlist = (id) => store.getters.isInWishlist(id)

// Dispatch store actions
const addToCart = async (product) => {
    if (loading.value) return
    loading.value = true
    try {
        await store.dispatch('addToCart', {
            productId: product.id,
            quantity: 1
        })
        showToast('Product added to cart successfully.')
    } catch (error) {
        console.error(error)
        showToast(
            error.message ||
            error.response?.data?.message ||
            'Unable to add product to cart.',
            'error'
        )
    } finally {
        loading.value = false
    }
}
const toggleWishlist = async (product) => {
    try {
        const response = await store.dispatch('toggleWishlist', product.id)
        showToast(response.message || 'Wishlist updated successfully.')
    } catch (error) {
        console.error(error)
        showToast(
            error.message ||
            error.response?.data?.message ||
            'Unable to update wishlist.',
            'error'
        )
    }
}

const categories = computed(() => store.getters.allCategories)

onMounted(async () => {

  const productsResponse = await axiosClient.get('/store/products')
  store.commit('SET_PRODUCTS', productsResponse.data.data ?? productsResponse.data)

  const response = await axiosClient.get('/store/categories')
  store.commit('SET_CATEGORIES', response.data)

  await store.dispatch('fetchBrands')

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
