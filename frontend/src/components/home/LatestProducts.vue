<template>

<section class="max-w-7xl mx-auto py-16 px-4">

    <div class="text-center mb-12">

        <h2 class="text-3xl font-bold">

            Latest Products

        </h2>

        <p class="text-gray-500 mt-2">

            Check out our newest arrivals.

        </p>

    </div>

    <div
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8"
    >

        <ProductCard
            v-for="product in latestProducts"
            :key="product.id"
            :product="product"
            :show-rating-details="true"
        />

    </div>

</section>

</template>

<script setup>

import { computed } from 'vue'
import { useStore } from 'vuex'
import ProductCard from '../product/ProductCard.vue'

const store = useStore()

const latestProducts = computed(() => {

    return [...store.getters.allProducts]
        .reverse()
        .slice(0, 8)

})

</script>