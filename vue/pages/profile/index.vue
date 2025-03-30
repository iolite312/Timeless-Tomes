<template>
  <div>
    <p>Welcome {{ useAccountStore().fullname }}</p>
  </div>
</template>

<script lang="ts" setup>
definePageMeta({
  layout: 'profile',
  middleware: 'auth',
});
</script>

<style scoped></style>
