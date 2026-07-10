<template>
    <div class="max-w-5xl mx-auto p-8">

        <div class="bg-white shadow rounded-xl p-8">

            <div class="flex justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold">
                        Invoice
                    </h1>

                    <p class="text-gray-500">
                        Order #{{ order.order_number }}
                    </p>
                </div>

                <div class="text-right">
                    <p class="font-bold text-xl">
                        BLS Solutions
                    </p>

                    <p class="text-gray-500">
                        E-Commerce Platform
                    </p>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="font-bold mb-2">
                    Customer Information
                </h2>

                <p>{{ order.detail?.first_name }} {{ order.detail?.last_name }}</p>
                <p>{{ order.detail?.phone }}</p>
                <p>{{ order.detail?.address1 }}</p>
                <p>{{ order.detail?.city }}</p>
            </div>

            <table class="w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Product</th>
                        <th class="p-3 text-left">Price</th>
                        <th class="p-3 text-left">Qty</th>
                        <th class="p-3 text-left">Total</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="item in order.items"
                        :key="item.id"
                        class="border-t"
                    >
                        <td class="p-3">
                            {{ item.product?.name }}
                        </td>

                        <td class="p-3">
                            ${{ item.price }}
                        </td>

                        <td class="p-3">
                            {{ item.quantity }}
                        </td>

                        <td class="p-3">
                            ${{ item.total }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-8 text-right space-y-2">
                <p>Subtotal: ${{ order.subtotal }}</p>
                <p>Shipping: ${{ order.shipping }}</p>
                <p>Tax: ${{ order.tax }}</p>
                <p>Discount: ${{ order.discount }}</p>

                <p class="text-2xl font-bold">
                    Grand Total: ${{ order.grand_total }}
                </p>
            </div>

            <div class="mt-8 flex gap-4">
                <button
                    @click="printInvoice"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg"
                >
                    Print Invoice
                </button>
            </div>

        </div>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axiosClient from '../../axios'

const route = useRoute()

const order = ref({
    items: []
})

const loadInvoice = async () => {
    const { data } = await axiosClient.get(
        `/invoice/${route.params.id}`
    )

    order.value = data
}

const printInvoice = () => {
    window.print()
}

onMounted(() => {
    loadInvoice()
})
</script>



