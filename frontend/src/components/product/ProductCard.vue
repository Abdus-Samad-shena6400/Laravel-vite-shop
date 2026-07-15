<template>
    <div class="card group flex flex-col relative">
        <!-- Wishlist -->
        <button
            @click="toggleWishlist(product)"
            class="absolute top-3 right-3 z-30 p-2 rounded-full bg-white shadow hover:bg-gray-100 transition-colors"
        >
            <span v-if="isInWishlist(product.id)" class="text-rose-500">❤️</span>
            <span v-else class="text-gray-400">🤍</span>
        </button>

        <!-- Image -->
        <div class="h-48 sm:h-56 bg-gray-100 overflow-hidden">
            <img
                :src="product.image_url || 'https://via.placeholder.com/300?text=No+Image'"
                :alt="product.name"
                class="w-full h-full object-contain group-hover:scale-105 transition duration-500"
            >
        </div>

        <!-- Content -->
        <div class="p-4 sm:p-5 flex flex-col flex-1">
            <span class="text-xs text-indigo-600 font-semibold uppercase">
                {{ product.category?.name }}
            </span>

            <h3 class="font-bold text-gray-900 mt-2 text-sm sm:text-base line-clamp-2">
                {{ product.name }}
            </h3>

            <div class="flex items-center gap-2 mt-2">
                <StarRating :rating="product.average_rating || 0" size="sm" />
                <span v-if="showRatingDetails" class="text-xs sm:text-sm font-semibold text-gray-700">
                    {{ (product.average_rating || 0).toFixed(1) }}
                </span>
                <span class="text-xs sm:text-sm text-gray-500">({{ product.review_count || 0 }})</span>
            </div>

            <p class="text-xs sm:text-sm text-gray-500 mt-2 line-clamp-2">
                {{ product.description }}
            </p>

            <div class="mt-auto">
                <div class="flex items-center justify-between mt-4">
                    <div>
                        <div v-if="product.hot_deal && product.deal_price">
                            <span class="text-lg sm:text-2xl font-bold text-red-600">
                                ${{ product.deal_price }}
                            </span>
                            <span class="text-sm sm:text-lg text-gray-400 line-through ml-2">
                                ${{ product.regular_price }}
                            </span>
                        </div>
                        <div v-else>
                            <span class="text-lg sm:text-2xl font-bold">
                                ${{ product.price }}
                            </span>
                        </div>
                    </div>

                    <button
                        @click="addToCart(product)"
                        :disabled="loading"
                        class="btn-primary px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ loading ? 'Adding...' : 'Add' }}
                    </button>
                </div>

                <router-link
                    :to="{
                        name:'product.show',
                        params:{ id: product.id }
                    }"
                    class="block text-center mt-3 border border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg py-2 text-xs sm:text-sm transition-colors"
                >
                    View Details
                </router-link>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <transition enter-active-class="transition duration-300" leave-active-class="transition duration-300"
      enter-from-class="opacity-0 translate-y-4" leave-to-class="opacity-0 translate-y-4">
      <div v-if="toast.show" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50">
        <div class="px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-xl text-white text-sm" :class="toast.type === 'success'
          ? 'bg-green-600'
          : 'bg-red-600'">
          {{ toast.message }}
        </div>
      </div>
    </transition>
</template>

<script setup>

import { useStore } from 'vuex'
import { ref } from 'vue'
import StarRating from '../reviews/StarRating.vue'

const props = defineProps({

    product:Object,
    showRatingDetails: {
        type: Boolean,
        default: false
    }

})

const store=useStore()

const isInWishlist = (productId) => store.getters.isInWishlist(productId)

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
        const errorMessage = error.message || error.response?.data?.message || 'Unable to add product to cart.'
        showToast(errorMessage, 'error')
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

</script>