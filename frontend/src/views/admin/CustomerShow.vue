<template>
    <div class="p-6">

        <!-- Header -->
        <div class="mb-8">
            <router-link :to="{ name: 'customers' }" class="text-indigo-600 hover:underline">
                ← Back to Customers
            </router-link>

            <h1 class="text-3xl font-bold mt-2">
                Customer Details
            </h1>
        </div>

        <!-- Customer Card -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">

            <div class="flex items-center">

                <div
                    class="w-20 h-20 rounded-full bg-indigo-600 text-white text-3xl flex items-center justify-center mr-6">
                    {{ customer.name?.charAt(0).toUpperCase() }}
                </div>

                <div>

                    <h2 class="text-2xl font-bold">
                        {{ customer.name }}
                    </h2>

                    <p class="text-gray-500">
                        {{ customer.email }}
                    </p>

                    <p class="text-gray-500 mt-1">
                        Joined:
                        {{ formatDate(customer.created_at) }}
                    </p>

                </div>

            </div>

        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500">
                    Total Orders
                </p>

                <h2 class="text-3xl font-bold mt-2">
                    {{ statistics.total_orders }}
                </h2>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500">
                    Total Spent
                </p>

                <h2 class="text-3xl font-bold mt-2">
                    ${{ statistics.total_spent }}
                </h2>

            </div>

        </div>

        <!-- Orders -->
        <div class="bg-white rounded-xl shadow">

            <div class="border-b p-6">

                <h2 class="text-xl font-semibold">
                    Order History
                </h2>

            </div>

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="text-left px-6 py-3">
                            Order #
                        </th>

                        <th>Total</th>

                        <th>Status</th>

                        <th>Payment</th>

                        <th>Date</th>

                        <th class="text-center">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr v-for="order in orders" :key="order.id" class="border-t">

                        <td class="px-6 py-4">
                            {{ order.order_number }}
                        </td>

                        <td>
                            ${{ order.grand_total }}
                        </td>

                        <td>

                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                :class="orderStatusClass(order.order_status)">

                                {{ order.order_status }}

                            </span>

                        </td>

                        <td>

                            <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="order.payment_status === 'paid'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700'">

                                {{ order.payment_status }}

                            </span>

                        </td>

                        <td>
                            {{ formatDate(order.created_at) }}
                        </td>

                        <td class="text-center">

                            <router-link :to="{ name: 'orders.show', params: { id: order.id } }"
                                class="text-indigo-600 hover:text-indigo-800 font-medium">
                                View
                            </router-link>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axiosClient from '../../axios'

const route = useRoute()

const customer = ref({})
const orders = ref([])
const statistics = ref({})

const loadCustomer = async () => {

    const { data } = await axiosClient.get(
        `/customers/${route.params.id}`
    )

    customer.value = data.customer
    orders.value = data.orders
    statistics.value = data.statistics

}

const formatDate = (date) => {

    return new Date(date).toLocaleDateString()

}

const orderStatusClass = (status) => {

    switch (status?.toLowerCase()) {

        case 'pending':
            return 'bg-yellow-100 text-yellow-700'

        case 'processing':
            return 'bg-blue-100 text-blue-700'

        case 'shipped':
            return 'bg-purple-100 text-purple-700'

        case 'delivered':
            return 'bg-green-100 text-green-700'

        case 'cancelled':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'

    }

}

onMounted(loadCustomer)
</script>