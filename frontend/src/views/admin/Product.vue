<template>
    <div class="p-4 sm:p-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">

            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    Products
                </h1>

                <p class="text-gray-500 mt-1">
                    Manage all products in your store.
                </p>
            </div>

            <router-link :to="{ name: 'product.create' }"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-lg font-medium transition text-center">
                + Add Product
            </router-link>

        </div>

        <!-- Search -->

        <div class="bg-white rounded-xl shadow p-5 mb-6">

            <input type="text" placeholder="Search product..." v-model="search"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">

        </div>
        <div v-if="loading" class="bg-white rounded-xl shadow p-20 text-center">

            <svg class="animate-spin h-10 w-10 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />

            </svg>

            <p class="mt-4 text-gray-500">
                Loading Products...
            </p>

        </div>

        <!-- Table -->

        <div v-else class="bg-white rounded-xl shadow overflow-hidden">

            <div class="overflow-x-auto">
            <table class="w-full min-w-240">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="p-4 text-left">Image</th>

                        <th class="p-4 text-left">Product</th>

                        <th class="p-4 text-left">Category</th>
                        <th class="p-4 text-left">Brand</th>

                        <th class="p-4 text-left">Price</th>

                        <th class="p-4 text-left">Stock</th>

                        <th class="p-4 text-left">Status</th>

                        <th class="p-4 text-center">Action</th>

                    </tr>

                </thead>

                <tbody>

                    <!-- No Products Found -->
                    <tr v-if="products.length === 0">

                        <td colspan="8" class="text-center py-10 text-gray-500">
                            No Products Found
                        </td>

                    </tr>

                    <!-- Product List -->
                    <tr v-for="product in products" :key="product.id" class="border-t hover:bg-gray-50">

                        <td class="p-4">

                            <img :src="product.featured_image ? 'http://localhost:8000/storage/' + product.featured_image : '/placeholder.png'" class="w-16 h-16 rounded-lg object-cover">

                        </td>

                        <td class="p-4 font-medium">

                            {{ product.name }}

                        </td>

                        <td class="p-4">

                            {{ product.category?.name || 'N/A' }}

                        </td>
                        <td class="p-4">

                            {{ product.brand?.name || 'N/A' }}

                        </td>

                        <td class="p-4">

                            ${{ product.regular_price }}

                        </td>

                        <td class="p-4">

                            {{ product.quantity }}

                        </td>

                        <td class="p-4">

                            <span :class="product.status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-3 py-1 rounded-full text-sm">
                                {{ product.status ? 'Active' : 'Inactive' }}
                            </span>

                        </td>

                        <td class="p-4">

                            <div class="flex flex-col sm:flex-row justify-center gap-2">

                                <router-link :to="`/products/${product.id}/edit`"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm text-center">
                                    Edit
                                </router-link>

                                <button @click="confirmDelete(product)" :disabled="deleting"
                                    class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-5 py-2 rounded text-sm">

                                    {{ deleting ? 'Deleting...' : 'Delete' }}

                                </button>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>
            </div>

        </div>

        <!-- Pagination -->
        <div v-if="!loading && products.length > 0" class="bg-white rounded-xl shadow p-4 mt-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-gray-600">
                    Showing {{ (currentPage - 1) * 10 + 1 }} to {{ Math.min(currentPage * 10, total) }} of {{ total }} products
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="currentPage--; fetchProducts()"
                        :disabled="currentPage === 1"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button
                        @click="currentPage++; fetchProducts()"
                        :disabled="currentPage === lastPage"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
        <!-- Delete Modal -->

        <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex justify-center items-center z-50">

            <div class="bg-white rounded-xl shadow-xl w-full max-w-105 mx-4 p-6">

                <h2 class="text-2xl font-bold mb-4">
                    Delete Product
                </h2>

                <p class="text-gray-600">

                    Are you sure you want to delete

                    <span class="font-semibold">

                        {{ selectedProduct.name }}

                    </span>

                    ?

                </p>

                <div class="flex justify-end gap-3 mt-8">

                    <button @click="showDeleteModal = false" class="px-5 py-2 rounded border">
                        Cancel
                    </button>

                    <button @click="deleteProduct" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded">
                        Delete
                    </button>

                </div>

            </div>

        </div>

        <transition enter-active-class="transition duration-300" leave-active-class="transition duration-300"
            enter-from-class="opacity-0 translate-y-3" leave-to-class="opacity-0 translate-y-3">

            <div v-if="toast.show" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50">

                <div class="px-6 py-4 rounded-lg shadow-xl text-white" :class="toast.type === 'success'
                    ? 'bg-green-600'
                    : 'bg-red-600'">

                    {{ toast.message }}

                </div>

            </div>

        </transition>

    </div>
</template>

<script setup>

import { ref, onMounted, watch } from 'vue'
import axiosClient from '../../axios'

const products = ref([])
const loading = ref(false)
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

const showDeleteModal = ref(false)
const selectedProduct = ref(null)

const toast = ref({
    show: false,
    message: '',
    type: 'success'
})

const deleting = ref(false)

const showToast = (message, type = 'success') => {

    toast.value.message = message
    toast.value.type = type
    toast.value.show = true

    setTimeout(() => {

        toast.value.show = false

    }, 3000)

}

const fetchProducts = async () => {

    loading.value = true

    try {

        const { data } = await axiosClient.get('/products', {
            params: {
                search: search.value,
                page: currentPage.value
            }
        })

        products.value = data.data.filter(p => p !== null)
        currentPage.value = data.current_page
        lastPage.value = data.last_page
        total.value = data.total

    }

    catch (error) {

        console.log(error)

    }

    finally {

        loading.value = false

    }

}

const confirmDelete = (product) => {
    selectedProduct.value = product
    showDeleteModal.value = true
}

const deleteProduct = async () => {

    deleting.value = true

    try {

        await axiosClient.delete(`/products/${selectedProduct.value.id}`)

        showDeleteModal.value = false

        await fetchProducts()

        showToast('Product deleted successfully.')

    } catch (error) {

        console.log(error)

        showToast('Failed to delete product.', 'error')

    } finally {

        deleting.value = false

    }

}

// Watch search input to trigger API call
watch(search, () => {
    currentPage.value = 1
    fetchProducts()
})

onMounted(() => {

    fetchProducts()

})

</script>