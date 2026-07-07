<template>
    <div class="p-6">

        <div v-if="loading" class="text-center py-20">

            Loading...

        </div>

        <div v-else class="bg-white rounded-xl shadow p-8 max-w-4xl mx-auto">

            <div class="flex justify-between items-start mb-8">

                <div>

                    <h1 class="text-3xl font-bold">

                        Contact Message

                    </h1>

                    <p class="text-gray-500 mt-2">

                        Received
                        {{ formatDate(contact.created_at) }}

                    </p>

                </div>

                <span class="px-4 py-2 rounded-full text-sm font-semibold" :class="contact.is_read
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700'">

                    {{ contact.is_read ? 'Read' : 'Unread' }}

                </span>

            </div>

            <div class="space-y-6">

                <div>

                    <label class="font-semibold text-gray-700">

                        Name

                    </label>

                    <p class="mt-1">

                        {{ contact.name }}

                    </p>

                </div>

                <div>

                    <label class="font-semibold text-gray-700">

                        Email

                    </label>

                    <p class="mt-1">

                        {{ contact.email }}

                    </p>

                </div>

                <div>

                    <label class="font-semibold text-gray-700">

                        Subject

                    </label>

                    <p class="mt-1">

                        {{ contact.subject }}

                    </p>

                </div>

                <div>

                    <label class="font-semibold text-gray-700">

                        Message

                    </label>

                    <div class="mt-2 p-5 bg-gray-50 rounded-lg whitespace-pre-line">

                        {{ contact.message }}

                    </div>

                </div>

            </div>

            <div class="flex gap-4 mt-10">

                <a :href="`mailto:${contact.email}?subject=Re: ${contact.subject}`"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg">

                    Reply

                </a>
                <button @click="confirmDelete" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg">
                    Delete
                </button>
            </div>

        </div>

    </div>
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex justify-center items-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-[420px] p-6">

            <h2 class="text-2xl font-bold mb-4">
                Delete Message
            </h2>

            <p class="text-gray-600">
                Are you sure you want to delete this message from
                <span class="font-semibold">
                    {{ contact.name }}
                </span>
                ?
            </p>

            <div class="flex justify-end gap-3 mt-8">

                <button @click="showDeleteModal = false" class="px-5 py-2 rounded border">
                    Cancel
                </button>

                <button @click="deleteMessage" :disabled="deleting"
                    class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-5 py-2 rounded">
                    {{ deleting ? 'Deleting...' : 'Delete' }}
                </button>

            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axiosClient from '../../axios'

const route = useRoute()

const router = useRouter()

const showDeleteModal = ref(false)
const deleting = ref(false)

const loading = ref(true)

const contact = ref({})

const loadContact = async () => {

    const { data } = await axiosClient.get(`/contacts/${route.params.id}`)

    contact.value = data

    loading.value = false

}

const confirmDelete = () => {
    showDeleteModal.value = true
}

const deleteMessage = async () => {
    deleting.value = true

    try {
        await axiosClient.delete(`/contacts/${contact.value.id}`)

        router.push({ name: 'contacts' })

    } catch (error) {
        console.error(error)

    } finally {
        deleting.value = false
        showDeleteModal.value = false
    }
}

const formatDate = (date) => {

    return new Date(date).toLocaleString()

}

onMounted(loadContact)

</script>