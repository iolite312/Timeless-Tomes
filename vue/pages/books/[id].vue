<template>
  <div>
    {{ data }}
  </div>
</template>

<script lang="ts" setup>
import axiosClient from '~/axios';

const id = useRoute().params.id;
const { data } = await axiosClient.get(`/books/${id}`);
</script>

<style scoped></style>
