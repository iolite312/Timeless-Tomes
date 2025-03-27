<template>
  <div class="flex flex-col lg:flex-row w-full gap-4">
    <div class="w-full lg:w-3/4 flex flex-col gap-4">
      <BookCard v-for="book in bookData" :key="book.id" :book="book" />
    </div>
    <div class="w-full lg:w-1/4 flex flex-col gap-4">
      <div class="flex flex-col gap-2">
        <p class="text-xl font-bold">
          Subtotal: €{{ (total * 0.79).toFixed(2) }}
        </p>
        <p class="text-xl font-bold">
          21% VAT: €{{ (total * 0.21).toFixed(2) }}
        </p>
        <p class="text-2xl font-bold">Total: €{{ total.toFixed(2) }}</p>
      </div>
      <UForm
        :schema="orderSchema"
        :state="state"
        class="space-y-2"
        @submit="checkAvailability"
      >
        <UFormField label="First name" name="first_name">
          <UInput v-model="state.first_name" class="w-full" />
        </UFormField>
        <UFormField label="Last name" name="last_name">
          <UInput v-model="state.last_name" class="w-full" />
        </UFormField>

        <UFormField label="Street" name="street">
          <UInput
            v-model="state.street"
            class="w-full"
            type="text"
            placeholder="Bijv Straatnaam 1"
          />
        </UFormField>
        <UFormField label="City" name="city">
          <UInput
            v-model="state.city"
            class="w-full"
            type="text"
            placeholder="Bijv Amsterdam"
          />
        </UFormField>
        <UFormField label="Postal code" name="postalcode">
          <UInput
            v-model="state.postalcode"
            class="w-full"
            type="text"
            placeholder="1234AB"
          />
        </UFormField>

        <UButton type="submit"> Place order </UButton>
      </UForm>
    </div>
  </div>
</template>

<script lang="ts" setup>
import type { FormSubmitEvent } from '@nuxt/ui';
import * as z from 'zod';
import axiosClient from '~/axios';
import type { Account, Book, BookResponse, CreateOrder } from '~/types';

const bookData = ref<Book[]>([]);
const accountStore = useAccountStore();
const orderStore = useOrderStore();

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

const account = useAccountStore().account as Account;

const state = ref({
  first_name: account.first_name,
  last_name: account.last_name,
  street: account.street,
  city: account.city,
  postalcode: account.postalcode,
});

const orderSchema = z.object({
  first_name: z.string().min(1, 'First name is required'),
  last_name: z.string().min(1, 'Last name is required'),
  street: z.string().min(1, 'Street is required'),
  city: z.string().min(1, 'City is required'),
  postalcode: z.string().min(1, 'Postal code is required'),
});

type OrderSchema = z.output<typeof orderSchema>;

function checkAvailability(event: FormSubmitEvent<OrderSchema>) {
  const data = event.data as CreateOrder;
  accountStore
    .checkAvailability(data)
    .then((response) => {
      if (response.status === 200) {
        orderStore
          .createOrder(data)
          .then((response) => {
            navigateTo('/checkout');
          })
          .catch((error) => {
            console.log(error);
          });
      }
    })
    .catch((error) => {
      console.log(error);
    });
}
</script>

<style scoped></style>
