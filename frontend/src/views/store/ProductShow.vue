<template>
    <div class="max-w-7xl mx-auto px-4 py-10">

        <LoadingSpinner v-if="loading" />

        <div v-else-if="error" class="text-center py-20">
            <div class="text-6xl mb-4">😕</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Product Not Found</h2>
            <p class="text-gray-600 mb-6">{{ error }}</p>
            <router-link to="/shop" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                Back to Shop
            </router-link>
        </div>

        <div v-else-if="product" class="grid gap-8 lg:grid-cols-2 lg:gap-12">

            <!-- Product Image -->

            <div>
                <img :src="selectedImage" :alt="product.name"
                    class="w-full rounded-2xl shadow-lg mb-4" />

                <!-- Gallery Thumbnails -->
                <div v-if="allImages.length > 0" class="grid grid-cols-4 gap-2">
                    <img v-for="(image, index) in allImages" :key="index"
                        :src="image.url"
                        :alt="product.name"
                        @click="selectedImage = image.url"
                        class="w-full h-20 object-contain rounded-lg cursor-pointer hover:opacity-80 transition"
                        :class="{ 'ring-2 ring-indigo-600': selectedImage === image.url }" />
                </div>

            </div>

            <!-- Product Information -->

            <div>

                <h1 class="text-3xl sm:text-4xl font-bold">

                    {{ product.name }}

                </h1>

                <p class="text-gray-500 mt-3">

                    {{ product.category?.name }}

                    •

                    {{ product.brand?.name }}

                </p>

                <div class="mt-6">

                    <div v-if="product.hot_deal && product.deal_price" class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                        <span class="text-3xl sm:text-4xl font-bold text-red-600">
                            ${{ product.deal_price }}
                        </span>
                        <span class="text-xl sm:text-2xl text-gray-400 line-through">
                            ${{ product.price }}
                        </span>
                        <div class="mt-2">
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-bold">
                                {{ product.discount_percentage }}% OFF
                            </span>
                            <span class="text-sm text-gray-500 ml-2">Hot Deal</span>
                        </div>
                    </div>
                    <div v-else>
                        <span class="text-3xl sm:text-4xl font-bold text-indigo-600">
                            ${{ product.price }}
                        </span>
                    </div>

                </div>

                <!-- Stock Status -->

                <div class="mt-6">

                    <span v-if="product.quantity > 0"
                        class="inline-flex items-center bg-green-100 text-green-700 px-4 py-2 rounded-full font-semibold">

                        🟢 In Stock ({{ product.quantity }} Available)

                    </span>

                    <span v-else
                        class="inline-flex items-center bg-red-100 text-red-700 px-4 py-2 rounded-full font-semibold">

                        🔴 Out of Stock

                    </span>

                </div>

                <p class="mt-8 text-gray-600 leading-7">

                    {{ product.description }}

                </p>

                <!-- Quantity -->

                <div class="mt-8">

                    <label class="font-semibold">

                        Quantity

                    </label>

                    <div class="flex items-center mt-3">
                        <button @click="decrease" class="w-10 h-10 border rounded-l-lg">

                            -

                        </button>

                        <div class="w-16 h-10 border-t border-b flex items-center justify-center">

                            {{ quantity }}

                        </div>

                        <button @click="increase" class="w-10 h-10 border rounded-r-lg">

                            +

                        </button>




                    </div>

                </div>

                <!-- Buttons -->

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-10">

                    <button @click="addToCart" :disabled="product.quantity <= 0 || cartLoading"
                        class="w-full sm:w-auto px-8 py-3 rounded-xl text-white transition" :class="product.quantity > 0 && !cartLoading
                            ? 'bg-indigo-600 hover:bg-indigo-700'
                            : 'bg-gray-400 cursor-not-allowed'">

                        {{ cartLoading ? 'Adding...' : (product.quantity > 0 ? 'Add to Cart' : 'Out of Stock') }}

                    </button>
                   

                    <button @click="toggleWishlist" class="w-full sm:w-auto border px-8 py-3 rounded-xl hover:bg-gray-100">

                        <span v-if="isInWishlist(product.id)" class="text-rose-500">❤️</span>
                        <span v-else class="text-gray-400">🤍</span>
                        Wishlist

                    </button>

                </div>

            </div>

        </div>

        <!-- Reviews Section -->
        <section class="mt-20">
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    <ReviewSummary :reviews="reviews" />
                </div>
                <div class="lg:col-span-2">
                    <ReviewList :reviews="reviews" />
                </div>
            </div>
        </section>

        <!-- Related Products -->

        <section v-if="relatedProducts.length" class="mt-20">

            <h2 class="text-3xl font-bold mb-8">

                Related Products

            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                <ProductCard v-for="item in relatedProducts" :key="item.id" :product="item" />

            </div>

        </section>
    </div>
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
</template>

