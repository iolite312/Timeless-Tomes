<template>
  <div class="flex-1 w-full">
    <div class="flex justify-between items-center">
      <h3 class="text-2xl font-bold mb-4">My products</h3>
      <UButton>
        <NuxtLink to="/profile/seller/products/new">Add product</NuxtLink>
      </UButton>
    </div>
    <div class="flex flex-row flex-wrap gap-4">
      <BookCard
        v-for="book in books"
        :key="book.id"
        :book="book"
        @remove="renderPage()"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import axiosClient from '~/axios';
import type { Book, BooksResponse } from '~/types';

definePageMeta({
  layout: 'profile',
  middleware: 'auth',
});

const books = ref<Book[]>([]);

onBeforeMount(async () => {
  renderPage();
});

async function renderPage() {
  books.value = [];
  axiosClient.defaults.headers.common.Authorization = `Bearer ${
    useAccountStore().token
  }`;
  const { data } = await axiosClient.get<BooksResponse>(
    `/seller/products?id=${useRoute().query.id}`
  );
  books.value = data.books;
}
</script>

<style scoped></style>
