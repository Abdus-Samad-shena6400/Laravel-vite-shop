<template>

<section class="max-w-7xl mx-auto py-16 px-4">

    <div class="text-center mb-12">

        <h2 class="text-3xl font-bold">

            Shop by Brand

        </h2>

        <p class="text-gray-500 mt-2">

            Discover products from trusted brands.

        </p>

    </div>

    <div
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6"
    >

        <router-link
            v-for="brand in brands"
            :key="brand.id"
            :to="{
                name:'shop',
                query:{
                    brand_id: brand.id
                }
            }"
            class="bg-white rounded-xl border border-gray-100 shadow hover:shadow-lg transition p-6 text-center group"
        >

            <img
                :src="imageUrl(brand.image)"
                :alt="brand.name"
                class="w-20 h-20 mx-auto object-contain bg-gray-50 group-hover:scale-110 transition"
            >

            <h3
                class="mt-4 font-semibold text-gray-800"
            >

                {{ brand.name }}

            </h3>

        </router-link>

    </div>

</section>

</template>

<script setup>

import { computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

const brands = computed(() => store.getters.topBrands)

const imageUrl = (image) => {

    if (!image) {

        return 'https://placehold.co/200x200?text=Brand'

    }

    return `http://localhost:8000/storage/${image}`

}

</script>