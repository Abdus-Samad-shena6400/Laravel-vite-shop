<template>
  <div>
    <div id="card-element" class="stripe-card-element"></div>
    <button 
      @click="handlePayment" 
      :disabled="loading || !stripeLoaded"
      class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
    >
      {{ loading ? 'Processing...' : 'Pay with Stripe' }}
    </button>
    <div v-if="error" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { loadStripe } from '@stripe/stripe-js'

const props = defineProps({
  clientSecret: String,
  amount: Number
})

const emit = defineEmits(['payment-success', 'payment-error'])

const stripe = ref(null)
const cardElement = ref(null)
const elements = ref(null)
const loading = ref(false)
const error = ref(null)
const stripeLoaded = ref(false)

onMounted(async () => {
  if (!props.clientSecret) {
    error.value = 'Payment initialization failed. Missing client secret.'
    return
  }

  try {
    stripe.value = await loadStripe(import.meta.env.VITE_STRIPE_KEY)
    
    if (!stripe.value) {
      error.value = 'Failed to load Stripe. Please refresh the page.'
      return
    }

    elements.value = stripe.value.elements()
    cardElement.value = elements.value.create('card', {
      style: {
        base: {
          fontSize: '16px',
          color: '#424770',
          '::placeholder': {
            color: '#aab7c4',
          },
        },
      },
    })
    cardElement.value.mount('#card-element')
    stripeLoaded.value = true
  } catch (err) {
    console.error('Stripe initialization error:', err)
    error.value = 'Failed to initialize payment form. Please try again.'
  }
})

onUnmounted(() => {
  if (cardElement.value) {
    cardElement.value.destroy()
  }
})

const handlePayment = async () => {
  if (!stripe.value || !cardElement.value) {
    error.value = 'Payment form not ready. Please wait.'
    return
  }

  loading.value = true
  error.value = null

  try {
    const { error: stripeError, paymentIntent } = await stripe.value.confirmCardPayment(props.clientSecret, {
      payment_method: {
        card: cardElement.value,
        billing_details: {
          name: 'Customer', // You can pass customer details if needed
        },
      }
    })

    if (stripeError) {
      error.value = stripeError.message
      emit('payment-error', stripeError)
    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
      emit('payment-success', paymentIntent)
    } else {
      error.value = 'Payment processing failed. Please try again.'
      emit('payment-error', { message: 'Payment processing failed' })
    }
  } catch (err) {
    console.error('Payment error:', err)
    error.value = 'An unexpected error occurred. Please try again.'
    emit('payment-error', err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.stripe-card-element {
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  margin-bottom: 16px;
  background: white;
}

.stripe-card-element:focus-within {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}
</style>