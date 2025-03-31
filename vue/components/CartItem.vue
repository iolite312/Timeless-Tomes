<template>
  <div
    class="flex flex-row items-center gap-4 justify-between rounded-md border border-[#475569] p-4"
  >
    <NuxtLink
      class="max-w-40 lg:max-w-1/5 xl:max-w-1/8"
      :to="`/books/${book.id}`"
    >
      <NuxtImg
        class="rounded-md"
        :src="`${useRuntimeConfig().public.BASE_URL}/images/uploads/books/${
          book.picture
        }`"
      />
    </NuxtLink>
    <div
      class="flex flex-col md:flex-row md:grow items-center md:items-start justify-between gap-2"
    >
      <h3 class="text-2xl font-bold">{{ book.title }}</h3>
      <UInputNumber
        v-model="count"
        :default-value="1"
        :min="1"
        class="w-32"
        @change="updateCart"
      />
      <p class="font-bold">
        {{ count }} x €{{ book.price.toFixed(2) }}: €{{
          (book.price * count).toFixed(2)
        }}
      </p>
      <UButton icon="i-lucide-trash-2" color="error" @click="removeFromCart" />
    </div>
  </div>
</template>

<script lang="ts" setup>
import type { Book } from '~/types';

const props = defineProps({
  book: {
    type: Object as PropType<Book>,
    required: true,
  },
});

const emit = defineEmits(['removeFromCart']);

const count = ref(
  useAccountStore().cart.find((item) => item.id === props.book.id)?.quantity ||
    1
);

function updateCart() {
  useAccountStore().updateQuantity(props.book.id, count.value);
}

function removeFromCart() {
  useAccountStore().removeFromCart(props.book.id);
  emit('removeFromCart');
}
</script>

<style scoped></style>
