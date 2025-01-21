<template>
  <UApp>
    <RouterView />
  </UApp>
</template>

<script setup lang="ts">
import { RouterView } from 'vue-router'
console.log(import.meta.env.VITE_APP)
</script>

<style scoped></style>
