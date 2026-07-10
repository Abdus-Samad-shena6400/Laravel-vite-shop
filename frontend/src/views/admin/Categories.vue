<template>
    <div class="p-4 sm:p-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    Categories
                </h1>
                <p class="text-gray-500 mt-1">
                    Manage your product categories.
                </p>
            </div>

            <router-link :to="{ name: 'category.create' }"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-lg font-medium text-center">
                + Add Category
            </router-link>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <input type="text" placeholder="Search category..." v-model="search"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <!-- Table -->
        <div v-if="loading" class="bg-white rounded-xl shadow p-20 text-center">

            <svg class="animate-spin h-10 w-10 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">

                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />

            </svg>

            <p class="mt-4 text-gray-500">
                Loading Categories...
            </p>

        </div>

        <div v-else class="bg-white rounded-xl shadow overflow-hidden">

            <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="text-left p-4">
                            Image
                        </th>

                        <th class="text-left p-4">
                            Name
                        </th>

                        <th class="text-left p-4">
                            Slug
                        </th>

                        <th class="text-left p-4">
                            Status
                        </th>

                        <th class="text-center p-4">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr v-for="category in categories" :key="category.id" class="border-t hover:bg-gray-50">

                        <td class="p-4">
                            <img :src="category.image
                                ? `http://localhost:8000/storage/${category.image}`
                                : 'https://placehold.co/100x100?text=No+Image'" class="w-16 h-16 rounded-lg object-contain" />
                        </td>

                        <td class="p-4 font-semibold">
                            {{ category.name }}
                        </td>

                        <td class="p-4 text-gray-500">
                            {{ category.slug }}
                        </td>

                        <td class="p-4">

                            <span class="px-3 py-1 rounded-full text-sm" :class="category.status
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700'">
                                {{ category.status ? 'Active' : 'Inactive' }}
                            </span>

                        </td>

                        <td class="p-4">

                            <div class="flex flex-col sm:flex-row justify-center gap-2">

                                <router-link :to="{ name: 'category.edit', params: { id: category.id } }"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm text-center">
                                    Edit
                                </router-link>

                                <button @click="confirmDelete(category)" :disabled="deleting"
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
        <div v-if="!loading && categories.length > 0" class="bg-white rounded-xl shadow p-4 mt-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-gray-600">
                    Showing {{ (currentPage - 1) * 4 + 1 }} to {{ Math.min(currentPage * 4, total) }} of {{ total }} categories
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="currentPage--; getCategories()"
                        :disabled="currentPage === 1"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button
                        @click="currentPage++; getCategories()"
                        :disabled="currentPage === lastPage"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex justify-center items-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
                <h2 class="text-2xl font-bold mb-4">
                    Delete Category
                </h2>
                <p class="text-gray-600">
                    Are you sure you want to delete
                    <span class="font-semibold">
                        {{ selectedCategory.name }}
                    </span>
                    ?
                </p>
                <div class="flex justify-end gap-3 mt-8">
                    <button @click="showDeleteModal = false" class="px-5 py-2 rounded border">
                        Cancel
                    </button>
                    <button @click="deleteCategory" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded">
                        Delete
                    </button>
                </div>
            </div>
        </div>

    </div>
    <transition
    enter-active-class="transition duration-300"
    leave-active-class="transition duration-300"
    enter-from-class="opacity-0 translate-y-4"
    leave-to-class="opacity-0 translate-y-4"
>
    <div
        v-if="toast.show"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50"
    >
        <div
            class="px-6 py-4 rounded-lg shadow-xl text-white"
            :class="toast.type === 'success'
                ? 'bg-green-600'
                : 'bg-red-600'"
        >
            {{ toast.message }}
        </div>
    </div>
</transition>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axiosClient from '../../axios'

const categories = ref([])
const loading = ref(false)
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

const showDeleteModal = ref(false)
const selectedCategory = ref(null)

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

const getCategories = async () => {
    try {
        loading.value = true
        const { data } = await axiosClient.get('/categories', {
            params: {
                search: search.value,
                page: currentPage.value
            }
        })

        categories.value = data.data
        currentPage.value = data.current_page
        lastPage.value = data.last_page
        total.value = data.total
    } catch (error) {
        console.log(error)
    } finally {
        loading.value = false
    }
}


const confirmDelete = (category) => {
    selectedCategory.value = category
    showDeleteModal.value = true
}

const deleteCategory = async () => {
    deleting.value = true
    try {
        await axiosClient.delete(`/categories/${selectedCategory.value.id}`)
        showDeleteModal.value = false
        await getCategories()
        showToast('Category deleted successfully.')
    } catch (error) {
        console.log(error)
        showToast('Failed to delete category.', 'error')
    } finally {
        deleting.value = false
    }
}

// Watch search input to trigger API call
watch(search, () => {
    currentPage.value = 1
    getCategories()
})

onMounted(() => {
    getCategories()
})
</script>