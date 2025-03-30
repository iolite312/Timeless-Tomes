<template>
  <OrderTable :orders="data" />
</template>

<script setup lang="ts">
import axiosClient from '~/axios';
import type { Order, OrderResponse } from '~/types';

definePageMeta({
  layout: 'profile',
  middleware: 'auth',
});

const data = ref<Order[]>([]);

onBeforeMount(async () => {
  axiosClient.defaults.headers.common.Authorization = `Bearer ${
    useAccountStore().token
  }`;
  const { data: response } = await axiosClient.get<OrderResponse>(
    '/admin/orders'
  );
  if (!response) return;
  data.value = response.orders as Order[];
});
</script>

<style scoped></style>
