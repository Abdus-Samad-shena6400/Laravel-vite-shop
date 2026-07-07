<template>
    <div class="p-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold">
                Contact Messages
            </h1>

            <input v-model="search" @input="getContacts()" type="text" placeholder="Search..."
                class="border rounded-lg px-4 py-2 w-72">

        </div>

        <!-- Table -->

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="p-4 text-left">
                            Name
                        </th>

                        <th class="p-4 text-left">
                            Email
                        </th>

                        <th class="p-4 text-left">
                            Subject
                        </th>

                        <th class="p-4 text-center">
                            Status
                        </th>

                        <th class="p-4 text-left">
                            Date
                        </th>

                        <th class="p-4 text-center">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr v-for="contact in contacts" :key="contact.id" class="border-t hover:bg-gray-50">

                        <td class="p-4 font-medium">

                            {{ contact.name }}

                        </td>

                        <td class="p-4">

                            {{ contact.email }}

                        </td>

                        <td class="p-4">

                            {{ contact.subject }}

                        </td>

                        <td class="p-4 text-center">

                            <span v-if="!contact.is_read"
                                class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold">
                                Unread
                            </span>

                            <span v-else
                                class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                Read
                            </span>

                        </td>

                        <td class="p-4">

                            {{ formatDate(contact.created_at) }}

                        </td>

                        <td class="p-4">

                            <div class="flex justify-center gap-2">

                                <router-link :to="`/contacts/${contact.id}`"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                                    View
                                </router-link>

                                <button @click="confirmDelete(contact)"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                    Delete
                                </button>

                            </div>

                        </td>

                    </tr>

                    <tr v-if="contacts.length === 0">

                        <td colspan="6" class="text-center py-8 text-gray-500">
                            No messages found.
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <!-- Pagination -->

        <div class="flex justify-end gap-2 mt-6">

            <button @click="getContacts(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
                class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
                Previous
            </button>

            <button @click="getContacts(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
                Next
            </button>

        </div>
      <!-- Delete Modal -->
<div
    v-if="showDeleteModal"
    class="fixed inset-0 bg-black/50 flex justify-center items-center z-50"
>
    <div class="bg-white rounded-xl shadow-xl w-[420px] p-6">

        <h2 class="text-2xl font-bold mb-4">
            Delete Message
        </h2>

        <p class="text-gray-600">
            Are you sure you want to delete the message from
            <span class="font-semibold">
                {{ selectedContact.name }}
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
                @click="deleteContact"
                :disabled="deleting"
                class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-5 py-2 rounded"
            >
                {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>

        </div>

    </div>
</div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axiosClient from '../../axios'

const contacts = ref([])
const pagination = ref({})
const search = ref('')
const showDeleteModal = ref(false)
const selectedContact = ref({})
const deleting = ref(false)

const getContacts = async (page = 1) => {

    try {

        const { data } = await axiosClient.get('/contacts', {

            params: {

                page,

                search: search.value

            }

        })

        contacts.value = data.data
        pagination.value = data

    } catch (error) {

        console.error(error)

    }

}

const confirmDelete = (contact) => {
    selectedContact.value = contact
    showDeleteModal.value = true
}

const deleteContact = async () => {

    deleting.value = true

    try {

        await axiosClient.delete(`/contacts/${selectedContact.value.id}`)

        showDeleteModal.value = false

        getContacts(pagination.value.current_page)

    } catch (error) {

        console.error(error)

    } finally {

        deleting.value = false

    }

}

const formatDate = (date) => {

    return new Date(date).toLocaleDateString()

}

onMounted(() => {

    getContacts()

})
</script>