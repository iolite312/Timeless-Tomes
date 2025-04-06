<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">{{ title }}</h2>
    <UForm
      ref="bookForm"
      :schema="bookSchema"
      :state="state"
      class="flex flex-col gap-4 mt-4"
      @submit="createBook"
    >
      <UFormField label="Picture" name="picture">
        <BookPicture :src="state.picture" @change-picture="changePicture" />
      </UFormField>
      <div class="flex flex-row gap-4">
        <div class="flex flex-col gap-4">
          <UFormField label="Title" name="title">
            <UInput v-model="state.title" class="w-full" />
          </UFormField>
          <UFormField label="Description" name="description">
            <UInput v-model="state.description" class="w-full" />
          </UFormField>
          <UFormField label="Author" name="author">
            <UInput v-model="state.author" class="w-full" />
          </UFormField>
          <UFormField label="Initial stock" name="stock">
            <UInput
              v-model="state.stock"
              class="w-full"
              type="number"
              placeholder="1"
            />
          </UFormField>
        </div>
        <div class="flex flex-col gap-4">
          <UFormField label="Language" name="language">
            <UInput
              v-model="state.language"
              class="w-full"
              type="text"
              placeholder="language"
            />
          </UFormField>
          <UFormField label="Genres" name="genre">
            <USelectMenu
              v-model="state.genre"
              multiple
              :items="items"
              class="w-full"
            />
          </UFormField>
          <UFormField label="ISBN" name="isbn">
            <UInput
              v-model="state.isbn"
              class="w-full"
              type="text"
              placeholder="9780062872623"
            />
          </UFormField>
          <UFormField label="Price in EUR" name="price">
            <UInput
              v-model="state.price"
              class="w-full"
              type="number"
              placeholder="29,99"
            />
          </UFormField>
        </div>
      </div>
      <UButton
        :label="button"
        type="submit"
        variant="solid"
        class="justify-around"
      />
    </UForm>
  </div>
</template>

<script lang="ts" setup>
import type { FormSubmitEvent } from '@nuxt/ui';
import * as z from 'zod';
import axiosClient from '~/axios';
import type { BookResponse } from '~/types';

defineProps({
  title: {
    type: String,
    required: true,
  },
  button: {
    type: String,
    required: true,
  },
});

const items = ref([
  'Action',
  'Adventure',
  'Animated',
  'Biography',
  'Comedy',
  'Crime',
  'Dance',
  'Disaster',
  'Documentary',
  'Drama',
  'Erotic',
  'Family',
  'Fantasy',
  'Found Footage',
  'Historical',
  'Horror',
  'Independent',
  'Legal',
  'Live Action',
  'Martial Arts',
  'Musical',
  'Mystery',
  'Noir',
  'Performance',
  'Political',
  'Romance',
  'Satire',
  'Science Fiction',
  'Short',
  'Silent',
  'Slasher',
  'Sports',
  'Spy',
  'Superhero',
  'Supernatural',
  'Suspense',
  'Teen',
  'Thriller',
  'War',
  'Western',
]);

const emit = defineEmits(['submit']);

const state = ref({
  id: parseInt(useRoute().params.id as string) ?? '',
  title: '',
  description: '',
  picture: '',
  author: '',
  language: '',
  genre: [] as string[],
  isbn: '',
  price: 0,
  stock: 0,
  seller_id: useAccountStore().account?.seller_id,
});

onMounted(async () => {
  if (useRoute().params.id) {
    axiosClient.defaults.headers.common.Authorization = `Bearer ${
      useAccountStore().token
    }`;
    const { data } = await axiosClient.get<BookResponse>(
      `/seller/products/${useRoute().params.id}`
    );

    state.value = data.book;
    state.value.isbn = data.book.isbn.toString();
    state.value.seller_id = useAccountStore().account?.seller_id;
  }
});

const bookSchema = z.object({
  title: z.string().min(2, 'Title must be at least 2 characters'),
  description: z.string().min(2, 'Description must be at least 2 characters'),
  picture: z.string().min(2, 'Picture is required'),
  author: z.string().min(2, 'Author must be at least 2 characters'),
  language: z.string().min(2, 'Language must be at least 2 characters'),
  genre: z.array(z.string()).min(1, 'At least one genre must be selected'),
  isbn: z.string().min(10, 'ISBN must be at least 10 numbers'),
  price: z.number().min(1, 'Price must be at least 1 EUR'),
  stock: z.number().min(0, 'Stock must be at least 0'),
  seller_id: z.number(),
});

export type BookSchema = z.output<typeof bookSchema>;

async function createBook(event: FormSubmitEvent<BookSchema>) {
  emit('submit', event);
}

function changePicture(n: string) {
  state.value.picture = n;
}
</script>

<style scoped></style>
