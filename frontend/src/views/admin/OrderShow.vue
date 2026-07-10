<template>
    <div class="p-6">

        <!-- Loading -->
        <div v-if="loading" class="bg-white rounded-xl shadow p-20 text-center">
            <svg class="animate-spin h-10 w-10 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
            </svg>

            <p class="mt-4 text-gray-500">
                Loading Order...
            </p>
        </div>

        <div v-else-if="order">

            <!-- Header -->

            <div class="flex justify-between items-center mb-6">

                <div>

                    <h1 class="text-3xl font-bold text-gray-800">

                        Order Details

                    </h1>

                    <p class="text-gray-500 mt-1">

                        {{ order.order_number }}

                    </p>

                </div>

                <router-link :to="{ name: 'orders' }"
                    class="bg-blue-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg">
                    Back
                </router-link>

            </div>

            <!-- Customer + Payment -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                <!-- Customer -->

                <div class="bg-white rounded-xl shadow p-6">

                    <h2 class="text-xl font-semibold mb-4">

                        Customer Information

                    </h2>

                    <div class="space-y-3">

                        <p>

                            <strong>Name:</strong>

                            {{ order.user?.name }}

                        </p>

                        <p>

                            <strong>Email:</strong>

                            {{ order.user?.email }}

                        </p>

                    </div>

                </div>

                <!-- Payment -->

                <div class="bg-white rounded-xl shadow p-6">

                    <h2 class="text-xl font-semibold mb-4">

                        Payment Information

                    </h2>

                    <div class="space-y-3">

                        <p>

                            <strong>Payment Method:</strong>

                            {{ order.payment_method }}

                        </p>

                        <p>

                            <strong>Payment Status:</strong>

                            <span class="px-3 py-1 rounded-full text-sm"
                                :class="paymentStatusClass(order.payment_status)">
                                {{ order.payment_status }}
                            </span>

                        </p>
                        <button v-if="
                            order.payment_method === 'bank_transfer' &&
                            order.payment_status === 'pending'
                        " @click="markAsPaid" class="mt-3 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                            Mark as Paid
                        </button>

                        <div v-if="order.transaction_id">
                            <strong>Transaction ID:</strong>
                            {{ order.transaction_id }}
                        </div>

                        <p>

                            <strong>Order Status:</strong>

                            <span class="px-3 py-1 rounded-full text-sm" :class="orderStatusClass(order.order_status)">
                                {{ order.order_status }}
                            </span>

                        </p>

                    </div>

                </div>

            </div>

            <!-- Products -->

            <div class="bg-white rounded-xl shadow overflow-hidden">

                <div class="p-6 border-b">

                    <h2 class="text-xl font-semibold">

                        Ordered Products

                    </h2>

                </div>

                <table class="w-full">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="p-4 text-left">Image</th>

                            <th class="p-4 text-left">Product</th>

                            <th class="p-4 text-left">Price</th>

                            <th class="p-4 text-left">Quantity</th>

                            <th class="p-4 text-left">Total</th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr v-for="item in order.items" :key="item.id" class="border-t hover:bg-gray-50">

                            <td class="p-4">

                                <img :src="item.product.featured_image
                                    ? `http://localhost:8000/storage/${item.product.featured_image}`
                                    : 'https://via.placeholder.com/80'" class="w-16 h-16 object-contain  rounded-lg" />

                            </td>

                            <td class="p-4">

                                {{ item.product.name }}

                            </td>

                            <td class="p-4">

                                ${{ Number(item.price).toFixed(2) }}

                            </td>

                            <td class="p-4">

                                {{ item.quantity }}

                            </td>

                            <td class="p-4 font-semibold">

                                ${{ Number(item.total).toFixed(2) }}

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            <!-- Totals -->

            <div class="mt-6 flex justify-end">

                <div class="bg-white rounded-xl shadow p-6 w-96">

                    <h2 class="text-xl font-semibold mb-5">

                        Order Summary

                    </h2>

                    <div class="space-y-3">

                        <div class="flex justify-between">

                            <span>Subtotal</span>

                            <span>${{ Number(order.subtotal).toFixed(2) }}</span>

                        </div>
                        <div class="mt-6 bg-white rounded-xl shadow p-6">

                            <div class="mt-6 bg-white rounded-xl shadow p-6">

                                <h2 class="text-xl font-semibold mb-5">

                                    Order Management

                                </h2>

                                <!-- Order Status -->

                                <div class="mb-6">

                                    <label class="block mb-2 font-medium">

                                        Order Status

                                    </label>

                                    <div class="flex gap-4 items-center">

                                        <select v-model="selectedStatus" class="border rounded-lg px-4 py-2 w-72">

                                            <option value="pending">Pending</option>

                                            <option value="confirmed">Confirmed</option>

                                            <option value="processing">Processing</option>

                                            <option value="shipped">Shipped</option>

                                            <option value="delivered">Delivered</option>

                                            <option value="cancelled">Cancelled</option>

                                        </select>

                                        <button @click="updateStatus" :disabled="updating"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg disabled:bg-gray-400">

                                            {{ updating ? 'Updating...' : 'Update Status' }}

                                        </button>

                                    </div>

                                </div>

                                <!-- Payment Status -->

                                <div>

                                    <label class="block mb-2 font-medium">

                                        Payment Status

                                    </label>

                                    <div class="flex gap-4 items-center">

                                        <select v-model="selectedPaymentStatus"
                                            class="border rounded-lg px-4 py-2 w-72">

                                            <option value="pending">

                                                Pending

                                            </option>

                                            <option value="paid">

                                                Paid

                                            </option>

                                            <option value="failed">

                                                Failed

                                            </option>

                                        </select>

                                        <button @click="updatePaymentStatus" :disabled="paymentUpdating"
                                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg disabled:bg-gray-400">

                                            {{ paymentUpdating ? 'Updating...' : 'Update Payment' }}

                                        </button>

                                    </div>

                                </div>

                            </div>



                        </div>

                        <div class="flex justify-between">

                            <span>Shipping</span>

                            <span>${{ Number(order.shipping).toFixed(2) }}</span>

                        </div>

                        <div class="flex justify-between">

                            <span>Tax</span>

                            <span>${{ Number(order.tax).toFixed(2) }}</span>

                        </div>

                        <div class="flex justify-between">

                            <span>Discount</span>

                            <span>${{ Number(order.discount).toFixed(2) }}</span>

                        </div>

                        <hr>

                        <div class="flex justify-between text-xl font-bold">

                            <span>Grand Total</span>

                            <span>${{ Number(order.grand_total).toFixed(2) }}</span>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        <transition enter-active-class="transition duration-300" leave-active-class="transition duration-300"
            enter-from-class="opacity-0 translate-y-4" leave-to-class="opacity-0 translate-y-4">
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
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axiosClient from '../../axios'

