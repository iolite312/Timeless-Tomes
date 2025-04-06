<template>
  <div class="flex flex-col gap-4 w-full">
    <h1 class="text-2xl font-bold">Requests</h1>
    <div v-for="seller in sellers" :key="seller.id">
      <p>{{ seller.seller_name }} - {{ seller.email }}</p>
      <UButton @click="accept(seller.seller_id as number)">Accept</UButton>
    </div>
  </div>
</template>

<script setup lang="ts">
import axiosClient from '~/axios';
import type { Account, AccountResponse } from '~/types';

definePageMeta({
  layout: 'profile',
  middleware: 'auth',
});
const sellers = ref<Account[]>([]);
const toast = useToast();

onBeforeMount(async () => {
  await render();
});

async function render() {
  const { data } = await axiosClient.get<AccountResponse>('/admin/sellers');
  if (!data) return;
  sellers.value = data.users;
}

async function accept(id: number) {
  const { data } = await axiosClient.put(`/admin/sellers/${id}`);
  toast.add({
    title: data.status === 200 ? 'Success' : 'Error',
    description: data.message,
    color: data.status === 200 ? 'success' : 'error',
  });
  await render();
}
</script>

<style scoped></style>
