<template>
    <PublicHeader />
    <div class="container-standard section-padding">
        <HotDealBanner />

        <div class="mb-6 sm:mb-8 lg:mb-10">
            <h1 class="text-heading">
                Shop
            </h1>
            <p class="text-body mt-2">
                Browse all available products.
            </p>
        </div>

        <div class="grid gap-6 lg:gap-8 lg:grid-cols-[280px_minmax(0,1fr)]">
            <!-- Sidebar -->
            <div class="lg:sticky lg:top-24 lg:h-fit order-2 lg:order-1">
                <ProductFilters :categories="categories" :brands="brands" @apply="applyFilters" />
            </div>

            <!-- Products -->
            <div class="min-w-0 order-1 lg:order-2">
                <LoadingSpinner v-if="loading" />
                <EmptyState v-else-if="products.data.length === 0" title="No Products"
                    message="Try changing your filters." />
                <ProductGrid v-else :products="products" />
                <Pagination v-if="products.links" :links="products.links" @change="changePage" />
            </div>
        </div>
    </div>
</template>


<script setup>

import { ref, onMounted, onUnmounted } from 'vue'

import axiosClient from '../../axios'

import ProductFilters from '../../components/shop/ProductFilters.vue'

import Pagination from '../../components/shop/Pagination.vue'

import ProductGrid from '../../components/shop/ProductGrid.vue'

import LoadingSpinner from '../../components/shop/LoadingSpinner.vue'

import EmptyState from '../../components/shop/EmptyState.vue'

import PublicHeader from '../../components/admin/PublicHeader.vue'

import HotDealBanner from '../../components/home/HotDealBanner.vue'

const loading = ref(true)

const products = ref({

    data: []

})

const categories = ref([])

const brands = ref([])

const filters = ref({})

const loadProducts = async () => {

    loading.value = true

    try {

        const { data } = await axiosClient.get('/store/products', {

            params: filters.value

        })

        products.value = data

    }

    finally {

        loading.value = false

    }

}


const loadCategories = async () => {

    const { data } = await axiosClient.get('/store/categories')

    categories.value = data

}

const loadBrands = async () => {

    const { data } = await axiosClient.get('/store/brands')

    brands.value = data

}

const applyFilters = (newFilters) => {

    filters.value = { ...newFilters }

    loadProducts()

}

const changePage = async (url) => {

    loading.value = true

    try {

        const page = new URL(url).searchParams.get('page')

        const { data } = await axiosClient.get('/store/products', {

            params: {

                ...filters.value,

                page

            }

        })

        products.value = data

    }

    finally {

        loading.value = false

    }

}

onMounted(async () => {

    await Promise.all([

        loadProducts(),

        loadCategories(),

        loadBrands()

    ])

})




</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity .8s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>