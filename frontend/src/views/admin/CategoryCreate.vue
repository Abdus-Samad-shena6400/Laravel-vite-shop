<template>
  <div class="p-6">

    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-800">
        Add Category
      </h1>

      <p class="text-gray-500 mt-1">
        Create a new category for your store.
      </p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-lg p-8">

      <form @submit.prevent="saveCategory">

        <!-- Category Name -->
        <div class="mb-6">
          <label class="block text-sm font-semibold mb-2">
            Category Name
          </label>

          <input
            type="text"
            v-model="form.name"
            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none"
            placeholder="Enter category name"
          >
        </div>

        <!-- Image -->
        <div class="mb-6">

          <label class="block text-sm font-semibold mb-2">
            Category Image
          </label>

          <input
            type="file"
            @change="onImageChange"
            accept="image/*"
            class="w-full border rounded-lg p-3"
          >

          <div
            v-if="preview"
            class="mt-4"
          >
            <img
              :src="preview"
              class="w-40 h-40 object-cover rounded-lg border"
            >
          </div>

        </div>

        <!-- Status -->
        <div class="mb-8">

          <label class="block text-sm font-semibold mb-3">
            Status
          </label>

          <label class="mr-6">

            <input
              type="radio"
              v-model="form.status"
              :value="1"
            >

            Active

          </label>

          <label>

            <input
              type="radio"
              v-model="form.status"
              :value="0"
            >

            Inactive

          </label>

        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3">

          <router-link
            :to="{name:'categories'}"
            class="border px-5 py-2 rounded-lg hover:bg-gray-100"
          >
            Cancel
          </router-link>

         <button
    type="submit"
    :disabled="submitting"
    class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg flex items-center"
>

    <svg
        v-if="submitting"
        class="animate-spin h-5 w-5 mr-2"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24">

        <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
        />

        <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8v8z"
        />

    </svg>

    {{ submitting ? 'Saving...' : 'Save Category' }}

</button>

        </div>

      </form>

    </div>

  </div>
  <transition
    enter-active-class="transition duration-300"
    leave-active-class="transition duration-300"
    enter-from-class="opacity-0 translate-y-4"
    leave-to-class="opacity-0 translate-y-4"
>

    <div
        v-if="toast.show"
        class="fixed top-5 right-5 z-50"
    >

        <div
            class="px-6 py-4 rounded-lg shadow-xl text-white"
            :class="toast.type === 'success'
                ? 'bg-green-600'
                : 'bg-red-600'"
        >

            {{ toast.message }}

        </div>

    </div>

</transition>
</template>


<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axiosClient from '../../axios'

const router = useRouter()

const form = ref({
    name: '',
    image: null,
    status: 1
})

const preview = ref(null)

const onImageChange = (event) => {

    const file = event.target.files[0]

    if (!file) return

    form.value.image = file

    preview.value = URL.createObjectURL(file)

}

const showToast = (message, type = 'success') => {

    toast.value.message = message
    toast.value.type = type
    toast.value.show = true

    setTimeout(() => {
        toast.value.show = false
    }, 3000)

}


const submitting = ref(false)

const errors = ref({})

const toast = ref({
    show: false,
    message: '',
    type: 'success'
})

const saveCategory = async () => {

    submitting.value = true

    errors.value = {}

    try {

        const data = new FormData()

        data.append('name', form.value.name)
        data.append('status', form.value.status)

        if (form.value.image) {
            data.append('image', form.value.image)
        }

        await axiosClient.post('/categories', data)

        showToast('Category created successfully.')

        setTimeout(() => {

            router.push({
                name: 'categories'
            })

        }, 1200)

    }

   catch (error) {

    console.log(JSON.stringify(error.response.data.errors, null, 2))

    if (error.response?.status === 422) {
        errors.value = error.response.data.errors
    } else {
        showToast('Something went wrong.', 'error')
    }

}

    finally {

        submitting.value = false

    }

}
</script>