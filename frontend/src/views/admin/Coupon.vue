<template>
    <div class="p-4 sm:p-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    Coupons
                </h1>
                <p class="text-gray-500 mt-1">
                    Manage your product coupons.
                </p>
            </div>

            <router-link :to="{ name: 'coupon.create' }"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-lg font-medium text-center">
                + Add Coupon
            </router-link>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <input type="text" placeholder="Search coupon..." v-model="search"
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
                Loading Coupons...
            </p>

        </div>

        <div v-else class="bg-white rounded-xl shadow overflow-hidden">

            <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="text-left p-4">
                            Code
                        </th>

                        <th class="text-left p-4">
                            Name
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

                    <tr v-for="coupon in coupons" :key="coupon.id" class="border-t hover:bg-gray-50">

                        <td class="p-4 font-semibold">
                            {{ coupon.code }}
                        </td>

                        <td class="p-4 text-gray-500">
                            {{ coupon.name }}
                        </td>

                        <td class="p-4">

                            <span class="px-3 py-1 rounded-full text-sm" :class="coupon.status
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700'">
                                {{ coupon.status ? 'Active' : 'Inactive' }}
                            </span>

                        </td>

                        <td class="p-4">

                            <div class="flex flex-col sm:flex-row justify-center gap-2">

                                <router-link :to="{ name: 'coupon.edit', params: { id: coupon.id } }"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm text-center">
                                    Edit
                                </router-link>

                                <button @click="confirmDelete(coupon)" :disabled="deleting"
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
        <div v-if="!loading && coupons.length > 0" class="bg-white rounded-xl shadow p-4 mt-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-gray-600">
                    Showing {{ (currentPage - 1) * 4 + 1 }} to {{ Math.min(currentPage * 4, total) }} of {{ total }} coupons
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="currentPage--; getCoupons()"
                        :disabled="currentPage === 1"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button
                        @click="currentPage++; getCoupons()"
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
                    Delete Coupon
                </h2>
                <p class="text-gray-600">
                    Are you sure you want to delete
                    <span class="font-semibold">
                        {{ selectedCoupon.name }}
                    </span>
                    ?
                </p>
                <div class="flex justify-end gap-3 mt-8">
                    <button @click="showDeleteModal = false" class="px-5 py-2 rounded border">
                        Cancel
                    </button>
                    <button @click="deleteCoupon" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded">
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

const coupons = ref([])
const loading = ref(false)
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

const showDeleteModal = ref(false)
const selectedCoupon = ref(null)

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

const getCoupons = async () => {
    try {
        loading.value = true
        const { data } = await axiosClient.get('/coupons', {
            params: {
                search: search.value,
                page: currentPage.value
            }
        })

        coupons.value = data.data
        currentPage.value = data.current_page
        lastPage.value = data.last_page
        total.value = data.total
    } catch (error) {
        console.log(error)
    } finally {
        loading.value = false
    }
}


const confirmDelete = (coupon) => {
    selectedCoupon.value = coupon
    showDeleteModal.value = true
}

const deleteCoupon = async () => {
    deleting.value = true
    try {
        await axiosClient.delete(`/coupons/${selectedCoupon.value.id}`)
        showDeleteModal.value = false
        await getCoupons()
        showToast('Coupon deleted successfully.')
    } catch (error) {
        console.log(error)
        showToast('Failed to delete coupon.', 'error')
    } finally {
        deleting.value = false
    }
}

// Watch search input to trigger API call
watch(search, () => {
    currentPage.value = 1
    getCoupons()
})

onMounted(() => {
    getCoupons()
})
</script>