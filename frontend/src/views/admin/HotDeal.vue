<template>
    <div class="p-4 sm:p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Hot Deals</h1>
                <p class="text-gray-500 mt-1">Manage your hot deal products.</p>
            </div>

            <router-link
                :to="{ name: 'hot-deal.create' }"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-lg font-medium text-center"
            >
                + Add Hot Deal
            </router-link>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <input
                type="text"
                placeholder="Search hot deals..."
                v-model="search"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none"
            />
        </div>

        <!-- Table -->
        <div v-if="loading" class="bg-white rounded-xl shadow p-20 text-center">
            <svg
                class="animate-spin h-10 w-10 mx-auto text-indigo-600"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
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

            <p class="mt-4 text-gray-500">Loading Hot Deals...</p>
        </div>

        <div v-else class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full min-w-[920px]">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">Product</th>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Deal Price</th>
                        <th class="px-6 py-3 text-left">Discount</th>
                        <th class="px-6 py-3 text-left">Created</th>
                        <th class="px-6 py-3 text-left">End Time</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="hotDeal in hotDeals"
                        :key="hotDeal.id"
                        class="border-t hover:bg-gray-50"
                    >
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="hotDeal.product?.image_url"
                                    :src="hotDeal.product.image_url"
                                    class="w-12 h-12 rounded object-cover"
                                />
                                <span>{{
                                    hotDeal.product?.name || "N/A"
                                }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            {{ hotDeal.title || "—" }}
                        </td>

                        <td class="px-6 py-4">
                            {{ hotDeal.deal_price || "—" }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm"
                            >
                                {{ hotDeal.discount_percentage }}% OFF
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ formatDate(hotDeal.created_at) }}
                        </td>

                        <td class="px-6 py-4">
                            {{ formatDateTime(hotDeal.end_time) }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                :class="
                                    hotDeal.status
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700'
                                "
                                class="px-3 py-1 rounded-full text-sm"
                            >
                                {{ hotDeal.status ? "Active" : "Inactive" }}
                            </span>
                        </td>

                        <td class="px-6 py-4 bg-gray-50">
                            <div class="flex flex-col sm:flex-row justify-center gap-2">
                                <router-link
                                    :to="{
                                        name: 'hot-deal.edit',
                                        params: { id: hotDeal.id },
                                    }"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm text-center"
                                >
                                    Edit
                                </router-link>

                                <button
                                    @click="confirmDelete(hotDeal)"
                                    :disabled="deleting"
                                    class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-5 py-2 rounded text-sm"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tr v-if="!hotDeals.length">
                    <td colspan="6" class="text-center py-10 text-gray-500">
                        No hot deals found.
                    </td>
                </tr>
            </table>
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="!loading && hotDeals.length > 0"
            class="bg-white rounded-xl shadow p-4 mt-6"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-gray-600">
                    Showing {{ (currentPage - 1) * 10 + 1 }} to
                    {{ Math.min(currentPage * 10, total) }} of {{ total }}
                    hot deals
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="
                            if (currentPage > 1) {
                                currentPage--;
                                getHotDeals();
                            }
                        "
                        :disabled="currentPage === 1"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Previous
                    </button>
                    <button
                        @click="
                            currentPage++;
                            getHotDeals();
                        "
                        :disabled="currentPage === lastPage"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
        <!-- Delete Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 bg-black/50 flex justify-center items-center z-50"
        >
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
                <h2 class="text-2xl font-bold mb-4">Delete Hot Deal</h2>
                <p class="text-gray-600">
                    Are you sure you want to delete this hot deal for
                    <span class="font-semibold">
                        {{ selectedHotDeal.product?.name }}
                    </span>
                    ?
                </p>
                <div class="flex justify-end gap-3 mt-8">
                    <button
                        @click="showDeleteModal = false"
                        class="px-5 py-2 rounded border"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteHotDeal()"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <transition
            enter-active-class="transition duration-300"
            leave-active-class="transition duration-300"
            enter-from-class="opacity-0 translate-y-4"
            leave-to-class="opacity-0 translate-y-4"
        >
            <div v-if="toast.show" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50">
                <div
                    class="px-6 py-4 rounded-lg shadow-xl text-white"
                    :class="
                        toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'
                    "
                >
                    {{ toast.message }}
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import axiosClient from "../../axios";

const hotDeals = ref([]);
const loading = ref(false);
const search = ref("");
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

const showDeleteModal = ref(false);
const selectedHotDeal = ref(null);

const toast = ref({
    show: false,
    message: "",
    type: "success",
});

const deleting = ref(false);

const formatDateTime = (date) => {
    return new Date(date).toLocaleString();
};

const showToast = (message, type = "success") => {
    toast.value.message = message;
    toast.value.type = type;
    toast.value.show = true;

    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

const getHotDeals = async () => {
    try {
        loading.value = true;
        const { data } = await axiosClient.get("/hot-deals", {
            params: {
                search: search.value,
                page: currentPage.value,
            },
        });

        hotDeals.value = data.data || [];
        currentPage.value = data.current_page;
        lastPage.value = data.last_page;
        total.value = data.total;
        console.log(data);
    } catch (error) {
        showToast("Failed to load hot deals", "error");
    } finally {
        loading.value = false;
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const confirmDelete = (hotDeal) => {
    selectedHotDeal.value = hotDeal;
    showDeleteModal.value = true;
};

const deleteHotDeal = async () => {
    deleting.value = true;

    try {
        await axiosClient.delete(`/hot-deals/${selectedHotDeal.value.id}`);

        showDeleteModal.value = false;

        getHotDeals();

        showToast("Hot deal deleted successfully");
    } catch (error) {
        showToast("Failed to delete hot deal", "error");
    } finally {
        deleting.value = false;
    }
};

// Watch search input to trigger API call
watch(search, () => {
    currentPage.value = 1;
    getHotDeals();
});

onMounted(() => {
    getHotDeals();
});
</script>
