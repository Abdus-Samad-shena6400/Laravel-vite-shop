<template>
    <div v-if="hotDeals.length > 0"
        class="bg-gradient-to-r from-red-600 to-orange-500 rounded-2xl sm:rounded-3xl overflow-hidden mb-8 sm:mb-10">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-center lg:justify-between mb-4 sm:mb-6">
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white flex items-center gap-2 sm:gap-3">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                            clip-rule="evenodd" />
                    </svg>
                    Hot Deals
                </h2>
                <div class="flex flex-wrap items-center gap-2 text-white">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm sm:text-base lg:text-xl font-bold">Ends in:</span>
                    <div class="flex flex-wrap gap-1 sm:gap-2">
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1.5 sm:px-3 sm:py-2 text-center min-w-[50px] sm:min-w-[58px] lg:min-w-[60px]">
                            <div class="text-base sm:text-lg lg:text-2xl font-bold">{{ countdown.days }}</div>
                            <div class="text-[10px] sm:text-xs uppercase">Days</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1.5 sm:px-3 sm:py-2 text-center min-w-[50px] sm:min-w-[58px] lg:min-w-[60px]">
                            <div class="text-base sm:text-lg lg:text-2xl font-bold">{{ countdown.hours }}</div>
                            <div class="text-[10px] sm:text-xs uppercase">Hours</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1.5 sm:px-3 sm:py-2 text-center min-w-[50px] sm:min-w-[58px] lg:min-w-[60px]">
                            <div class="text-base sm:text-lg lg:text-2xl font-bold">{{ countdown.minutes }}</div>
                            <div class="text-[10px] sm:text-xs uppercase">Mins</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1.5 sm:px-3 sm:py-2 text-center min-w-[50px] sm:min-w-[58px] lg:min-w-[60px]">
                            <div class="text-base sm:text-lg lg:text-2xl font-bold">{{ countdown.seconds }}</div>
                            <div class="text-[10px] sm:text-xs uppercase">Secs</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 z-10 hidden md:flex">
                    <button @click="previousDeal"
                        class="w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-lg flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>
                <div class="absolute right-0 top-1/2 -translate-y-1/2 z-10 hidden md:flex">
                    <button @click="nextDeal"
                        class="w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-lg flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div class="mx-0 sm:mx-8 lg:mx-12" @mouseenter="pauseRotation" @mouseleave="resumeRotation">
                    <transition name="slide" mode="out-in">
                        <router-link v-if="activeDeal" :key="activeDeal.id"
                            :to="`/products/${activeDeal.product?.id || activeDeal.product_id}`"
                            class="block bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                            <div class="grid md:grid-cols-[1.1fr,0.9fr]">
                                <div class="relative h-48 sm:h-56 md:h-64 lg:h-full flex items-center justify-center bg-gray-100">
                                    <img :src="activeDeal.product?.image_url || `http://localhost:8000/storage/${activeDeal.product?.featured_image}`"
                                        :alt="activeDeal.product?.name"
                                        class="max-h-full max-w-full object-contain p-3 sm:p-4" />
                                    <div class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-red-600 text-white px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-bold">
                                        {{ activeDeal.discount_percentage }}% OFF
                                    </div>
                                    <div class="absolute top-2 right-2 sm:top-3 sm:right-3 bg-white text-red-600 px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-bold shadow">
                                        HOT
                                    </div>
                                </div>

                                <div class="p-4 sm:p-6 md:p-8 flex flex-col justify-center">
                                    <div class="text-xs sm:text-sm font-semibold text-indigo-600 mb-2">
                                        {{ activeIndex + 1 }} / {{ hotDeals.length }} deals
                                    </div>
                                    <h3 class="text-base sm:text-xl lg:text-2xl font-bold text-gray-800 mb-2 sm:mb-3 line-clamp-2">
                                        {{ activeDeal.title || activeDeal.product?.name || 'Product Name' }}
                                    </h3>
                                    <p v-if="activeDeal.subtitle" class="text-gray-600 text-xs sm:text-sm mb-3 sm:mb-4 line-clamp-2">
                                        {{ activeDeal.subtitle }}
                                    </p>
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-4 sm:mb-5">
                                        <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-red-600">
                                            ${{ formatPrice(activeDeal.deal_price) }}
                                        </div>
                                        <div v-if="getOriginalPrice(activeDeal)" class="text-gray-400 line-through text-sm sm:text-base">
                                            ${{ formatPrice(getOriginalPrice(activeDeal)) }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 sm:gap-3">
    <div class="text-xs sm:text-sm text-gray-500">
        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                clip-rule="evenodd" />
        </svg>
        Ends: {{ formatDateTime(activeDeal.end_time) }}
    </div>

    <span
        class="inline-flex w-fit bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg font-medium text-xs sm:text-sm transition-colors"
    >
        View Deal
    </span>
</div>
                                </div>
                            </div>
                        </router-link>
                    </transition>
                </div>

                <div v-if="hotDeals.length > 1" class="flex justify-center gap-2 sm:gap-3 mt-4 sm:mt-6">
                    <button v-for="(deal, index) in hotDeals" :key="deal.id" @click="goToDeal(index)"
                        class="transition-all rounded-full"
                        :class="activeIndex === index ? 'bg-white w-6 h-2.5 sm:w-8 sm:h-3' : 'bg-white/50 w-2.5 h-2.5 sm:w-3 sm:h-3'">
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import axiosClient from '../../axios'

const hotDeals = ref([])
const activeIndex = ref(0)

const countdown = ref({
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
})

let countdownInterval = null
let rotateInterval = null

const activeDeal = computed(() => {
    if (!hotDeals.value.length) return null
    return hotDeals.value[activeIndex.value]
})

const formatDateTime = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const formatPrice = (value) => {
    if (value === null || value === undefined || value === '') return ''

    const numericValue = Number(value)
    return Number.isNaN(numericValue) ? value : numericValue.toFixed(2)
}

const getOriginalPrice = (deal) => {
    if (!deal?.product) return null

    return deal.product.regular_price ?? null
}

const startCountdown = (endTime) => {
    if (countdownInterval) {
        clearInterval(countdownInterval)
    }

    const updateCountdown = () => {
        const now = Date.now()
        const end = new Date(endTime).getTime()
        const distance = end - now

        if (distance < 0) {
            countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
            clearInterval(countdownInterval)
            countdownInterval = null
            return
        }

        countdown.value = {
            days: Math.floor(distance / (1000 * 60 * 60 * 24)),
            hours: Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
            minutes: Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
            seconds: Math.floor((distance % (1000 * 60)) / 1000),
        }
    }

    updateCountdown()
    countdownInterval = setInterval(updateCountdown, 1000)
}

const stopRotation = () => {
    if (rotateInterval) {
        clearInterval(rotateInterval)
        rotateInterval = null
    }
}

const startRotation = () => {
    stopRotation()

    if (hotDeals.value.length <= 1) return

    rotateInterval = setInterval(() => {
        activeIndex.value = (activeIndex.value + 1) % hotDeals.value.length
        if (activeDeal.value?.end_time) {
            startCountdown(activeDeal.value.end_time)
        }
    }, 4000)
}

const pauseRotation = () => {
    stopRotation()
}

const resumeRotation = () => {
    if (hotDeals.value.length > 1) {
        startRotation()
    }
}

const nextDeal = () => {
    if (!hotDeals.value.length) return
    activeIndex.value = (activeIndex.value + 1) % hotDeals.value.length
    if (activeDeal.value?.end_time) {
        startCountdown(activeDeal.value.end_time)
    }
}

const previousDeal = () => {
    if (!hotDeals.value.length) return
    activeIndex.value = (activeIndex.value - 1 + hotDeals.value.length) % hotDeals.value.length
    if (activeDeal.value?.end_time) {
        startCountdown(activeDeal.value.end_time)
    }
}

const goToDeal = (index) => {
    if (!hotDeals.value.length) return
    activeIndex.value = index
    if (activeDeal.value?.end_time) {
        startCountdown(activeDeal.value.end_time)
    }
}

const fetchHotDeals = async () => {
    try {
        const { data } = await axiosClient.get('/store/hot-deals')
        const deals = Array.isArray(data) ? data : (data?.data ?? [])

        hotDeals.value = deals
            .filter(Boolean)
            .sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))

        activeIndex.value = 0

        if (hotDeals.value.length) {
            const firstDeal = hotDeals.value[0]
            if (firstDeal?.end_time) {
                startCountdown(firstDeal.end_time)
            }
            startRotation()
        } else {
            if (countdownInterval) clearInterval(countdownInterval)
            countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
        }
    } catch (error) {
        console.error('Failed to fetch hot deals:', error)
        hotDeals.value = []
        if (countdownInterval) clearInterval(countdownInterval)
        countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
    }
}

onMounted(() => {
    fetchHotDeals()
})

onUnmounted(() => {
    if (countdownInterval) clearInterval(countdownInterval)
    stopRotation()
})
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.slide-enter-active,
.slide-leave-active {
    transition: all 0.4s ease;
}

.slide-enter-from {
    opacity: 0;
    transform: translateX(20px);
}

.slide-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}
</style>
