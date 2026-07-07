<template>

<section class="bg-gray-50 py-20">

    <div class="max-w-7xl mx-auto px-4">

        <div class="text-center mb-12">

            <h2 class="text-3xl font-bold text-gray-900">

                Why Shop With Us?

            </h2>

            <p class="text-gray-500 mt-3">

                We provide the best shopping experience with premium quality and reliable service.

            </p>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            <div
                v-for="item in features"
                :key="item.title"
                class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-xl transition duration-300"
            >

                <div
                    :class="item.color"
                    class="w-16 h-16 rounded-full flex items-center justify-center mx-auto"
                >

                    <component
                        :is="item.icon"
                        class="w-8 h-8 text-white"
                    />

                </div>

                <h3 class="mt-6 text-xl font-bold">

                    {{ item.title }}

                </h3>

                <p class="text-gray-500 mt-3">

                    {{ item.description }}

                </p>

            </div>

        </div>

    </div>

</section>

</template>

<script setup>

import {

    TruckIcon,
    ShieldCheckIcon,
    StarIcon,
    ChatAlt2Icon

} from '@heroicons/vue/outline'

const features = [

    {

        title:'Free Shipping',

        description:'Enjoy free delivery on all eligible orders.',

        icon:TruckIcon,

        color:'bg-indigo-600'

    },

    {

        title:'Secure Payment',

        description:'100% safe and encrypted online payments.',

        icon:ShieldCheckIcon,

        color:'bg-green-600'

    },

    {

        title:'Premium Quality',

        description:'Only high-quality products from trusted brands.',

        icon:StarIcon,

        color:'bg-yellow-500'

    },

    {

        title:'24/7 Support',

        description:'Our team is always here to help you anytime.',

        icon:ChatAlt2Icon,

        color:'bg-pink-600'

    }

]

</script>