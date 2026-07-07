<template>

    <div
        v-if="links.length > 3"
        class="flex justify-center mt-10"
    >

        <nav class="flex flex-wrap gap-2">

            <button
                v-for="link in links"
                :key="link.label"
                @click="changePage(link)"
                :disabled="!link.url"
                v-html="formatLabel(link.label)"
                class="px-4 py-2 rounded-lg border transition"
                :class="[
                    link.active
                        ? 'bg-indigo-600 text-white border-indigo-600'
                        : 'bg-white hover:bg-gray-100',
                    !link.url
                        ? 'opacity-50 cursor-not-allowed'
                        : ''
                ]"
            />

        </nav>

    </div>

</template>

<script setup>

const props = defineProps({

    links: {

        type: Array,

        default: () => []

    }

})

const emit = defineEmits([

    'change'

])

const changePage = (link) => {

    if (!link.url) return

    emit('change', link.url)

}

const formatLabel = (label) => {

    return label
        .replace('&laquo;', '«')
        .replace('&raquo;', '»')

}

</script>