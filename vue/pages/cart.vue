<template>
  <div class="flex flex-row w-full gap-4">
    <div class="w-3/4 flex flex-col gap-4">
      <BookCard v-for="book in bookData" :key="book.id" :book="book" />
    </div>
    <div class="w-1/4">
      <p class="text-2xl font-bold">Total: €{{ total.toFixed(2) }}</p>
    </div>
  </div>
</template>

<script lang="ts" setup>
import axiosClient from '~/axios';
import type { Book, BookResponse } from '~/types';

const bookData = ref<Book[]>([]);
const accountStore = useAccountStore();

accountStore.cart.forEach(async (element) => {
  const { data } = await axiosClient.get<BookResponse>(`/books/${element.id}`);
  bookData.value.push(data.book);
});

const total = computed(() => {
  return bookData.value.reduce((sum, book) => {
    const cartItem = accountStore.cart.find((item) => item.id === book.id);
    if (!cartItem) return sum;
    return sum + book.price * cartItem.quantity;
  }, 0);
});
</script>

<style scoped></style>
