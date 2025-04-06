<template>
  <BookEditForm
    title="Create new product"
    button="Create product"
    @submit="createBook"
  />
</template>

<script setup lang="ts">
import type { FormSubmitEvent } from '@nuxt/ui';
import type { BookSchema } from '~/components/BookEditForm.vue';
import type { CreateBook } from '~/types';

definePageMeta({
  layout: 'profile',
  middleware: 'auth',
});

const toast = useToast();

async function createBook(event: FormSubmitEvent<BookSchema>) {
  const book = await useBookStore().createBook(event.data as CreateBook);

  if (book) {
    console.log(book);
    toast.add({
      title: 'Success',
      description: 'Book created successfully',
      color: 'success',
    });
    setTimeout(() => {
      useRouter().push(
        '/profile/seller/products?id=' + useAccountStore().account?.seller_id
      );
    }, 2000);
  } else {
    toast.add({
      title: 'Error',
      description: 'Something went wrong',
      color: 'error',
    });
  }
}
</script>

<style scoped></style>
