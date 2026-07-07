<template>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        <div class="text-center mb-10">

            <h2 class="text-3xl font-extrabold text-gray-900">

                Shop by Category

            </h2>

            <p class="mt-2 text-gray-500">

                Explore our most popular product categories.

            </p>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            <router-link
                v-for="category in categories"
                :key="category.id"
                :to="{
                    name:'shop',
                    query:{ category_id: category.id }
                }"
                class="group relative h-64 rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition block"
            >

                <img
                    :src="imageUrl(category.image)"
                    :alt="category.name"
                    class="w-full h-full object-contain border border-gray-200 group-hover:scale-105 transition duration-500"
                >

                <div
                    class="absolute inset-0 bg-black/35 group-hover:bg-black/45 transition"
                ></div>

                <div
                    class="absolute bottom-0 left-0 right-0 p-6"
                >

                    <h3 class="text-white text-xl font-bold">

                        {{ category.name }}

                    </h3>

                    <p class="text-indigo-200 text-sm mt-1">

                        Explore Products →

                    </p>

                </div>

            </router-link>

        </div>

    </section>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

const categories = computed(() => store.getters.allCategories)

const imageUrl = (image) => {

    if (!image) {
        return 'https://placehold.co/600x600?text=Category'
    }

    return `http://localhost:8000/storage/${image}`

}

onMounted(() => {

    if (!store.getters.allCategories.length) {

        store.dispatch('fetchCategories')

    }

})
</script>