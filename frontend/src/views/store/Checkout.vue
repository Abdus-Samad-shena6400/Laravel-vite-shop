<template>
    <div class="bg-gray-100 min-h-screen">
        <PublicHeader />

        <div class="container-standard section-padding">
            <h1 class="text-heading mb-6 sm:mb-8 lg:mb-10">Checkout</h1>

            <div class="grid lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Customer Form -->
                <div class="lg:col-span-2">
                    <div class="card p-4 sm:p-6 lg:p-8">
                        <h2 class="text-subheading mb-6 sm:mb-8">Shipping Information</h2>

                        <!-- First Name -->
                        <div class="mb-4 sm:mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-700">First Name</label>
                            <input v-model="form.first_name" class="input-standard">
                        </div>

                        <!-- Last Name -->
                        <div class="mb-4 sm:mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Last Name</label>
                            <input v-model="form.last_name" class="input-standard">
                        </div>

                        <!-- Phone -->
                        <div class="mb-4 sm:mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Phone</label>
                            <input v-model="form.phone" class="input-standard">
                        </div>

                        <!-- Address -->
                        <div class="mb-4 sm:mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Address Line 1</label>
                            <input v-model="form.address1" class="input-standard">
                        </div>

                        <div class="mb-4 sm:mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Address Line 2</label>
                            <input v-model="form.address2" class="input-standard">
                        </div>

                        <!-- City & State -->
                        <div class="grid md:grid-cols-2 gap-4 mb-4 sm:mb-5">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">City</label>
                                <input v-model="form.city" class="input-standard">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">State</label>
                                <input v-model="form.state" class="input-standard">
                            </div>
                        </div>

                        <!-- Zip & Country -->
                        <div class="grid md:grid-cols-2 gap-4 mb-4 sm:mb-5">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">ZIP Code</label>
                                <input v-model="form.zip_code" class="input-standard">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Country Code</label>
                                <input v-model="form.country_code" placeholder="PK" class="input-standard">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4 sm:mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Order Notes</label>
                            <textarea v-model="form.notes" rows="4" class="input-standard"></textarea>
                        </div>

                        <!-- Payment -->
                        <div class="space-y-3 sm:space-y-4">
                            <label class="flex items-center gap-3 border rounded-xl p-3 sm:p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" value="cash_on_delivery" v-model="form.payment_method">
                                <div>
                                    <div class="font-semibold text-sm sm:text-base">Cash On Delivery</div>
                                    <div class="text-gray-500 text-xs sm:text-sm">Pay when your order arrives.</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 border rounded-xl p-3 sm:p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" value="bank_transfer" v-model="form.payment_method">
                                <div>
                                    <div class="font-semibold text-sm sm:text-base">Bank Transfer</div>
                                    <div class="text-gray-500 text-xs sm:text-sm">Transfer payment manually and enter transaction ID.</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 border rounded-xl p-3 sm:p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" value="stripe" v-model="form.payment_method">
                                <div>
                                    <div class="font-semibold text-sm sm:text-base">Credit/Debit Card (Stripe)</div>
                                    <div class="text-gray-500 text-xs sm:text-sm">Pay securely with your card.</div>
                                </div>
                            </label>
                        </div>

                        <div v-if="form.payment_method === 'bank_transfer'"
                            class="bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-6 mt-4">
                            <h3 class="font-bold text-base sm:text-lg mb-4">Bank Details</h3>
                            <div class="space-y-2 text-gray-700 text-sm">
                                <p><strong>Bank:</strong> {{ config.bank_name }}</p>
                                <p><strong>Account Title:</strong> {{ config.bank_account_title }}</p>
                                <p><strong>Account Number:</strong> {{ config.bank_account_number }}</p>
                                <p><strong>JazzCash:</strong> {{ config.jazzcash_number }}</p>
                                <p><strong>EasyPaisa:</strong> {{ config.easypaisa_number }}</p>
                            </div>
                            <div class="mt-4 sm:mt-6">
                                <label class="block mb-2 font-semibold text-sm">Transaction ID</label>
                                <input v-model="form.transaction_id" class="input-standard" placeholder="Enter payment transaction ID">
                            </div>
                        </div>

                        <div v-if="form.payment_method === 'stripe'"
                            class="bg-gray-50 border border-gray-200 rounded-xl p-4 sm:p-6 mt-4">
                            <h3 class="font-bold text-base sm:text-lg mb-4">Card Payment</h3>
                            <StripePayment
                                v-if="stripeClientSecret"
                                :client-secret="stripeClientSecret"
                                :amount="total"
                                @payment-success="handleStripePaymentSuccess"
                                @payment-error="handleStripePaymentError"
                            />
                            <div v-else class="text-gray-500">
                                Loading payment form...
                            </div>
                        </div>

                        <button @click="placeOrder" :disabled="loading || (form.payment_method === 'stripe' && !paymentIntentId.value)"
                            class="btn-primary w-full py-3 sm:py-4 text-sm sm:text-base disabled:bg-gray-400 disabled:cursor-not-allowed mb-3">
                            {{ loading ? 'Placing Order...' : 'Place Order' }}
                        </button>

                        <button @click="clearCart" class="btn-danger w-full py-3 text-sm sm:text-base">
                            Clear Cart
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div>
                    <div class="card p-4 sm:p-6 lg:p-8">
                        <h2 class="text-subheading mb-4 sm:mb-6">Order Summary</h2>

                        <!-- Coupon Code -->
                        <div class="mb-4 sm:mb-6">
                            <label class="block font-semibold mb-2 text-sm">Coupon Code</label>
                            <div class="flex gap-2">
                                <input v-model="couponCode" type="text" class="input-standard flex-1" placeholder="Enter coupon code">
                                <button @click="applyCoupon" class="btn-primary px-3 sm:px-4 text-sm">Apply</button>
                            </div>
                        </div>

                        <div v-for="item in cartItems" :key="item.product.id" class="flex justify-between mb-3 sm:mb-4 text-sm">
                            <span>{{ item.product.name }} × {{ item.quantity }}</span>
                            <span>${{ (item.quantity * getItemPrice(item.product)).toFixed(2) }}</span>
                        </div>

                        <div class="flex justify-between mb-3 text-sm">
                            <span>Payment Method</span>
                            <span class="font-semibold text-indigo-600">{{ paymentMethodLabel }}</span>
                        </div>

                        <hr class="my-4 sm:my-6">

                        <div class="flex justify-between mb-3 text-sm">
                            <span>Subtotal</span>
                            <span>${{ subtotal.toFixed(2) }}</span>
                        </div>

                        <div class="flex justify-between mb-3 text-sm">
                            <span>Discount</span>
                            <span class="text-green-600">-${{ couponDiscount.toFixed(2) }}</span>
                        </div>

                        <div class="flex justify-between mb-3 text-sm">
                            <span>Shipping</span>
                            <span>${{ shipping.toFixed(2) }}</span>
                        </div>

                        <div class="flex justify-between mb-3 text-sm">
                            <span>Tax</span>
                            <span>${{ tax.toFixed(2) }}</span>
                        </div>

                        <hr class="my-4 sm:my-6">

                        <div class="flex justify-between text-lg sm:text-xl font-bold">
                            <span>Total</span>
                            <span>${{ total.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, computed, onMounted, ref, watch } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import axiosClient from '../../axios'
import PublicHeader from '../../components/admin/PublicHeader.vue'
import { useToast } from 'vue-toastification'
import StripePayment from '../../components/StripePayment.vue'



const couponCode = ref('')
const coupon = ref(null)
const couponDiscount = ref(0)
const stripeClientSecret = ref(null)
const paymentIntentId = ref(null)

const toast = useToast()
const router = useRouter()

const store = useStore()
const loading = ref(false)
const config = reactive({
    bank_name: 'Bank of Punjab',
    bank_account_title: 'Ecommerce Store',
    bank_account_number: '1234567890',
    jazzcash_number: '+92 300 1234567',
    easypaisa_number: '+92 300 7654321'
})

const applyCoupon = async () => {
    try {

        const { data } = await axiosClient.post('/coupons/apply', {
            code: couponCode.value,
            subtotal: subtotal.value
        })

        coupon.value = data.coupon
        couponDiscount.value = data.coupon.discount

        alert('Coupon applied successfully.')

    } catch (error) {

        alert(error.response?.data?.message || 'Invalid coupon.')

    }
}

const createPaymentIntent = async () => {
    try {
        const { data } = await axiosClient.post('/checkout/create-payment-intent', {
            amount: total.value,
            currency: 'usd'
        })
        stripeClientSecret.value = data.client_secret
    } catch (error) {
        console.error('Failed to create payment intent:', error)
        toast.error('Failed to initialize payment. Please try again.')
    }
}

const handleStripePaymentSuccess = (paymentIntent) => {
    paymentIntentId.value = paymentIntent.id
    // Don't auto-place order - let user click the button
    toast.success('Payment successful! Click "Place Order" to complete your order.')
}

const handleStripePaymentError = (error) => {
    toast.error(error.message || 'Payment failed. Please try again.')
}

const cartItems = computed(() => store.getters.cartItems)

const getItemPrice = (product) => {
    // Use hot deal price if available, otherwise use regular price
    return product.deal_price || product.price || product.sale_price || product.regular_price || 0
}

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

const subtotal = computed(() => {
    return cartItems.value.reduce((total, item) => {
        return total + (item.quantity * getItemPrice(item.product))
    }, 0)
})

const paymentMethodLabel = computed(() => {
    const labels = {
        'cash_on_delivery': 'Cash On Delivery',
        'bank_transfer': 'Bank Transfer',
        'stripe': 'Credit/Debit Card'
    }
    return labels[form.payment_method] || form.payment_method
})

const shipping = computed(() => subtotal.value > 150 ? 0 : 15)

const tax = computed(() => subtotal.value * 0.1)

const total = computed(() =>
    subtotal.value +
    shipping.value +
    tax.value -
    couponDiscount.value
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
    country_code: '',
    notes: '',
    payment_method: 'cash_on_delivery',
    transaction_id: ''
})

