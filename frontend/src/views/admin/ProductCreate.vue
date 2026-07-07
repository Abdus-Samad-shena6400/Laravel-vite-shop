<template>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                Add Product
            </h1>

            <p class="text-gray-500 mt-2">
                Create a new product for your store.
            </p>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="bg-white rounded-xl shadow p-20 text-center">
            <svg class="animate-spin h-10 w-10 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
            </svg>

            <p class="mt-4 text-gray-500">
                Loading...
            </p>
        </div>

        <!-- Form -->
        <div v-else class="bg-white rounded-xl shadow p-6">
            <form @submit.prevent="submitForm">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Product Name -->
                    <div class="md:col-span-2">

                        <label class="block font-medium mb-2">
                            Product Name
                        </label>

                        <input v-model="form.name" type="text"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none"
                            placeholder="Enter product name">
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">
                            {{ errors.name[0] }}
                        </p>

                    </div>

                    <!-- Category -->

                    <div>

                        <label class="block font-medium mb-2">
                            Category
                        </label>

                        <select v-model="form.category_id" class="w-full border rounded-lg px-4 py-3">

                            <option value="">
                                Choose Category
                            </option>

                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                            <p v-if="errors.category_id" class="text-red-500 text-sm mt-1">
                                {{ errors.category_id[0] }}
                            </p>
                        </select>


                    </div>

                    <!-- Brand -->

                    <div>

                        <label class="block font-medium mb-2">
                            Brand
                        </label>

                        <select v-model="form.brand_id" class="w-full border rounded-lg px-4 py-3">

                            <option value="">
                                Choose Brand
                            </option>

                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                {{ brand.name }}
                            </option>
                            <p v-if="errors.brand_id" class="text-red-500 text-sm mt-1">
                                {{ errors.brand_id[0] }}
                            </p>
                        </select>


                    </div>

                    <!-- Short Description -->

                    <div class="md:col-span-2">

                        <label class="block font-medium mb-2">
                            Short Description
                        </label>

                        <textarea rows="3" v-model="form.short_description"
                            class="w-full border rounded-lg px-4 py-3" />

                        <p v-if="errors.short_description" class="text-red-500 text-sm mt-1">
                            {{ errors.short_description[0] }}
                        </p>

                    </div>

                    <!-- Description -->

                    <div class="md:col-span-2">

                        <label class="block font-medium mb-2">
                            Description
                        </label>

                        <textarea rows="6" v-model="form.description" class="w-full border rounded-lg px-4 py-3" />
                        <p v-if="errors.description" class="text-red-500 text-sm mt-1">
                            {{ errors.description[0] }}
                        </p>
                    </div>

                </div>
                <div class="md:col-span-2">

                    <label class="block font-medium mb-2">
                        Featured Image
                    </label>

                    <input type="file" accept="image/*" @change="handleFeaturedImage">

                    <img v-if="previewImage" :src="previewImage" class="mt-4 w-40 h-40 object-cover rounded-lg border">

                </div>

                <!-- Gallery Images -->
                <div class="md:col-span-2 mt-6">

                    <label class="block font-medium mb-2">
                        Gallery Images
                    </label>

                    <input type="file" accept="image/*" multiple @change="handleGalleryImages">

                    <div v-if="galleryImages.length > 0" class="mt-4 grid grid-cols-4 gap-4">
                        <div v-for="(image, index) in galleryImages" :key="index" class="relative">
                            <img :src="image.preview" class="w-full h-32 object-cover rounded-lg border">
                            <button type="button" @click="removeGalleryImage(index)"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                ×
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Pricing & Inventory -->

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                    <!-- Regular Price -->

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Regular Price
                        </label>

                        <input type="number" step="0.01" min="0" v-model="form.regular_price"
                            placeholder="Enter regular price"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <p v-if="errors.regular_price" class="text-red-500 text-sm mt-1">
                            {{ errors.regular_price[0] }}
                        </p>
                    </div>

                    <!-- Sale Price -->

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sale Price
                        </label>

                        <input type="number" step="0.01" min="0" v-model="form.sale_price"
                            placeholder="Enter sale price"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <p v-if="errors.sale_price" class="text-red-500 text-sm mt-1">
                            {{ errors.sale_price[0] }}
                        </p>
                    </div>

                    <!-- Quantity -->

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Quantity
                        </label>

                        <input type="number" min="0" v-model="form.quantity" placeholder="Enter quantity"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <p v-if="errors.quantity" class="text-red-500 text-sm mt-1">
                            {{ errors.quantity[0] }}
                        </p>
                    </div>

                    <!-- Status -->

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>

                        <select v-model="form.status" class="w-full border rounded-lg px-4 py-3">

                            <option :value="true">
                                Active
                            </option>

                            <option :value="false">
                                Inactive
                            </option>

                        </select>
                        <p v-if="errors.status" class="text-red-500 text-sm mt-1">
                            {{ errors.status[0] }}
                        </p>

                    </div>

                </div>

                <!-- Featured -->

                <div class="mt-6">

                    <label class="flex items-center gap-3 cursor-pointer">

                        <input type="checkbox" v-model="form.featured" class="w-5 h-5">

                        <span class="font-medium">
                            Featured Product
                        </span>

                    </label>
                    <p v-if="errors.featured" class="text-red-500 text-sm mt-1">
                        {{ errors.featured[0] }}
                    </p>

                </div>

                <!-- Buttons -->

                <div class="flex justify-end gap-3 mt-8">

                    <router-link :to="{ name: 'products' }" class="px-6 py-3 border rounded-lg">
                        Cancel
                    </router-link>

                    <button type="submit" :disabled="submitting"
                        class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white px-6 py-3 rounded-lg">

                        {{ submitting ? 'Saving...' : 'Save Product' }}

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
import { useRouter } from 'vue-router'
import { ref, onMounted } from "vue";
import axiosClient from "../../axios";
const featuredImage = ref(null)
const previewImage = ref(null)
const galleryImages = ref([])

