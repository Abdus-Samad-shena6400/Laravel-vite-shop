<template>
  <div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center gap-4 mb-6">
      <StarRating :rating="averageRating" size="lg" />
      <div>
        <div class="text-2xl font-bold">
          {{ averageRating.toFixed(1) }}
        </div>
        <div class="text-gray-500 text-sm">
          {{ totalReviews }} {{ totalReviews === 1 ? 'Review' : 'Reviews' }}
        </div>
      </div>
    </div>

    <div class="space-y-2">
      <div
        v-for="count in 5"
        :key="count"
        class="flex items-center gap-3"
      >
        <div class="flex items-center w-20">
          <span class="text-yellow-400">
            {{ '★'.repeat(6 - count) }}{{ '☆'.repeat(count - 1) }}
          </span>
        </div>
        <div class="flex-1 bg-gray-200 rounded-full h-3">
          <div
            class="bg-yellow-400 h-3 rounded-full"
            :style="{ width: `${getPercentage(6 - count)}%` }"
          ></div>
        </div>
        <div class="w-10 text-right text-sm text-gray-600">
          {{ ratingBreakdown[6 - count] || 0 }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import StarRating from './StarRating.vue'

const props = defineProps({
  reviews: {
    type: Array,
    default: () => []
  }
})

const totalReviews = computed(() => props.reviews.length)

const averageRating = computed(() => {
  if (totalReviews.value === 0) return 0
  const sum = props.reviews.reduce((acc, review) => acc + review.rating, 0)
  return sum / totalReviews.value
})

const ratingBreakdown = computed(() => {
  const breakdown = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 }
  props.reviews.forEach(review => {
    breakdown[review.rating] = (breakdown[review.rating] || 0) + 1
  })
  return breakdown
})

const getPercentage = (rating) => {
  if (totalReviews.value === 0) return 0
  const count = ratingBreakdown.value[rating] || 0
  return (count / totalReviews.value) * 100
}
</script>
