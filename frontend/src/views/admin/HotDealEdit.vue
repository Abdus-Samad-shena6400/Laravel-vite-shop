<template>
    <div class="p-6">
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Edit Hot Deal</h1>

            <p class="text-gray-500 mt-2">Update your hot deal.</p>
        </div>

        <form @submit.prevent="updateHotDeal" class="bg-white rounded-xl shadow p-8">
            <!-- Product -->
            <div class="mb-6">
                <label class="block font-semibold mb-2"> Product </label>

                <select v-model="form.product_id" required class="w-full border rounded-lg px-4 py-3">
                    <option value="">Select a product</option>
                    <option v-for="product in products" :key="product.id" :value="product.id">
                        {{ product.name }} - ${{ product.regular_price }}
                    </option>
                </select>
            </div>
            <div v-if="selectedProduct" class="mb-6 border rounded-lg p-4 flex items-center gap-4">
                <img :src="selectedProduct.image_url" class="w-20 h-20 rounded-lg object-cover">

                <div>
                    <h3 class="font-bold">
                        {{ selectedProduct.name }}
                    </h3>

                    <p class="text-gray-500">
                        Original Price:
                        ${{ selectedProduct.regular_price }}
                    </p>
                </div>
            </div>

            <!-- Title -->
            <div class="mb-6">
                <label class="block font-semibold mb-2"> Title </label>

                <input v-model="form.title" type="text" required class="w-full border rounded-lg px-4 py-3" />
            </div>

            <!-- Subtitle -->
            <div class="mb-6">
                <label class="block font-semibold mb-2"> Subtitle </label>

                <input v-model="form.subtitle" type="text" class="w-full border rounded-lg px-4 py-3" />
            </div>

            <!-- Discount Percentage -->
            <div class="mb-6">
                <label class="block font-semibold mb-2">
                    Discount Percentage (%)
                </label>

                <input v-model="form.discount_percentage" type="number" step="0.01" min="0" max="100" required
                    class="w-full border rounded-lg px-4 py-3" />
            </div>
            <!-- Start Time -->
            <div class="mb-6">
                <label class="block font-semibold mb-2"> Start Time </label>

                <input v-model="form.start_time" type="datetime-local" required
                    class="w-full border rounded-lg px-4 py-3" />
            </div>

            <!-- End Time -->
            <div class="mb-6">
                <label class="block font-semibold mb-2"> End Time </label>

                <input v-model="form.end_time" type="datetime-local" required
                    class="w-full border rounded-lg px-4 py-3" />
            </div>

            <!-- Status -->
            <div class="mb-8">
                <label class="block font-semibold mb-3"> Status </label>

                <label class="mr-6">
                    <input type="radio" v-model="form.status" :value="true" />

                    Active
                </label>

                <label>
                    <input type="radio" v-model="form.status" :value="false" />

                    Inactive
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <router-link :to="{ name: 'hot-deals' }" class="border px-5 py-2 rounded-lg hover:bg-gray-100">
                    Cancel
                </router-link>

                <button type="submit" :disabled="submitting"
                    class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-lg flex items-center">
                    <svg v-if="submitting" class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>

                    {{ submitting ? "Updating..." : "Update Hot Deal" }}
                </button>
            </div>
        </form>
    </div>

    <transition enter-active-class="transition duration-300" leave-active-class="transition duration-300"
        enter-from-class="opacity-0 translate-y-4" leave-to-class="opacity-0 translate-y-4">
        <div v-if="toast.show" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50">
            <div class="px-6 py-4 rounded-lg shadow-xl text-white" :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'
                ">
                {{ toast.message }}
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axiosClient from "../../axios";

const router = useRouter();
const route = useRoute();

const submitting = ref(false);
const products = ref([]);

const toast = ref({
    show: false,
    message: "",
    type: "success",
});

const showToast = (message, type = "success") => {
    toast.value = {
        show: true,
        message,
        type,
    };

    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const form = ref({
    product_id: "",
    discount_percentage: "",
    title: "",
    subtitle: "",
    start_time: "",
    end_time: "",
    description: "",
    status: true,
});

const loadProducts = async () => {
    try {
        const { data } = await axiosClient.get("/products");
        products.value = data.data || data;
    } catch (error) {
        showToast("Failed to load products", "error");
    }
};

const loadHotDeal = async () => {
    try {
        const { data } = await axiosClient.get(`/hot-deals/${route.params.id}`);

        form.value.product_id = data.product_id;
        form.value.discount_percentage = data.discount_percentage;
        form.value.title = data.title;
        form.value.subtitle = data.subtitle;
        form.value.start_time = data.start_time?.slice(0, 16);
        form.value.end_time = data.end_time?.slice(0, 16);
        form.value.description = data.description;
        form.value.status = data.status;
    } catch (error) {
        showToast("Unable to load hot deal.", "error");
    }
};

const updateHotDeal = async () => {
    if (
        new Date(form.value.end_time) <=
        new Date(form.value.start_time)
    ) {
        showToast(
            "End time must be after start time.",
            "error",
        );

        submitting.value = false;
        return;
    }
    submitting.value = true;

    try {
        await axiosClient.put(`/hot-deals/${route.params.id}`, form.value);

        showToast("Hot deal updated successfully");

        setTimeout(() => {
            router.push({
                name: "hot-deals",
            });
        }, 1000);
    } catch (error) {
        showToast(
            error.response?.data?.message || "Failed to update hot deal.",
            "error",
        );
    } finally {
        submitting.value = false;
    }
};

onMounted(async () => {
    await Promise.all([loadProducts(), loadHotDeal()]);
});
</script>