const router = useRouter()
const loading = ref(false);

const categories = ref([]);
const brands = ref([]);

const errors = ref({})
const submitting = ref(false)

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
    category_id: '',
    brand_id: '',
    short_description: '',
    description: '',

    regular_price: '',
    sale_price: '',
    quantity: 0,

    featured: false,
    status: true,
})

const loadData = async () => {
    loading.value = true;

    try {

        const [categoryResponse, brandResponse] = await Promise.all([
            axiosClient.get("/categories"),
            axiosClient.get("/brands"),
        ]);

        categories.value = categoryResponse.data.data.filter(c => c !== null);
        brands.value = brandResponse.data.data.filter(b => b !== null);

    } catch (error) {

        console.log(error);

    } finally {

        loading.value = false;

    }
};

const handleFeaturedImage = (event) => {

    const file = event.target.files[0]

    if (!file) return

    featuredImage.value = file

    previewImage.value = URL.createObjectURL(file)
}

const handleGalleryImages = (event) => {
    const files = Array.from(event.target.files)

    files.forEach(file => {
        galleryImages.value.push({
            file: file,
            preview: URL.createObjectURL(file)
        })
    })

    // Clear the input so the same files can be selected again if needed
    event.target.value = ''
}

const removeGalleryImage = (index) => {
    galleryImages.value.splice(index, 1)
}

const submitForm = async () => {

    errors.value = {}
    submitting.value = true

    try {
        const formData = new FormData()

        Object.keys(form.value).forEach(key => {
            if (typeof form.value[key] === 'boolean') {
                formData.append(key, form.value[key] ? '1' : '0')
            } else {
                formData.append(key, form.value[key])
            }
        })

        if (featuredImage.value) {
            formData.append('featured_image', featuredImage.value)
        }

        galleryImages.value.forEach((image, index) => {
            formData.append(`gallery_images[${index}]`, image.file)
        })

        await axiosClient.post('/products', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        showToast('Product created successfully.')

        setTimeout(() => {
            router.push({
                name: 'products'
            })
        }, 1500)

    } catch (error) {

        if (error.response?.status === 422) {

            errors.value = error.response.data.errors

        } else {

            console.log(error)

            showToast('Something went wrong.', 'error')

        }

    } finally {

        submitting.value = false

    }

}
onMounted(() => {

    loadData();

});
</script>
