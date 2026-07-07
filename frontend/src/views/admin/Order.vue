<template>
    <div class="p-6">

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold">

                Orders

            </h1>

            <div class="flex gap-3">

                <input v-model="search" @input="getOrders" placeholder="Search..." class="border rounded-lg px-4 py-2">

                <select v-model="status" @change="getOrders" class="border rounded-lg px-4 py-2">

                    <option value="">

                        All Status

                    </option>

                    <option value="pending">

                        Pending

                    </option>

                    <option value="confirmed">

                        Confirmed

                    </option>

                    <option value="processing">

                        Processing

                    </option>

                    <option value="shipped">

                        Shipped

                    </option>

                    <option value="delivered">

                        Delivered

                    </option>

                    <option value="cancelled">

                        Cancelled

                    </option>

                </select>

            </div>

        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="p-4 text-left">Order #</th>

                        <th class="p-4 text-left">Customer</th>

                        <th class="p-4 text-left">Total</th>

                        <th class="p-4 text-left">Payment</th>

                        <th class="p-4 text-left">Status</th>

                        <th class="p-4 text-left">Date</th>

                        <th class="p-4 text-center">Action</th>

                    </tr>

                </thead>

                <tbody>

                    <tr v-for="order in orders" :key="order.id" class="border-t hover:bg-gray-50">

                        <td class="p-4">
                            {{ order.order_number }}
                        </td>

                        <td class="p-4">
                            {{ order.user?.name }}
                        </td>

                        <td class="p-4">
                            ${{ order.grand_total }}
                        </td>

                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-sm" :class="paymentClass(order.payment_status)">

                                {{ order.payment_status }}

                            </span>
                        </td>

                        <td class="p-4">


                            <span class="px-3 py-1 rounded-full text-sm" :class="statusClass(order.order_status)">

                                {{ order.order_status }}

                            </span>


                        </td>

                        <td class="p-4">
                            {{ order.created_at }}
                        </td>

                        <td class="p-4 text-center">

                            <div class="flex gap-2 justify-center">
                                <router-link :to="`/orders/${order.id}`" class="bg-indigo-600 text-white px-4 py-2 rounded">
                                    View
                                </router-link>
                                <button @click="confirmDelete(order)" :disabled="deleting"
                                    class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-4 py-2 rounded">
                                    {{ deleting ? 'Deleting...' : 'Delete' }}
                                </button>
                            </div>

                        </td>

                    </tr>

                    <tr v-if="orders.length === 0">

                        <td colspan="7" class="text-center py-8">

                            No Orders Found

                        </td>

                    </tr>

                </tbody>

            </table>


        </div>
        <div class="flex justify-end mt-6 gap-2">

            <button @click="getOrders(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
                class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">

                Previous

            </button>

            <button @click="getOrders(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">

                Next

            </button>

        </div>
        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex justify-center items-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-[420px] p-6">
                <h2 class="text-2xl font-bold mb-4">
                    Delete Order
                </h2>
                <p class="text-gray-600">
                    Are you sure you want to delete
                    <span class="font-semibold">
                        {{ selectedOrder.order_number }}
                    </span>
                    ?
                </p>
                <div class="flex justify-end gap-3 mt-8">
                    <button @click="showDeleteModal = false" class="px-5 py-2 rounded border">
                        Cancel
                    </button>
                    <button @click="deleteOrder" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded">
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
            class="fixed top-5 right-5 z-50"
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

import { ref, onMounted } from 'vue'
import axiosClient from '../../axios'

const orders = ref([])

const pagination = ref({})

const search = ref('')

const status = ref('')

const showDeleteModal = ref(false)
const selectedOrder = ref(null)

const toast = ref({
    show: false,
    message: '',
    type: 'success'
})

const deleting = ref(false)

const getOrders = async (page = 1) => {

    const { data } = await axiosClient.get('/orders', {

        params: {

            page,

            search: search.value,

            status: status.value

        }

    })

    orders.value = data.data

    pagination.value = data

}

onMounted(getOrders)

const statusClass = (status) => {

    switch (status) {

        case 'pending':

            return 'bg-yellow-100 text-yellow-700'

        case 'confirmed':

            return 'bg-purple-100 text-purple-700'

        case 'processing':

            return 'bg-blue-100 text-blue-700'

        case 'shipped':

            return 'bg-indigo-100 text-indigo-700'

        case 'delivered':

            return 'bg-green-100 text-green-700'

        case 'cancelled':

            return 'bg-red-100 text-red-700'

        default:

            return 'bg-gray-100'

    }

}

const paymentClass = (status) => {

    switch (status) {

        case 'paid':

            return 'bg-green-100 text-green-700'

        case 'failed':

            return 'bg-red-100 text-red-700'

        default:

            return 'bg-yellow-100 text-yellow-700'

    }

}

const showToast = (message, type = 'success') => {
    toast.value.message = message
    toast.value.type = type
    toast.value.show = true

    setTimeout(() => {
        toast.value.show = false
    }, 3000)
}

const confirmDelete = (order) => {
    selectedOrder.value = order
    showDeleteModal.value = true
}

const deleteOrder = async () => {
    deleting.value = true
    try {
        await axiosClient.delete(`/orders/${selectedOrder.value.id}`)
        showDeleteModal.value = false
        await getOrders(pagination.value.current_page)
        showToast('Order deleted successfully.')
    } catch (error) {
        console.error(error)
        showToast('Failed to delete order.', 'error')
    } finally {
        deleting.value = false
    }
}

</script>