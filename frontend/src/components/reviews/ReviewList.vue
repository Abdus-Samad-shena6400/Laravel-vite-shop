<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">
      Customer Reviews
    </h2>

    <div
      v-if="reviews.length"
      class="space-y-6"
    >
      <div
        v-for="review in reviews"
        :key="review.id"
        class="bg-white rounded-xl shadow p-6"
      >
        <div class="flex justify-between items-center">
          <div>
            <h3 class="font-semibold text-lg">
              {{ review.user.name }}
            </h3>
            <p class="text-gray-500 text-sm">
              {{ formatDate(review.created_at) }}
            </p>
          </div>

          <StarRating :rating="review.rating" size="md" />
        </div>

        <p class="mt-5 text-gray-700 whitespace-pre-line">
          {{ review.comment }}
        </p>
      </div>
    </div>

    <div
      v-else
      class="text-center py-10 text-gray-500"
    >
      No reviews yet.
    </div>
  </div>
</template>

<script setup>
import StarRating from './StarRating.vue'

defineProps({
  reviews: {
    type: Array,
    default: () => []
  }
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>
