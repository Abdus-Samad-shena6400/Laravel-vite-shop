<template>

    <div
        class="group bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl transition duration-300 flex flex-col relative"
    >

        <!-- Wishlist -->

        <button
            @click="toggleWishlist(product)"
            class="absolute top-4 right-4 z-30 p-2 rounded-full bg-white shadow hover:bg-gray-100"
        >

            <span v-if="isInWishlist(product.id)" class="text-rose-500">❤️</span>
            <span v-else class="text-gray-400">🤍</span>

        </button>

        <!-- Image -->

        <div class="h-56 bg-gray-100 overflow-hidden">

            <img
                :src="product.image_url || 'https://via.placeholder.com/300?text=No+Image'"
                :alt="product.name"
                class="w-full h-full object-contain group-hover:scale-105 transition duration-500"
            >

        </div>

        <!-- Content -->

        <div class="p-5 flex flex-col flex-1">

            <span class="text-xs text-indigo-600 font-semibold uppercase">

                {{ product.category?.name }}

            </span>

            <h3 class="font-bold text-gray-900 mt-2">

                {{ product.name }}

            </h3>

            <div class="flex items-center gap-2 mt-2">
                <StarRating :rating="product.average_rating || 0" size="sm" />
                <span v-if="showRatingDetails" class="text-sm font-semibold text-gray-700">
                    {{ (product.average_rating || 0).toFixed(1) }}
                </span>
                <span class="text-sm text-gray-500">({{ product.review_count || 0 }})</span>
            </div>

            <p
                class="text-sm text-gray-500 mt-2 line-clamp-2"
            >

                {{ product.description }}

            </p>

            <div class="mt-auto">

                <div
                    class="flex items-center justify-between mt-5"
                >

                    <span class="text-2xl font-bold">

                        ${{ product.price }}

                    </span>

                    <button
                        @click="addToCart(product)"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"
                    >

                        Add

                    </button>

                </div>

                <router-link
                    :to="{
                        name:'product.show',
                        params:{ id: product.id }
                    }"
                    class="block text-center mt-4 border border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg py-2 transition"
                >

                    View Details

                </router-link>

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