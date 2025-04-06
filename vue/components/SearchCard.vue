<template>
  <UCard variant="outline" class="w-fit">
    <template #header>
      <h1 class="text-2xl font-bold">
        {{ book.title }} <span class="text-sm">({{ book.author }})</span>
      </h1>
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
            icon="i-ix-arrow-diagonal-top-right"
            :to="`/books/${book.id}`"
            >View Book</UButton
          >
        </div>
      </div>
    </template>
  </UCard>
</template>

<script lang="ts" setup>
import type { Book } from '~/types';

defineProps({
  book: {
    type: Object as PropType<Book>,
    required: true,
  },
});
</script>

<style scoped></style>
