<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="xl:max-w-3/4">
    <div
      class="flex flex-col items-center md:items-start justify-center md:flex-row"
    >
      <NuxtImg
        class="max-w-96 h-full lg:max-w-1/2 mr-4 my-4 rounded-md"
        :src="`${useRuntimeConfig().public.BASE_URL}/images/uploads/books/${
          data.book.picture
        }`"
      />
      <div class="m-4">
        <div class="flex justify-between items-center">
          <h1 class="text-5xl font-bold">{{ data.book.title }}</h1>
          <div class="hidden xl:flex items-center gap-4">
            <p class="text-2xl font-bold">€{{ data.book.price.toFixed(2) }}</p>
            <UButton
              v-if="data.book.stock > 0"
              color="primary"
              label="Add to cart"
              class="text-xl font-bold"
              icon="i-lucide-shopping-cart"
              @click="addToCart()"
            />
            <UButton
              v-else
              color="neutral"
              label="Add to cart"
              class="text-xl font-bold"
              icon="i-lucide-shopping-cart"
              disabled
            />
          </div>
        </div>
        <h2 class="text-2xl mb-4">
          By {{ data.book.author }} | {{ data.book.language }}
        </h2>
        <div class="flex items-center gap-4 mb-4 xl:hidden">
          <p class="text-2xl font-bold">€{{ data.book.price.toFixed(2) }}</p>
          <UButton
            v-if="data.book.stock > 0"
            color="primary"
            label="Add to cart"
            class="text-xl font-bold"
            icon="i-lucide-shopping-cart"
            @click="addToCart()"
          />
          <UButton
            v-else
            color="neutral"
            label="Add to cart"
            class="text-xl font-bold"
            icon="i-lucide-shopping-cart"
            disabled
          />
        </div>
        <p class="whitespace-pre-wrap">
          {{ data.book.description }}
        </p>
      </div>
    </div>
    <div>
      <h3 class="text-2xl font-bold mb-4">Details</h3>
      <div class="flex flex-col md:flex-row md:gap-4">
        <NuxtImg
          class="hidden md:block w-28 rounded-md"
          :src="`${useRuntimeConfig().public.BASE_URL}/images/uploads/books/${
            data.book.picture
          }`"
        />
        <div>
          <hr class="mb-2" />
          <p>Author(s): {{ data.book.author }}</p>
          <hr class="my-2" />
          <p>ISBN: {{ data.book.isbn }}</p>
          <hr class="my-2 hidden md:block" />
        </div>
        <div>
          <hr class="my-2 md:mt-0" />
          <p>Language: {{ data.book.language }}</p>
          <hr class="my-2" />
          <p>Genre: {{ data.book.genre.join(', ') }}</p>
          <hr class="my-2" />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { NuxtImg, UButton } from '#components';
import axiosClient from '~/axios';
import type { Book, BookResponse, CartItem } from '~/types';

const id = useRoute().params.id;
const { data } = await axiosClient.get<BookResponse>(`/books/${id}`);
const accountStore = useAccountStore();
const toast = useToast();

function addToCart() {
  accountStore.addToCart(convertToCart(data.book, 1));
  toast.add({
    title: 'Success',
    description: 'Book added to cart',
    color: 'success',
  });
}
function convertToCart(book: Book, quantity: number): CartItem {
  return {
    ...book,
    quantity,
  };
}
</script>

<style scoped></style>
