<template>
  <div class="flex flex-col gap-4 w-full">
    <OrderTable v-for="order in orders" :key="order.id" :order="order" />
  </div>
</template>

<script lang="ts" setup>
const orderStore = useOrderStore();
const orders = computed(() => orderStore.orders);
onBeforeMount(() => {
  orderStore.getUserOrders();
});
</script>

<style scoped></style>
