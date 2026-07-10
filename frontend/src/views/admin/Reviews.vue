<template>
  <div class="p-4 sm:p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold">Reviews Management</h1>
      
      <div class="flex w-full sm:w-auto gap-4">
        <select
          v-model="statusFilter"
          @change="loadReviews"
          class="w-full sm:w-auto border rounded-lg px-4 py-2"
        >
          <option value="">All Reviews</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="text-center py-10">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="reviews.length === 0" class="text-center py-10 text-gray-500">
      No reviews found.
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="review in reviews"
        :key="review.id"
        class="bg-white rounded-lg shadow p-4 sm:p-6"
      >
        <div class="flex flex-col gap-4 lg:flex-row lg:justify-between lg:items-start">
          <div class="flex-1">
            <div class="flex items-center gap-4 mb-3">
              <span class="font-semibold">{{ review.user?.name }}</span>
              <span class="text-gray-500 text-sm">{{ formatDate(review.created_at) }}</span>
              <span
                class="px-3 py-1 rounded-full text-xs font-semibold"
                :class="review.status ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
              >
                {{ review.status ? 'Approved' : 'Pending' }}
              </span>
            </div>

            <div class="flex items-center gap-2 mb-3">
              <StarRating :rating="review.rating" size="sm" />
              <span class="text-gray-600 text-sm">{{ review.product?.name }}</span>
            </div>

            <p class="text-gray-700 whitespace-pre-line">{{ review.comment }}</p>
          </div>

          <div class="flex flex-wrap gap-2 lg:ml-4">
            <button
              v-if="!review.status"
              @click="approveReview(review)"
              class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm"
            >
              Approve
            </button>
            <button
              v-if="review.status"
              @click="rejectReview(review)"
              class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg text-sm"
            >
              Reject
            </button>
            <button
              @click="deleteReview(review)"
              class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axiosClient from '../../axios'
import { useToast } from 'vue-toastification'
import StarRating from '../../components/reviews/StarRating.vue'

const toast = useToast()

const loading = ref(true)
const reviews = ref([])
const statusFilter = ref('')

const loadReviews = async () => {
  loading.value = true
  try {
    const { data } = await axiosClient.get('/reviews', {
      params: { status: statusFilter.value }
    })
    reviews.value = data
  } catch (error) {
    console.error('Failed to load reviews:', error)
    toast.error('Failed to load reviews.')
  } finally {
    loading.value = false
  }
}

const approveReview = async (review) => {
  try {
    await axiosClient.patch(`/reviews/${review.id}`, { status: true })
    review.status = true
    toast.success('Review approved successfully.')
  } catch (error) {
    console.error('Failed to approve review:', error)
    toast.error('Failed to approve review.')
  }
}

const rejectReview = async (review) => {
  try {
    await axiosClient.patch(`/reviews/${review.id}`, { status: false })
    review.status = false
    toast.success('Review rejected successfully.')
  } catch (error) {
    console.error('Failed to reject review:', error)
    toast.error('Failed to reject review.')
  }
}

const deleteReview = async (review) => {
  if (!confirm('Are you sure you want to delete this review?')) {
    return
  }

  try {
    await axiosClient.delete(`/reviews/${review.id}`)
    reviews.value = reviews.value.filter(r => r.id !== review.id)
    toast.success('Review deleted successfully.')
  } catch (error) {
    console.error('Failed to delete review:', error)
    toast.error('Failed to delete review.')
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

onMounted(loadReviews)
</script>