<script setup>

import { ref, onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import axiosClient from '../../axios'

import LoadingSpinner from '../../components/shop/LoadingSpinner.vue'
import ProductCard from '../../components/product/ProductCard.vue'
import ReviewSummary from '../../components/reviews/ReviewSummary.vue'
import ReviewList from '../../components/reviews/ReviewList.vue'

const route = useRoute()
const store = useStore()

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

const loading = ref(true)
const cartLoading = ref(false)

const error = ref('')

const product = ref(null)

const selectedImage = ref('')

const allImages = computed(() => {
    if (!product.value) return []
    const images = []
    if (product.value.featured_image) {
        images.push({
            url: `http://localhost:8000/storage/${product.value.featured_image}`
        })
    }
    if (product.value.images && product.value.images.length > 0) {
        product.value.images.forEach(img => {
            images.push({
                url: `http://localhost:8000/storage/${img.image}`
            })
        })
    }
    return images
})

const relatedProducts = ref([])

const quantity = ref(1)

const reviews = ref([])

const loadProduct = async () => {

    try {

        const { data } = await axiosClient.get(
            `/store/products/${route.params.id}`
        )

        product.value = data.product

        selectedImage.value = data.product.featured_image 
            ? `http://localhost:8000/storage/${data.product.featured_image}`
            : (data.product.images?.[0]?.image ? `http://localhost:8000/storage/${data.product.images[0].image}` : '')

        relatedProducts.value = data.related_products

        await loadReviews()

    } catch (error) {
        console.error('Failed to load product:', error)
        if (error.response?.status === 404) {
            error.value = 'The product you are looking for does not exist or is not available.'
        } else {
            error.value = 'Failed to load product. Please try again later.'
        }
    }

    finally {

        loading.value = false

    }

}

const loadReviews = async () => {
    try {
        const { data } = await axiosClient.get(`/products/${route.params.id}/reviews`)
        reviews.value = data
    } catch (error) {
        console.error('Failed to load reviews:', error)
    }
}



const imageUrl = (image) => {

    return `http://localhost:8000/storage/${image}`

}

const increase = () => {
    const maxQuantity = product.value?.quantity || 999
    if (quantity.value < maxQuantity) {
        quantity.value++
    }
}

const decrease = () => {
    if (quantity.value > 1) {
        quantity.value--
    }
}

const addToCart = async () => {
    if (cartLoading.value) return
    cartLoading.value = true
    try {
        await store.dispatch('addToCart', {
            productId: product.value.id,
            quantity: quantity.value
        })
        showToast('Product added to cart successfully.')
    } catch (error) {
        console.error(error)
        const errorMessage = error.message || error.response?.data?.message || 'Unable to add product to cart.'
        showToast(errorMessage, 'error')
    } finally {
        cartLoading.value = false
    }
}

const toggleWishlist = async () => {
    try {
        const response = await store.dispatch('toggleWishlist', product.value.id)
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

onMounted(loadProduct)

watch(() => route.params.id, () => {
    loadProduct()
})
</script>