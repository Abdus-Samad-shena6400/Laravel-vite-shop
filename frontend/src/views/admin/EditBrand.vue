<template>
    <div class="p-6">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Edit Brand
            </h1>

            <p class="text-gray-500 mt-1">
                Edit a brand for your store.
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div v-if="loading" class="bg-white rounded-xl shadow p-20 text-center">

                <svg class="animate-spin h-10 w-10 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">

                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />

                </svg>

                <p class="mt-4 text-gray-500">
                    Loading Brand...
                </p>

            </div>
            <div v-else>
                <form @submit.prevent="saveBrand">

                    <!-- Brand Name -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2">
                            Brand Name
                        </label>

                        <input type="text" v-model="form.name"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none"
                            placeholder="Enter brand name">
                    </div>

                    <!-- Image -->
                    <div class="mb-6">

                        <label class="block text-sm font-semibold mb-2">
                            Brand Image
                        </label>

                        <input type="file" @change="onImageChange" accept="image/*"
                            class="w-full border rounded-lg p-3">

                        <div v-if="preview" class="mt-4">
                            <img :src="preview" class="w-40 h-40 object-cover rounded-lg border">
                        </div>

                    </div>

                    <!-- Status -->
                    <div class="mb-8">

                        <label class="block text-sm font-semibold mb-3">
                            Status
                        </label>

                        <label class="mr-6">

                            <input type="radio" v-model="form.status" :value="1">

                            Active

                        </label>

                        <label>

                            <input type="radio" v-model="form.status" :value="0">

                            Inactive

                        </label>

                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3">

                        <router-link :to="{ name: 'brands' }" class="border px-5 py-2 rounded-lg hover:bg-gray-100">
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

    {{ submitting ? 'Updating...' : 'Update Brand' }}

</button>

                    </div>

                </form>
            </div>

        </div>

    </div>
    <transition enter-active-class="transition duration-300" leave-active-class="transition duration-300"
        enter-from-class="opacity-0 translate-y-4" leave-to-class="opacity-0 translate-y-4">

        <div v-if="toast.show" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50">

            <div class="px-6 py-4 rounded-lg shadow-xl text-white" :class="toast.type === 'success'
                ? 'bg-green-600'
                : 'bg-red-600'">

                {{ toast.message }}

            </div>

        </div>

    </transition>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axiosClient from '../../axios'

const router = useRouter()
const route = useRoute()
const loading = ref(false)

const toast = ref({
    show: false,
    message: '',
    type: 'success'
})

const showToast = (message, type = 'success') => {

    toast.value.message = message
    toast.value.type = type
    toast.value.show = true

    setTimeout(() => {
        toast.value.show = false
    }, 3000)

}

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




const submitting = ref(false)

const errors = ref({})




const loadBrand = async () => {

    loading.value = true

    try {

        const { data } = await axiosClient.get(`/brands/${route.params.id}`)

        form.value.name = data.name
        form.value.status = data.status

        preview.value = data.image
            ? `http://localhost:8000/storage/${data.image}`
            : null

    } catch (error) {

        showToast('Failed to load brand.', 'error')

    } finally {

        loading.value = false

    }

}


const saveBrand = async () => {

    submitting.value = true
    errors.value = {}

    try {

        const data = new FormData()

        data.append('name', form.value.name)
        data.append('status', form.value.status)

        // Tell Laravel this is a PUT request
        data.append('_method', 'PUT')

        if (form.value.image instanceof File) {
            data.append('image', form.value.image)
        }

        await axiosClient.post(
            `/brands/${route.params.id}`,
            data
        )

        showToast('Brand updated successfully.')

        setTimeout(() => {
            router.push({
                name: 'brands'
            })
        }, 1200)

    } catch (error) {

        console.log(error.response)

        if (error.response?.status === 422) {
            errors.value = error.response.data.errors
        } else {
            showToast('Something went wrong.', 'error')
        }

    } finally {

        submitting.value = false

    }

}



onMounted(() => {
    loadBrand()
})
</script>