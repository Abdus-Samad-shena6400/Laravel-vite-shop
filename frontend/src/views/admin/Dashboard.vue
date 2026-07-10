<template>
    <div class="p-0 sm:p-2 lg:p-6">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-heading">Dashboard</h1>
            <p class="text-body mt-2">Welcome to your Admin Dashboard.</p>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center py-20">
            <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
            </svg>
        </div>

        <!-- Dashboard Cards -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <div v-for="card in cards" :key="card.title" class="card-admin p-4 sm:p-5 min-h-36 flex items-center justify-between">
                <!-- Icon -->
                <div :class="card.color" class="w-12 sm:w-14 h-12 sm:h-14 rounded-xl flex items-center justify-center">
                    <component :is="card.icon" class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
                </div>
                <!-- Value -->
                <div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800">{{ card.value }}</h2>
                    <p class="text-gray-500 mt-2 text-sm sm:text-base lg:text-lg">{{ card.title }}</p>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="mt-6 sm:mt-8 card-admin p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 sm:mb-6">Revenue (Last 7 Days)</h2>
            <canvas ref="chartCanvas" height="90"></canvas>
        </div>

        <div class="mt-6 sm:mt-8">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 sm:mb-6">Order Status</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 sm:p-5">
                    <p class="text-yellow-700 font-medium text-sm sm:text-base">Pending</p>
                    <h2 class="text-2xl sm:text-3xl font-bold mt-2 sm:mt-3">{{ orderStatus.pending ?? 0 }}</h2>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-5">
                    <p class="text-blue-700 font-medium text-sm sm:text-base">Processing</p>
                    <h2 class="text-2xl sm:text-3xl font-bold mt-2 sm:mt-3">{{ orderStatus.processing ?? 0 }}</h2>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 sm:p-5">
                    <p class="text-green-700 font-medium text-sm sm:text-base">Delivered</p>
                    <h2 class="text-2xl sm:text-3xl font-bold mt-2 sm:mt-3">{{ orderStatus.delivered ?? 0 }}</h2>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 sm:p-5">
                    <p class="text-red-700 font-medium text-sm sm:text-base">Cancelled</p>
                    <h2 class="text-2xl sm:text-3xl font-bold mt-2 sm:mt-3">{{ orderStatus.cancelled ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="mt-6 sm:mt-8 lg:mt-10 card-admin">
            <div class="border-b px-4 sm:px-6 py-4 flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
                <h2 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800">Latest Orders</h2>
                <router-link :to="{ name: 'orders' }" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm sm:text-base">View All</router-link>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left">Order #</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Customer</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Total</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Payment</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Status</th>
                            <th class="px-4 sm:px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in latestOrders" :key="order.id" class="border-t hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-3 sm:py-4">{{ order.order_number }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">{{ order.user?.name }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">${{ order.grand_total }}</td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs" :class="paymentClass(order.payment_status)">
                                    {{ order.payment_status }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs" :class="statusClass(order.order_status)">
                                    {{ order.order_status }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                <router-link :to="{ name: 'orders.show', params: { id: order.id } }" class="text-indigo-600 hover:underline text-xs sm:text-sm">
                                    View
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>

import {
    CubeIcon,
    CollectionIcon,
    TagIcon,
    ShoppingCartIcon,
    UsersIcon,
    CurrencyDollarIcon
} from '@heroicons/vue/outline'

import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend
} from 'chart.js/auto'

Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    Tooltip,
    Legend
)

import { ref, onMounted, computed, nextTick } from 'vue'
import axiosClient from '../../axios'
const latestOrders = ref([])
const orderStatus = ref({})
const chartCanvas = ref(null)
const revenueChart = ref([])

const loading = ref(true)

const statistics = ref({})

const loadDashboard = async () => {

    try {

        const { data } = await axiosClient.get('/dashboard')

        statistics.value = data.statistics

        latestOrders.value = data.latestOrders

        revenueChart.value = data.revenueChart

        orderStatus.value = data.orderStatus

    }

    catch (error) {

        console.log(error)

    }

    finally {

        loading.value = false

        await nextTick()

        renderChart()

    }

}


const renderChart = async () => {

    new Chart(chartCanvas.value, {

        type: 'line',

        data: {

            labels: revenueChart.value.map(item => item.date),

            datasets: [

                {

                    label: 'Revenue',

                    data: revenueChart.value.map(item => item.total),

                    borderColor: '#4F46E5',

                    backgroundColor: 'rgba(79,70,229,.2)',

                    fill: true,

                    tension: .4,

                    pointRadius: 5,

                    pointBackgroundColor: '#4F46E5'

                }

            ]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {

                    display: false

                }

            },

            scales: {

                y: {

                    beginAtZero: true

                }

            }

        }

    })

}



const cards = computed(() => [

    {
        title: 'Products',
        value: statistics.value.products ?? 0,
        icon: CubeIcon,
        color: 'bg-blue-500'
    },

    {
        title: 'Categories',
        value: statistics.value.categories ?? 0,
        icon: CollectionIcon,
        color: 'bg-green-500'
    },

    {
        title: 'Brands',
        value: statistics.value.brands ?? 0,
        icon: TagIcon,
        color: 'bg-purple-500'
    },

    {
        title: 'Orders',
        value: statistics.value.orders ?? 0,
        icon: ShoppingCartIcon,
        color: 'bg-orange-500'
    },

    {
        title: 'Customers',
        value: statistics.value.customers ?? 0,
        icon: UsersIcon,
        color: 'bg-pink-500'
    },

    {
        title: 'Revenue',
        value: `$${statistics.value.revenue ?? 0}`,
        icon: CurrencyDollarIcon,
        color: 'bg-emerald-500'
    }

])


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

onMounted(loadDashboard)

</script>
