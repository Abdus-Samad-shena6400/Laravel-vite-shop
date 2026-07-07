<template>

    <div class="bg-gray-100 min-h-screen">

        <PublicHeader />

        <div class="max-w-7xl mx-auto py-10 px-4">

            <h1 class="text-4xl font-bold mb-10">

                Checkout

            </h1>

            <div class="grid lg:grid-cols-3 gap-8">

                <!-- Customer Form -->

                <div class="lg:col-span-2">

                    <div class="bg-white rounded-2xl shadow p-8">

                        <h2 class="text-2xl font-bold mb-8">

                            Shipping Information

                        </h2>

                        <!-- First Name -->

                        <div class="mb-5">

                            <label class="block mb-2">

                                First Name

                            </label>

                            <input v-model="form.first_name" class="w-full border rounded-lg p-3">

                        </div>

                        <!-- Last Name -->

                        <div class="mb-5">

                            <label class="block mb-2">

                                Last Name

                            </label>

                            <input v-model="form.last_name" class="w-full border rounded-lg p-3">

                        </div>

                        <!-- Phone -->

                        <div class="mb-5">

                            <label class="block mb-2">

                                Phone

                            </label>

                            <input v-model="form.phone" class="w-full border rounded-lg p-3">

                        </div>

                        <!-- Address -->

                        <div class="mb-5">

                            <label class="block mb-2">

                                Address Line 1

                            </label>

                            <input v-model="form.address1" class="w-full border rounded-lg p-3">

                        </div>

                        <div class="mb-5">

                            <label class="block mb-2">

                                Address Line 2

                            </label>

                            <input v-model="form.address2" class="w-full border rounded-lg p-3">

                        </div>
                        <!-- City & State -->

                        <div class="grid md:grid-cols-2 gap-4 mb-5">

                            <div>

                                <label class="block mb-2">

                                    City

                                </label>

                                <input v-model="form.city" class="w-full border rounded-lg p-3">

                            </div>

                            <div>

                                <label class="block mb-2">

                                    State

                                </label>

                                <input v-model="form.state" class="w-full border rounded-lg p-3">

                            </div>

                        </div>

                        <!-- Zip & Country -->

                        <div class="grid md:grid-cols-2 gap-4 mb-5">

                            <div>

                                <label class="block mb-2">

                                    ZIP Code

                                </label>

                                <input v-model="form.zip_code" class="w-full border rounded-lg p-3">

                            </div>

                            <div>

                                <label class="block mb-2">

                                    Country Code

                                </label>

                                <input v-model="form.country_code" placeholder="PK"
                                    class="w-full border rounded-lg p-3">

                            </div>

                        </div>

                        <!-- Notes -->

                        <div class="mb-5">

                            <label class="block mb-2">

                                Order Notes

                            </label>

                            <textarea v-model="form.notes" rows="4" class="w-full border rounded-lg p-3"></textarea>

                        </div>

                        <!-- Payment -->

                        <div class="mb-8">

                            <label class="block mb-3 font-semibold">

                                Payment Method

                            </label>

                            <label class="flex items-center gap-3">

                                <input type="radio" value="cash_on_delivery" v-model="form.payment_method">

                                Cash on Delivery

                            </label>

                        </div>

                        <button @click="placeOrder" :disabled="loading"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-xl font-semibold disabled:bg-gray-400 mb-3">
                            {{ loading ? 'Placing Order...' : 'Place Order' }}
                        </button>

                        <button @click="clearCart"
                            class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-semibold">
                            Clear Cart
                        </button>

                    </div>

                </div>

                <!-- Order Summary -->

                <div>

                    <div class="bg-white rounded-2xl shadow p-8">

                        <h2 class="text-2xl font-bold mb-6">

                            Order Summary

                        </h2>

                        <div v-for="item in cartItems" :key="item.product.id" class="flex justify-between mb-4">

                            <span>

                                {{ item.product.name }}

                                ×

                                {{ item.quantity }}

                            </span>

                            <span>

                                ${{ (item.quantity * (item.product.price || item.product.sale_price || item.product.regular_price || 0)).toFixed(2) }}

                            </span>

                        </div>

                        <hr class="my-6">

                        <div class="flex justify-between mb-3">

                            <span>

                                Subtotal

                            </span>

                            <span>

                                ${{ subtotal.toFixed(2) }}

                            </span>

                        </div>

                        <div class="flex justify-between mb-3">

                            <span>

                                Shipping

                            </span>

                            <span>

                                ${{ shipping.toFixed(2) }}

                            </span>

                        </div>

                        <div class="flex justify-between mb-3">

                            <span>

                                Tax

                            </span>

                            <span>

                                ${{ tax.toFixed(2) }}

                            </span>

                        </div>

                        <hr class="my-6">

                        <div class="flex justify-between text-xl font-bold">

                            <span>

                                Total

                            </span>

                            <span>

                                ${{ total.toFixed(2) }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</template>

<script setup>
import { reactive, computed, onMounted, ref } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import axiosClient from '../../axios'
import PublicHeader from '../../components/admin/PublicHeader.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
const router = useRouter()

const store = useStore()
const loading = ref(false)

const cartItems = computed(() => store.getters.cartItems)



onMounted(() => {

    if (cartItems.value.length === 0) {

        router.push({
            name: 'cart'
        })

    }

    // Check if user is authenticated
    if (!store.getters.userToken) {
        router.push({
            name: 'login'
        })
    }

})

const subtotal = computed(() => store.getters.cartTotal || 0)

const shipping = computed(() => subtotal.value > 150 ? 0 : 15)

const tax = computed(() => subtotal.value * 0.1)

const total = computed(() =>
    subtotal.value +
    shipping.value +
    tax.value
)

const form = reactive({
    first_name: '',
    last_name: '',
    phone: '',
    address1: '',
    address2: '',
    city: '',
    state: '',
    zip_code: '',
    country_code: 'PK',
    notes: '',
    payment_method: 'cash_on_delivery'
})

const placeOrder = async () => {
    loading.value = true
    try {

        const payload = {

            ...form,

            cart: cartItems.value

        }

        console.log('Checkout payload:', payload)

        const { data } = await axiosClient.post(
            '/checkout',
            payload
        )

        toast.success(data.message)

        // Clear cart

        store.dispatch('clearCart')

        // Redirect to My Orders

        router.push({
            name: 'myOrders'
        })

    } catch (error) {

        console.error(error)

        if (error.response?.data?.errors) {

            toast.error('Please fill all required fields.')

        } else {

            toast.error(error.response?.data?.message || 'Checkout failed.')

        }

    } finally {
        loading.value = false
    }

}

const clearCart = () => {
    if (confirm('Are you sure you want to clear your cart?')) {
        store.dispatch('clearCart')
        router.push({
            name: 'cart'
        })
    }
}
</script>