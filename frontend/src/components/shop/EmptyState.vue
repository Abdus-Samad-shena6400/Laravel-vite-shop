<template>

    <div class="text-center py-20">

        <h2 class="text-2xl font-bold text-gray-700">

            {{ title }}

        </h2>

        <p class="text-gray-500 mt-3">

            {{ message }}

        </p>

    </div>

</template>

<script setup>

defineProps({

    title:{
        type:String,
        default:'Nothing Found'
    },

    message:{
        type:String,
        default:'No data available.'
    }

})

</script>