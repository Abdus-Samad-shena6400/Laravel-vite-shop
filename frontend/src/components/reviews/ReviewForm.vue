<template>
  <div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">
      Write a Review
    </h2>

    <div v-if="isLoggedIn">
      <!-- Rating -->
      <label class="block font-medium mb-3">
        Rating
      </label>

      <div class="flex gap-2 mb-5">
        <button
          v-for="star in 5"
          :key="star"
          @click="form.rating = star"
          type="button"
          class="text-3xl transition"
        >
          <span
            :class="star <= form.rating ? 'text-yellow-400' : 'text-gray-300'"
          >
            ★
          </span>
        </button>
      </div>

      <!-- Comment -->
      <label class="block font-medium mb-2">
        Comment
      </label>

      <textarea
        v-model="form.comment"
        rows="5"
        class="w-full border rounded-lg px-4 py-3 resize-none"
        placeholder="Write your review..."
      ></textarea>

      <button
        @click="submitReview"
        :disabled="loading"
        class="mt-5 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg"
      >
        {{ loading ? 'Submitting...' : 'Submit Review' }}
      </button>
    </div>

    <div
      v-else
      class="bg-yellow-50 border border-yellow-200 rounded-lg p-4"
    >
      Please login to write a review.
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useStore } from 'vuex'
import axiosClient from '../../axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const props = defineProps({
  productId: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['submitted'])

const store = useStore()

const loading = ref(false)

const form = ref({
  rating: 5,
  comment: ''
})

const isLoggedIn = computed(() => !!store.state.user.token)

const submitReview = async () => {
  if (!form.value.comment.trim()) {
    toast.error('Please write your review.')
    return
  }

  loading.value = true

  try {
    await axiosClient.post(`/products/${props.productId}/reviews`, {
      rating: form.value.rating,
      comment: form.value.comment
    })

    form.value.rating = 5
    form.value.comment = ''

    toast.success('Review submitted successfully! Waiting for admin approval.')

    emit('submitted')

  } catch (error) {
    console.error(error)
    toast.error(error.response?.data?.message || 'Failed to submit review.')
  } finally {
    loading.value = false
  }
}
</script>
