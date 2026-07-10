<script setup>
import { onMounted } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

onMounted(() => {
  if (store.getters.userToken) {
    store.dispatch('loadCart')
    store.dispatch('loadWishlist')
  }
})
</script>

<template>
  
  <router-view></router-view> 
</template>
