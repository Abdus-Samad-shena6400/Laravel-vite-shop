<template>
    <div class="p-6">

        <!-- Header -->
        <div class="mb-8">

            <h1 class="text-3xl font-bold text-gray-800">
                Sales Report
            </h1>

            <p class="text-gray-500 mt-2">
                View sales performance by date range.
            </p>

        </div>

        <!-- Filters -->

        <div class="bg-white rounded-xl shadow p-6 mb-8">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>

                    <label class="block mb-2 font-medium">
                        From
                    </label>

                    <input type="date" v-model="filters.from" class="w-full border rounded-lg px-4 py-2">

                </div>

                <div>

                    <label class="block mb-2 font-medium">
                        To
                    </label>

                    <input type="date" v-model="filters.to" class="w-full border rounded-lg px-4 py-2">

                </div>

                <div class="flex items-end gap-3">



                    <button @click="exportPdf" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                        Export PDF
                    </button>

                </div>

            </div>

        </div>

        <!-- Summary Cards -->

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500">
                    Total Orders
                </p>

                <h2 class="text-3xl font-bold mt-3">
                    {{ summary.total_orders }}
                </h2>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500">
                    Total Revenue
                </p>

                <h2 class="text-3xl font-bold mt-3 text-green-600">
                    ${{ summary.total_revenue }}
                </h2>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500">
                    Average Order
                </p>

                <h2 class="text-3xl font-bold mt-3 text-indigo-600">
                    ${{ summary.average_order }}
                </h2>

            </div>

        </div>

        <!-- Orders Table -->

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50">

                        <tr class="text-left text-sm font-semibold text-gray-700">

                            <th class="px-6 py-4">
                                Order #
                            </th>

                            <th class="px-6 py-4">
                                Customer
                            </th>

                            <th class="px-6 py-4">
                                Total
                            </th>

                            <th class="px-6 py-4 text-center">
                                Status
                            </th>

                            <th class="px-6 py-4">
                                Date
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition-colors">

                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ order.order_number }}
                            </td>

                            <td class="px-6 py-4">
                                {{ order.user?.name }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-green-600">
                                ${{ order.grand_total }}
                            </td>

                            <td class="px-6 py-4 text-center">

                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                    :class="statusClass(order.order_status)">
                                    {{ order.order_status }}
                                </span>

                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ formatDate(order.created_at) }}
                            </td>

                        </tr>

                        <tr v-if="orders.length === 0">

                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No report found.
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axiosClient from '../../axios'

const filters = ref({
    from: '',
    to: ''
})

const orders = ref([])

const summary = ref({
    total_orders: 0,
    total_revenue: 0,
    average_order: 0
})

const fetchReport = async () => {
    try {
        const response = await axiosClient.get('/reports/sales', {
            params: filters.value
        })
        orders.value = response.data.orders
        summary.value = response.data.summary
    } catch (error) {
        console.error('Failed to fetch report:', error)
    }
}

const formatDate = (date) => {

    return new Date(date).toLocaleDateString()

}

const statusClass = (status) => {

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

const exportPdf = async () => {

    try {

        const response = await axiosClient.get('/reports/export/pdf', {
            params: filters.value,
            responseType: 'blob'
        })

        const blob = new Blob([response.data], {
            type: 'application/pdf'
        })

        const url = window.URL.createObjectURL(blob)

        const link = document.createElement('a')

        link.href = url

        link.download = 'sales-report.pdf'

        document.body.appendChild(link)

        link.click()

        link.remove()

        window.URL.revokeObjectURL(url)

    } catch (error) {

        // Helpful debugging for API failures (401/500/etc)
        console.error('exportPdf failed:', {
            status: error?.response?.status,
            data: error?.response?.data,
            headers: error?.response?.headers,
            message: error?.message
        })

    }

}

onMounted(() => {
    fetchReport()
})

watch(filters, () => {
    fetchReport()
}, { deep: true })

</script>