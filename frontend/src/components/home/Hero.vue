<template>
    <section
        class="relative bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 text-white overflow-hidden py-24 px-6 sm:px-12 lg:px-24"
    >
        <!-- Background Effect -->
        <div
            class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-indigo-300 via-transparent to-transparent"
        ></div>

        <div class="relative max-w-4xl mx-auto text-center">

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-tight">

                Your Ultimate Shopping Destination

            </h1>

            <p class="mt-6 text-lg sm:text-xl text-indigo-100 max-w-2xl mx-auto">

                Discover premium brands, top-quality products, and exclusive offers.
                Shop confidently with fast delivery and secure payments.

            </p>

            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">

                <router-link
                    :to="{ name: 'shop' }"
                    class="px-8 py-3 rounded-xl bg-white text-indigo-700 font-semibold hover:bg-indigo-100 transition shadow-lg"
                >
                    Shop Now
                </router-link>

                <a
                    href="#featured-products"
                    class="px-8 py-3 rounded-xl border border-white hover:bg-white hover:text-indigo-700 transition"
                >
                    Explore Products
                </a>

            </div>

        </div>
    </section>
</template>

<script setup>
</script>