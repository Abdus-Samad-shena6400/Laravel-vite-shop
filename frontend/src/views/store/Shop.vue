<template>
<PublicHeader />
<div class="max-w-7xl mx-auto py-10 px-4">
    
    <div class="mb-10">
  
        <h1 class="text-4xl font-bold">

            Shop

        </h1>

        <p class="text-gray-500 mt-2">

            Browse all available products.

        </p>

    </div>

    <div class="grid lg:grid-cols-4 gap-8">

        <!-- Sidebar -->

        <div>

            <ProductFilters

                :categories="categories"

                :brands="brands"

                @apply="applyFilters"

            />

        </div>

        <!-- Products -->

        <div class="lg:col-span-3">

            <LoadingSpinner

                v-if="loading"

            />

            <EmptyState

                v-else-if="products.data.length===0"

                title="No Products"

                message="Try changing your filters."

            />

            <ProductGrid

                v-else

                :products="products"

            />
            <Pagination
                v-if="products.links"
                :links="products.links"
                @change="changePage"
            />

        </div>

    </div>

</div>

</template>


<script setup>

import {ref,onMounted} from 'vue'

import axiosClient from '../../axios'

import ProductFilters from '../../components/shop/ProductFilters.vue'

import Pagination from '../../components/shop/Pagination.vue'

import ProductGrid from '../../components/shop/ProductGrid.vue'

import LoadingSpinner from '../../components/shop/LoadingSpinner.vue'

import EmptyState from '../../components/shop/EmptyState.vue'

import PublicHeader from '../../components/admin/PublicHeader.vue'

const loading=ref(true)

const products=ref({

    data:[]

})

const categories=ref([])

const brands=ref([])

const filters=ref({})

const loadProducts=async()=>{

    loading.value=true

    try{

        const {data}=await axiosClient.get('/store/products',{

            params:filters.value

        })

        products.value=data

    }

    finally{

        loading.value=false

    }

}

const loadCategories=async()=>{

    const {data}=await axiosClient.get('/store/categories')

    categories.value=data

}

const loadBrands=async()=>{

    const {data}=await axiosClient.get('/store/brands')

    brands.value=data

}

const applyFilters=(newFilters)=>{

    filters.value={...newFilters}

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

onMounted(async()=>{

    await Promise.all([

        loadProducts(),

        loadCategories(),

        loadBrands()

    ])

})

</script>