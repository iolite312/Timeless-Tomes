<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Create a new product</h2>
    <UForm
      ref="profileForm"
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
        label="Add product"
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
import type { CreateBook } from '~/types';

definePageMeta({
  layout: 'profile',
  middleware: 'auth',
});

const toast = useToast();

const items = ref(['Backlog', 'Todo', 'In Progress', 'Done']);

const state = ref({
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

type BookSchema = z.output<typeof bookSchema>;

async function createBook(event: FormSubmitEvent<BookSchema>) {
  const book = await useBookStore().createBook(event.data as CreateBook);

  if (book) {
    toast.add({
      title: 'Success',
      description: 'Book created successfully',
      color: 'success',
    });
    setTimeout(() => {
      useRouter().push('/profile/seller/products?id=' + book.seller_id);
    }, 2000);
  } else {
    toast.add({
      title: 'Error',
      description: 'Something went wrong',
      color: 'error',
    });
  }
}

function changePicture(n: string) {
  state.value.picture = n;
}
</script>

<style scoped></style>
