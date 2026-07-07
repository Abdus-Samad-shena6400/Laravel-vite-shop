<template>

<section
    id="featured-products"
    class="max-w-7xl mx-auto py-16 px-4"
>

    <div class="text-center mb-12">

        <h2 class="text-3xl font-bold">

            Featured Products

        </h2>

        <p class="text-gray-500 mt-2">

            Handpicked products for you.

        </p>

    </div>

    <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8"
    >

        <ProductCard
            v-for="product in featuredProducts"
            :key="product.id"
            :product="product"
            :show-rating-details="true"
        />

    </div>

</section>

</template>

<script setup>

import {computed,onMounted} from 'vue'

import {useStore} from 'vuex'

import ProductCard from '../product/ProductCard.vue'

const store=useStore()

const featuredProducts=computed(()=>{

    return store.getters.allProducts.slice(0,8)

})

onMounted(()=>{

    if(!store.getters.allProducts.length){

        store.dispatch('fetchProducts')

    }

})

</script>