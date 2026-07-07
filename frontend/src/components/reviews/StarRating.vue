<template>
  <div class="flex items-center" :class="sizeClass">
    <span
      v-for="star in 5"
      :key="star"
      class="star"
      :class="star <= rating ? 'text-yellow-400' : 'text-gray-300'"
    >
      {{ star <= rating ? '★' : '☆' }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  rating: {
    type: Number,
    required: true,
    default: 0
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  }
})

const sizeClass = computed(() => {
  const sizes = {
    sm: 'text-sm',
    md: 'text-xl',
    lg: 'text-3xl'
  }
  return sizes[props.size] || sizes.md
})
</script>
