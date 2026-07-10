<template>
    <div class="p-4 sm:p-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">

            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    Customers
                </h1>

                <p class="text-gray-500">
                    Manage your customers.
                </p>
            </div>

            <input v-model="search" @input="loadCustomers" type="text" placeholder="Search customers..."
                class="border rounded-lg px-4 py-2 w-full sm:w-72 focus:ring-2 focus:ring-indigo-500 outline-none">

        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">

            <div class="overflow-x-auto">
            <table class="w-full min-w-[720px]">

                <thead class="bg-gray-50">
                    <tr class="text-left">
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Total Orders</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="customer in customers.data" :key="customer.id" class="border-t hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div
                                    class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3">
                                    {{ customer.name.charAt(0).toUpperCase() }}
                                </div>

                                <div>
                                    <div class="font-semibold">
                                        {{ customer.name }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            {{ customer.email }}
                        </td>

                        <td class="px-6 py-4">
                            {{ customer.orders_count }}
                        </td>

                        <td class="px-6 py-4">
                            {{ formatDate(customer.created_at) }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            <router-link :to="{ name: 'customers.show', params: { id: customer.id } }"
                                class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">
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
import { ref, onMounted } from 'vue'
import axiosClient from '../../axios'
import { formatDate } from '../../utils/dateFormatter'

const customers = ref({
    data: []
})

const search = ref('')

const loadCustomers = async () => {

    const { data } = await axiosClient.get('/customers', {
        params: {
            search: search.value
        }
    })

    customers.value = data

}

onMounted(loadCustomers)
</script>