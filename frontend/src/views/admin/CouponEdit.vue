<template>
    <div class="p-6">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Edit Coupon
            </h1>

            <p class="text-gray-500 mt-1">
                Edit a coupon for your store.
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl sadow-lg p-8">

            <form @submit.prevent="saveCoupon">

                <!-- Coupon Code -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">
                        Coupon Code
                    </label>

                    <input type="text" v-model="form.code" class="w-full border rounded-lg px-4 py-3"
                        placeholder="SAVE20">
                </div>

                <!-- Coupon Name -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">
                        Coupon Name
                    </label>

                    <input type="text" v-model="form.name" class="w-full border rounded-lg px-4 py-3"
                        placeholder="Summer Sale Discount">
                </div>

                <!-- Coupon Type -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">
                        Coupon Type
                    </label>

                    <select v-model="form.type" class="w-full border rounded-lg px-4 py-3">
                        <option value="fixed">
                            Fixed Amount
                        </option>

                        <option value="percentage">
                            Percentage
                        </option>
                    </select>
                </div>

                <!-- Coupon Value -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">
                        Discount Value
                    </label>

                    <input type="number" v-model="form.value" class="w-full border rounded-lg px-4 py-3">
                </div>

                <!-- Minimum Order -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">
                        Minimum Order Amount
                    </label>

                    <input type="number" v-model="form.minimum_amount" class="w-full border rounded-lg px-4 py-3">
                </div>

                <!-- Maximum Discount -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">
                        Maximum Discount
                    </label>

                    <input type="number" v-model="form.maximum_discount" class="w-full border rounded-lg px-4 py-3">
                </div>

                <!-- Usage Limit -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">
                        Usage Limit
                    </label>

                    <input type="number" v-model="form.usage_limit" class="w-full border rounded-lg px-4 py-3">
                </div>

                <!-- Start Date -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">
                        Start Date
                    </label>

                    <input type="date" v-model="form.starts_at" class="w-full border rounded-lg px-4 py-3">
                </div>

                <!-- Expiry Date -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">
                        Expiry Date
                    </label>

                    <input type="date" v-model="form.expires_at" class="w-full border rounded-lg px-4 py-3">
                </div>

                <!-- Status -->
                <div class="mb-8">

                    <label class="block text-sm font-semibold mb-3">
                        Status
                    </label>

                    <label class="mr-6">

                        <input type="radio" v-model="form.status" :value="true">

                        Active

                    </label>

                    <label>

                        <input type="radio" v-model="form.status" :value="false">

                        Inactive

                    </label>

                </div>

                <!-- Buttons -->

                <div class="flex justify-end gap-3">

                    <router-link :to="{ name: 'coupons' }" class="border px-5 py-2 rounded-lg hover:bg-gray-100">
                        Cancel
                    </router-link>

                    <button type="submit" :disabled="submitting"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                        {{ submitting ? 'Updating...' : 'Update Coupon' }}
                    </button>

                </div>

            </form>

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
import { useRouter, useRoute } from 'vue-router'
import axiosClient from '../../axios'

const router = useRouter()
const route = useRoute()

const form = ref({
    code: '',
    name: '',
    type: 'fixed',
    value: '',
    minimum_amount: '',
    maximum_discount: '',
    usage_limit: '',
    starts_at: '',
    expires_at: '',
    status: true
})



const showToast = (message, type = 'success') => {

    toast.value.message = message
    toast.value.type = type
    toast.value.show = true

    setTimeout(() => {
        toast.value.show = false
    }, 3000)

}


const submitting = ref(false)

const errors = ref({})

const toast = ref({
    show: false,
    message: '',
    type: 'success'
})


const loadCoupon = async () => {
    try {

        const { data } = await axiosClient.get(`/coupons/${route.params.id}`)

        form.value = {
            code: data.code,
            name: data.name,
            type: data.type,
            value: data.value,
            minimum_amount: data.minimum_amount,
            maximum_discount: data.maximum_discount,
            usage_limit: data.usage_limit,
            starts_at: data.starts_at,
            expires_at: data.expires_at,
            status: data.status
        }

    } catch (error) {

        console.log(error)

    }
}
const saveCoupon = async () => {

    submitting.value = true

    try {

        await axiosClient.put(`/coupons/${route.params.id}`, {

            code: form.value.code,
            name: form.value.name,
            type: form.value.type,
            value: form.value.value,
            minimum_amount: form.value.minimum_amount || null,
            maximum_discount: form.value.maximum_discount || null,
            usage_limit: form.value.usage_limit || null,
            starts_at: form.value.starts_at || null,
            expires_at: form.value.expires_at || null,
            status: form.value.status

        })

        showToast('Coupon updated successfully.')

        router.push({ name: 'coupons' })

    } catch (error) {

        console.log(error.response)

    } finally {

        submitting.value = false

    }

}

onMounted(() => {
    loadCoupon()
})
</script>