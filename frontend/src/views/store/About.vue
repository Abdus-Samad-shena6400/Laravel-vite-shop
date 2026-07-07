
<template>
  <div class="bg-gray-50 min-h-screen">

    <PublicHeader />

    <!-- Hero -->
    <section class="bg-indigo-600 text-white">
      <div class="max-w-7xl mx-auto px-6 py-20 text-center">
        <h1 class="text-5xl font-bold mb-4">
          About Our Store
        </h1>

        <p class="max-w-3xl mx-auto text-lg text-indigo-100">
          We are passionate about delivering high-quality products,
          affordable prices, and an exceptional shopping experience
          for customers around the world.
        </p>
      </div>
    </section>

    <!-- Our Story -->
    <section class="max-w-7xl mx-auto px-6 py-20">

      <div class="grid md:grid-cols-2 gap-12 items-center">

        <div>
          <img
            src="https://images.unsplash.com/photo-1556740749-887f6717d7e4?w=900"
            class="rounded-2xl shadow-xl w-full object-cover"
          />
        </div>

        <div>

          <h2 class="text-3xl font-bold mb-6">
            Our Story
          </h2>

          <p class="text-gray-600 leading-8 mb-4">
            Our journey began with a simple mission: to make online shopping
            easy, affordable, and trustworthy.
          </p>

          <p class="text-gray-600 leading-8 mb-4">
            Today, we proudly serve thousands of customers by offering
            premium products from trusted brands with secure payments,
            fast delivery, and dedicated customer support.
          </p>

          <p class="text-gray-600 leading-8">
            Every order is handled with care because customer satisfaction
            is our highest priority.
          </p>

        </div>

      </div>

    </section>

    <!-- Why Choose Us -->

    <section class="bg-white py-20">

      <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-3xl font-bold text-center mb-14">
          Why Choose Us
        </h2>

        <div class="grid md:grid-cols-4 gap-8">

          <div class="bg-gray-50 rounded-2xl p-8 text-center shadow">

            <div class="text-5xl mb-5">🚚</div>

            <h3 class="font-bold text-xl mb-3">
              Fast Delivery
            </h3>

            <p class="text-gray-600">
              Quick and reliable shipping for every order.
            </p>

          </div>

          <div class="bg-gray-50 rounded-2xl p-8 text-center shadow">

            <div class="text-5xl mb-5">🔒</div>

            <h3 class="font-bold text-xl mb-3">
              Secure Payments
            </h3>

            <p class="text-gray-600">
              Safe and encrypted payment methods.
            </p>

          </div>

          <div class="bg-gray-50 rounded-2xl p-8 text-center shadow">

            <div class="text-5xl mb-5">⭐</div>

            <h3 class="font-bold text-xl mb-3">
              Premium Quality
            </h3>

            <p class="text-gray-600">
              Carefully selected products from trusted brands.
            </p>

          </div>

          <div class="bg-gray-50 rounded-2xl p-8 text-center shadow">

            <div class="text-5xl mb-5">💬</div>

            <h3 class="font-bold text-xl mb-3">
              24/7 Support
            </h3>

            <p class="text-gray-600">
              Friendly customer service whenever you need us.
            </p>

          </div>

        </div>

      </div>

    </section>

    <!-- Statistics -->

    <section class="py-20 bg-indigo-600 text-white">

      <div class="max-w-7xl mx-auto px-6">

        <div class="grid md:grid-cols-4 gap-8 text-center">

          <div>

            <h2 class="text-5xl font-bold">
              10K+
            </h2>

            <p class="mt-3">
              Happy Customers
            </p>

          </div>

          <div>

            <h2 class="text-5xl font-bold">
              5000+
            </h2>

            <p class="mt-3">
              Products
            </p>

          </div>

          <div>

            <h2 class="text-5xl font-bold">
              50+
            </h2>

            <p class="mt-3">
              Trusted Brands
            </p>

          </div>

          <div>

            <h2 class="text-5xl font-bold">
              99%
            </h2>

            <p class="mt-3">
              Customer Satisfaction
            </p>

          </div>

        </div>

      </div>

    </section>

    <!-- Team -->

    <section class="max-w-7xl mx-auto px-6 py-20">

      <h2 class="text-3xl font-bold text-center mb-14">
        Meet Our Team
      </h2>

      <div class="grid md:grid-cols-3 gap-10">

        <div class="bg-white rounded-2xl shadow text-center p-8">

          <img
            src="https://randomuser.me/api/portraits/men/32.jpg"
            class="w-32 h-32 rounded-full mx-auto mb-5"
          >

          <h3 class="font-bold text-xl">
            John Smith
          </h3>

          <p class="text-indigo-600">
            Founder & CEO
          </p>

        </div>

        <div class="bg-white rounded-2xl shadow text-center p-8">

          <img
            src="https://randomuser.me/api/portraits/women/44.jpg"
            class="w-32 h-32 rounded-full mx-auto mb-5"
          >

          <h3 class="font-bold text-xl">
            Sarah Johnson
          </h3>

          <p class="text-indigo-600">
            Marketing Manager
          </p>

        </div>

        <div class="bg-white rounded-2xl shadow text-center p-8">

          <img
            src="https://randomuser.me/api/portraits/men/51.jpg"
            class="w-32 h-32 rounded-full mx-auto mb-5"
          >

          <h3 class="font-bold text-xl">
            David Wilson
          </h3>

          <p class="text-indigo-600">
            Customer Support
          </p>

        </div>

      </div>

    </section>

    <!-- CTA -->

    <section class="bg-indigo-700 text-white">

      <div class="max-w-5xl mx-auto px-6 py-20 text-center">

        <h2 class="text-4xl font-bold mb-6">
          Ready to Start Shopping?
        </h2>

        <p class="text-indigo-100 mb-10">
          Discover thousands of amazing products at unbeatable prices.
        </p>

        <router-link
          to="/shop"
          class="inline-block bg-white text-indigo-700 px-8 py-4 rounded-xl font-bold hover:bg-gray-100 transition"
        >
          Shop Now
        </router-link>

      </div>

    </section>

  </div>
</template>

<script setup>
import PublicHeader from '../../components/admin/PublicHeader.vue'
</script>
