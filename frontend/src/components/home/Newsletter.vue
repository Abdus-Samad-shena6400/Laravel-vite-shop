<template>
    <section class="bg-indigo-700 py-20">

        <div class="max-w-4xl mx-auto text-center px-6">

            <h2 class="text-4xl font-bold text-white">

                Subscribe to Our Newsletter

            </h2>

            <p class="text-indigo-100 mt-4">

                Get exclusive offers, new arrivals, and special discounts directly in your inbox.

            </p>

            <form
                @submit.prevent="subscribe"
                class="mt-10 flex flex-col sm:flex-row gap-4 justify-center"
            >

                <input
                    v-model="email"
                    type="email"
                    placeholder="Enter your email address"
                    class="flex-1 max-w-xl px-5 py-4 rounded-xl outline-none text-gray-800"
                    required
                >

                <button
                    class="bg-white text-indigo-700 font-semibold px-8 py-4 rounded-xl hover:bg-indigo-100 transition"
                >

                    Subscribe

                </button>

            </form>

            <p
                v-if="message"
                class="text-green-200 mt-6"
            >

                {{ message }}

            </p>

        </div>

    </section>
</template>

<script setup>

import { ref } from 'vue'

const email = ref('')

const message = ref('')

const subscribe = () => {

    message.value = 'Thank you for subscribing!'

    email.value = ''

    setTimeout(() => {

        message.value = ''

    },3000)

}

</script>