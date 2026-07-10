<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4 sm:p-6">
        <div class="card p-6 sm:p-8 lg:p-10 max-w-2xl w-full text-center">
            <div class="text-5xl sm:text-6xl mb-4 sm:mb-6">✅</div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-green-600 mb-3 sm:mb-4">
                Order Placed Successfully
            </h1>
            <p class="text-gray-600 mb-6 sm:mb-8 text-sm sm:text-base">
                Thank you for your order.
            </p>

            <div class="bg-gray-50 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-left space-y-3 sm:space-y-4">
                <div class="flex justify-between text-sm sm:text-base">
                    <span>Order ID</span>
                    <span class="font-bold">#{{ $route.params.id }}</span>
                </div>

                <div class="flex justify-between text-sm sm:text-base">
                    <span>Payment Method</span>
                    <span class="font-bold text-indigo-600">{{ paymentMethodLabel }}</span>
                </div>

                <div class="flex justify-between text-sm sm:text-base">
                    <span>Payment Status</span>
                    <span class="text-orange-600 font-bold">{{ paymentStatusLabel }}</span>
                </div>

                <div class="flex justify-between text-sm sm:text-base">
                    <span>Order Status</span>
                    <span class="text-blue-600 font-bold">{{ orderStatusLabel }}</span>
                </div>
            </div>

            <div class="mt-6 sm:mt-8 flex gap-3 sm:gap-4 justify-center flex-col sm:flex-row">
                <router-link :to="{ name: 'user-dashboard' }" class="btn-primary px-4 py-2 sm:px-6 sm:py-3 text-sm sm:text-base">
                    My Orders
                </router-link>
                <router-link :to="{ name: 'shop' }" class="btn-secondary px-4 py-2 sm:px-6 sm:py-3 text-sm sm:text-base">
                    Continue Shopping
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const paymentMethodLabel = computed(() => {
    const method = route.query.paymentMethod
    const labels = {
        'cash_on_delivery': 'Cash On Delivery',
        'bank_transfer': 'Bank Transfer'
    }
    return labels[method] || method || 'N/A'
})

const paymentStatusLabel = computed(() => {
    return (route.query.paymentStatus || 'Pending').toString().replace(/_/g, ' ')
})

const orderStatusLabel = computed(() => {
    return (route.query.orderStatus || 'Pending').toString().replace(/_/g, ' ')
})
</script>