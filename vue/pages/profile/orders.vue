<template>
  <OrderTable :orders="data" />
</template>

<script lang="ts" setup>
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
    '/profile/orders'
  );
  if (!response) return;
  data.value = response.orders as Order[];
});
</script>

<style scoped></style>