// Watch for payment method changes to create payment intent
watch(() => form.payment_method, (newMethod) => {
    if (newMethod === 'stripe') {
        createPaymentIntent()
    } else {
        stripeClientSecret.value = null
        paymentIntentId.value = null
    }
})

const placeOrder = async () => {
    loading.value = true
    try {
        if (form.payment_method === 'bank_transfer' && !form.transaction_id?.trim()) {
            toast.error('Please enter a transaction ID for bank transfer payments.')
            loading.value = false
            return
        }

        if (form.payment_method === 'stripe' && !paymentIntentId.value) {
            toast.error('Please complete the card payment first.')
            loading.value = false
            return
        }

        const payload = {

            ...form,

            cart: cartItems.value,

            coupon_id: coupon.value?.id || null,

            coupon_code: coupon.value?.code || null,

            discount: couponDiscount.value,

            payment_intent_id: paymentIntentId.value || null

        }

        console.log('Checkout payload:', payload)

        const { data } = await axiosClient.post(
            '/checkout',
            payload
        )

        toast.success(data.message)

        // Clear cart

        store.dispatch('clearCart')

        // Redirect to User Dashboard

        router.push({
            name: 'order-success',
            params: {
                id: data.order_id
            },
            query: {
                paymentMethod: data.payment_method,
                paymentStatus: data.payment_status,
                orderStatus: data.order_status
            }
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
    {
        store.dispatch('clearCart')
        toast.success('Cart cleared successfully.')
        router.push({
            name: 'cart'
        })
    }
}
</script>