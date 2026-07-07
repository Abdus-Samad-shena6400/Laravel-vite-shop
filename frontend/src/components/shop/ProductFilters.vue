<template>

    <div class="bg-white rounded-2xl shadow p-6">

        <h2 class="text-xl font-bold mb-6">

            Filters

        </h2>

        <!-- Search -->

        <div class="mb-6">

            <label class="block text-sm font-semibold mb-2">

                Search

            </label>

            <input
                v-model="filters.search"
                type="text"
                placeholder="Search products..."
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none"
            >

        </div>

        <!-- Category -->

        <div class="mb-6">

            <label class="block text-sm font-semibold mb-2">

                Category

            </label>

            <select
                v-model="filters.category_id"
                class="w-full border rounded-lg px-4 py-2"
            >

                <option value="">

                    All Categories

                </option>

                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                >

                    {{ category.name }}

                </option>

            </select>

        </div>

        <!-- Brand -->

        <div class="mb-6">

            <label class="block text-sm font-semibold mb-2">

                Brand

            </label>

            <select
                v-model="filters.brand_id"
                class="w-full border rounded-lg px-4 py-2"
            >

                <option value="">

                    All Brands

                </option>

                <option
                    v-for="brand in brands"
                    :key="brand.id"
                    :value="brand.id"
                >

                    {{ brand.name }}

                </option>

            </select>

        </div>

        <!-- Sort -->

        <div class="mb-8">

            <label class="block text-sm font-semibold mb-2">

                Sort By

            </label>

            <select
                v-model="filters.sort"
                class="w-full border rounded-lg px-4 py-2"
            >

                <option value="latest">

                    Latest

                </option>

                <option value="oldest">

                    Oldest

                </option>

                <option value="price_low">

                    Price: Low to High

                </option>

                <option value="price_high">

                    Price: High to Low

                </option>

            </select>

        </div>

        <!-- Buttons -->

        <div class="space-y-3">

            <button
                @click="$emit('apply', filters)"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg"
            >

                Apply Filters

            </button>

            <button
                @click="clearFilters"
                class="w-full border py-2 rounded-lg hover:bg-gray-100"
            >

                Clear Filters

            </button>

        </div>

    </div>

</template>

<script setup>

import { reactive } from 'vue'

defineProps({

    categories: Array,

    brands: Array

})

const emit = defineEmits([

    'apply'

])

const filters = reactive({

    search: '',

    category_id: '',

    brand_id: '',

    sort: 'latest'

})

const clearFilters = () => {

    filters.search = ''

    filters.category_id = ''

    filters.brand_id = ''

    filters.sort = 'latest'

    emit('apply', filters)

}

</script>