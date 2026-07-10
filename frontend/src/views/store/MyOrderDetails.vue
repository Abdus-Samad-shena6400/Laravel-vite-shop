<template>
    <div class="bg-gray-100 min-h-screen">

        <PublicHeader />

        <div class="max-w-6xl mx-auto py-10 px-4">

            <div v-if="loading" class="text-center py-20">

                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600">
                </div>

                <p class="mt-4 text-gray-600">
                    Loading order...
                </p>

            </div>

            <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden">

                <!-- Header -->

                <div class="flex justify-between items-center border-b p-6">

                    <div>

                        <h1 class="text-3xl font-bold">

                            Order #{{ order.order_number }}

                        </h1>

                        <p class="text-gray-500 mt-2">

                            {{ formatDate(order.created_at) }}

                        </p>

                    </div>

                    <div class="flex flex-col items-end gap-4">

                        <span class="px-4 py-2 rounded-full text-sm font-semibold"
                            :class="statusClass(order.order_status)">
                            {{ order.order_status }}
                        </span>

                        <span class="px-4 py-2 rounded-full text-sm font-semibold"
                            :class="paymentClass(order.payment_status)">
                            {{ order.payment_status }}
                        </span>

                    </div>

                </div>

                <!-- Customer -->

                <div class="grid md:grid-cols-2 gap-8 p-6 border-b">

                    <div>

                        <h2 class="font-bold text-lg mb-3">

                            Customer

                        </h2>

                        <p>{{ order.user?.name }}</p>

                        <p>{{ order.user?.email }}</p>

                    </div>

                    <div>

                        <h2 class="font-bold text-lg mb-3">

                            Shipping Address

                        </h2>

                        <template v-if="order.detail">

                            <p>

                                {{ order.detail.first_name }}
                                {{ order.detail.last_name }}

                            </p>

                            <p>{{ order.detail.address1 }}</p>

                            <p v-if="order.detail.address2">

                                {{ order.detail.address2 }}

                            </p>

                            <p>

                                {{ order.detail.city }},
                                {{ order.detail.state }}

                            </p>

                            <p>

                                {{ order.detail.country_code }}

                            </p>

                        </template>

                    </div>

                </div>

                <!-- Products -->

                <div class="p-6">

                    <h2 class="text-xl font-bold mb-5">

                        Ordered Products

                    </h2>

                    <table class="w-full">

                        <thead class="bg-gray-100">

                            <tr>

                                <th class="text-left p-3">
                                    Product
                                </th>

                                <th class="text-center">
                                    Qty
                                </th>

                                <th class="text-right">
                                    Price
                                </th>

                                <th class="text-right">
                                    Total
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr v-for="item in order.items" :key="item.id" class="border-b">

                                <td class="py-4">

                                    {{ item.product?.name }}

                                </td>

                                <td class="text-center">

                                    {{ item.quantity }}

                                </td>

                                <td class="text-right">

                                    ${{ Number(item.price).toFixed(2) }}

                                </td>

                                <td class="text-right font-semibold">

                                    ${{ Number(item.price * item.quantity).toFixed(2) }}

                                </td>

                                <td class="text-center py-4">
                                    <ReviewForm
                                        v-if="order.order_status === 'delivered' && !item.reviewed"
                                        :product-id="item.product_id"
                                        @submitted="loadOrder"
                                    />

                                    <span v-else-if="item.reviewed" class="text-green-600 font-semibold">
                                        ✔ Reviewed
                                    </span>

                                    <span v-else class="text-gray-500">
                                        You can review this product after delivery.
                                    </span>
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

                <!-- Summary -->

                <div class="border-t p-6">

                    <div class="max-w-sm ml-auto space-y-3">

                        <div class="flex justify-between">

                            <span>Subtotal</span>

                            <span>${{ Number(order.subtotal).toFixed(2) }}</span>

                        </div>

                        <div class="flex justify-between">

                            <span>Shipping</span>

                            <span>${{ Number(order.shipping).toFixed(2) }}</span>

                        </div>

                        <div class="flex justify-between">

                            <span>Tax</span>

                            <span>${{ Number(order.tax).toFixed(2) }}</span>

                        </div>

                        <div class="flex justify-between text-xl font-bold border-t pt-3">

                            <span>Total</span>

                            <span>

                                ${{ Number(order.grand_total).toFixed(2) }}

                            </span>

                        </div>

                    </div>

                </div>

                <!-- Cancel Button -->
                <div v-if="['pending', 'processing'].includes(order.order_status)"
                    class="border-t p-6 flex justify-end">
                    <button @click="cancelOrder" :disabled="cancelling"
                        class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-6 py-3 rounded-lg font-semibold">
                        {{ cancelling ? 'Cancelling...' : 'Cancel Order' }}
                    </button>
                </div>

                <router-link
                    :to="{ name: 'invoice', params: { id: order.id } }"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg"
                >
                    Download Invoice
                </router-link>

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

</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axiosClient from '../../axios'
import PublicHeader from '../../components/admin/PublicHeader.vue'
import ReviewForm from '../../components/reviews/ReviewForm.vue'

const route = useRoute()

const loading = ref(true)

const order = ref({})

const cancelling = ref(false)

const toast = ref({
    show: false,
    message: '',
    type: 'success'
})


const showToast = (message, type = 'success') => {

    toast.value = {
        show: true,
        message,
        type
    }

    setTimeout(() => {
        toast.value.show = false
    }, 3000)
}


const cancelOrder = async () => {

    cancelling.value = true

    try {

        await axiosClient.put(`/my-orders/${order.value.id}/cancel`)

        order.value.order_status = 'cancelled'

        showToast('Order cancelled successfully.')

    } catch (error) {

        showToast(
            error.response?.data?.message || 'Unable to cancel order.',
            'error'
        )

    } finally {

        cancelling.value = false

    }

}



const loadOrder = async () => {

    try {

        const { data } = await axiosClient.get(
            `/my-orders/${route.params.id}`
        )

        order.value = data

    } finally {

        loading.value = false

    }

}

const formatDate = (date) => {

    return new Date(date).toLocaleString()

}

const statusClass = (status) => {

    const map = {

        pending: 'bg-yellow-100 text-yellow-700',

        processing: 'bg-blue-100 text-blue-700',

        shipped: 'bg-purple-100 text-purple-700',

        delivered: 'bg-green-100 text-green-700',

        cancelled: 'bg-red-100 text-red-700'

    }

    return map[status] || 'bg-gray-100'

}

const paymentClass = (status) => {

    return status === 'paid'

        ? 'bg-green-100 text-green-700'

        : 'bg-red-100 text-red-700'

}

onMounted(loadOrder)
</script>