const route = useRoute()

const selectedStatus = ref('')
const selectedPaymentStatus = ref('')
const paymentUpdating = ref(false)

const loading = ref(true)

const order = ref(null)

const updating = ref(false)

const updateStatus = async () => {

    updating.value = true

    try {

        await axiosClient.put(
            `/orders/${order.value.id}/status`,
            {
                order_status: selectedStatus.value
            }
        )

        order.value.order_status = selectedStatus.value

        showToast('Order status updated successfully.')

    }

    catch (error) {

        console.log(error)

        showToast('Failed to update status.', 'error')

    }

    finally {

        updating.value = false

    }

}

const toast = ref({
    show: false,
    message: '',
    type: 'success'
})

const showToast = (message, type = 'success') => {

    toast.value.message = message

    toast.value.type = type

    toast.value.show = true

    setTimeout(() => {

        toast.value.show = false

    }, 3000)

}

const loadOrder = async () => {

    loading.value = true

    try {

        const { data } = await axiosClient.get(`/orders/${route.params.id}`)

        order.value = data

        selectedStatus.value = data.order_status

        selectedPaymentStatus.value = data.payment_status

    } catch (error) {

        console.log(error)

    } finally {

        loading.value = false

    }

}




const updatePaymentStatus = async () => {

    paymentUpdating.value = true

    try {

        await axiosClient.patch(

            `/orders/${order.value.id}/payment-status`,

            {
                

                payment_status: selectedPaymentStatus.value

            }

        )

        order.value.payment_status = selectedPaymentStatus.value

        showToast('Payment status updated successfully.')

    } catch (error) {

        console.error(error)

        showToast('Failed to update payment status.', 'error')

    } finally {

        paymentUpdating.value = false

    }

}

const paymentStatusClass = (status) => {

    switch (status) {

        case 'paid':
            return 'bg-green-100 text-green-700'

        case 'failed':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-yellow-100 text-yellow-700'
    }
}

const orderStatusClass = (status) => {

    switch (status) {

        case 'delivered':
            return 'bg-green-100 text-green-700'

        case 'processing':
            return 'bg-blue-100 text-blue-700'

        case 'shipped':
            return 'bg-indigo-100 text-indigo-700'

        case 'cancelled':
            return 'bg-red-100 text-red-700'

        case 'confirmed':
            return 'bg-purple-100 text-purple-700'

        default:
            return 'bg-yellow-100 text-yellow-700'
    }
}

const markAsPaid = async () => {
    paymentUpdating.value = true
    try {
        await axiosClient.patch(
            `/orders/${order.value.id}/payment-status`,
            {
                payment_status: 'paid'
            }
        )
        order.value.payment_status = 'paid'
        showToast('Payment status updated successfully.')
    } catch (error) {
        console.error(error)
        showToast('Failed to update payment status.', 'error')
    } finally {
        paymentUpdating.value = false
    }
}

onMounted(() => {

    loadOrder()

})
</script>

<style scoped></style>