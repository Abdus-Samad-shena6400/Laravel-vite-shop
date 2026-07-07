<template>

<div
    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"
>

    <ProductCard
        v-for="product in products.data"
        :key="product.id"
        :product="product"
    />

</div>

</template>

<script setup>

import ProductCard from '../product/ProductCard.vue'

defineProps({

    products:{
        type:Object,
        required:true
    }

})

</script>