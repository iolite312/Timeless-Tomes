<template>
  <BookEditForm
    title="Edit product"
    button="Update product"
    @submit="updateBook"
  />
</template>

<script lang="ts" setup>
import type { FormSubmitEvent } from '@nuxt/ui';
import type { BookSchema } from '~/components/BookEditForm.vue';
import type { UpdateBook } from '~/types';

definePageMeta({
  layout: 'profile',
  middleware: 'auth',
});

const toast = useToast();

async function updateBook(event: FormSubmitEvent<BookSchema>) {
  const book = await useBookStore().updateBook(event.data as UpdateBook);

  if (book) {
    toast.add({
      title: 'Success',
      description: 'Book Updated successfully',
      color: 'success',
    });
    setTimeout(() => {
      useRouter().push('/profile/seller/products?id=' + event.data.seller_id);
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
