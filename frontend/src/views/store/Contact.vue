<template>
  <div class="bg-gray-50 min-h-screen">

    <PublicHeader />

    <div class="max-w-4xl mx-auto py-12 px-6">

      <div class="bg-white rounded-2xl shadow-lg p-8">

        <h1 class="text-3xl font-bold mb-2">
          Contact Us
        </h1>

        <p class="text-gray-500 mb-8">
          We'd love to hear from you. Send us a message.
        </p>

        <form @submit.prevent="submitForm">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
              <label class="block mb-2 font-medium">
                Name
              </label>

              <input v-model="form.name" class="w-full border rounded-lg px-4 py-3" type="text">
            </div>

            <div>
              <label class="block mb-2 font-medium">
                Email
              </label>

              <input v-model="form.email" class="w-full border rounded-lg px-4 py-3" type="email">
            </div>

          </div>

          <div class="mt-6">

            <label class="block mb-2 font-medium">
              Subject
            </label>

            <input v-model="form.subject" class="w-full border rounded-lg px-4 py-3" type="text">

          </div>

          <div class="mt-6">

            <label class="block mb-2 font-medium">
              Message
            </label>

            <textarea v-model="form.message" rows="6" class="w-full border rounded-lg px-4 py-3 resize-none"></textarea>

          </div>

          <button :disabled="loading" class="mt-8 bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg">
            {{ loading ? 'Sending...' : 'Send Message' }}
          </button>

        </form>

      </div>

    </div>
    <transition enter-active-class="transition duration-300" leave-active-class="transition duration-300"
      enter-from-class="opacity-0 translate-y-4" leave-to-class="opacity-0 translate-y-4">
      <div v-if="toast.show" class="fixed top-5 right-5 z-50">
        <div class="px-6 py-4 rounded-lg shadow-xl text-white" :class="toast.type === 'success'
          ? 'bg-green-600'
          : 'bg-red-600'">
          {{ toast.message }}
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import axiosClient from '../../axios'
import PublicHeader from '../../components/admin/PublicHeader.vue'

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

const loading = ref(false)

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const submitForm = async () => {

  loading.value = true

  try {

    await axiosClient.post('/store/contact', form)

    showToast('Message sent successfully.')

   form.value = {
    name: '',
    email: '',
    subject: '',
    message: ''
}

  } catch (error) {

    
showToast('Failed to send message.', 'error')

  } finally {

    loading.value = false

  }

}
</script>