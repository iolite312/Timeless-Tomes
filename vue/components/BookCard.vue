<template>
  <UCard variant="outline" class="w-fit">
    <template #header>
      <h1 class="text-2xl font-bold">{{ book.title }}</h1>
    </template>

    <NuxtImg
      :src="`${useRuntimeConfig().public.BASE_URL}/images/uploads/books/${
        book.picture
      }`"
      class="max-w-82 h-full rounded-md"
    />

    <template #footer>
      <div class="flex justify-between">
        <div>
          <p>€{{ book.price.toFixed(2) }}</p>
          <p>{{ book.stock }} in stock</p>
        </div>
        <div class="flex gap-2">
          <UButton
            icon="i-lucide-edit"
            class="w-12 h-12 flex items-center justify-center"
            :to="`/profile/seller/products/${book.id}`"
          />
          <UButton
            color="error"
            icon="i-lucide-trash"
            class="w-12 h-12 flex items-center justify-center"
            @click="warning(book.id)"
          />
        </div>
      </div>
    </template>
  </UCard>
</template>

<script lang="ts" setup>
import type { Book } from '~/types';
import { DeletionModal } from '#components';

defineProps({
  book: {
    type: Object as PropType<Book>,
    required: true,
  },
});

const modal = useModal();
const toast = useToast();
const bookStore = useBookStore();
const emit = defineEmits(['remove']);

function warning(id: number) {
  modal.open(DeletionModal, {
    title: 'Are you sure you want to delete this product?',
    description: 'This action cannot be undone',
    onDeletion() {
      bookStore.deleteBook(id).catch(() => {
        toast.add({
          title: 'Error',
          description: 'Something went wrong',
          color: 'error',
        });
      });
      setTimeout(() => {
        emit('remove');
      }, 500);
      modal.close();
    },
  });
}
</script>

<style scoped></style>
