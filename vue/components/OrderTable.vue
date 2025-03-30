<template>
  <details class="w-full">
    <summary class="text-xl mb-3">
      Order #{{ order.id }} Payment Status:{{ order.payment_status }}
    </summary>
    <table class="w-full border-white border">
      <thead>
        <tr>
          <th class="border-white border p-2">Product</th>
          <th class="border-white border p-2">Quantity</th>
          <th class="border-white border p-2">Price</th>
          <th class="border-white border p-2">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in order.order_lines" :key="item.id">
          <td class="border-white border p-2">{{ item.book?.title }}</td>
          <td class="border-white border p-2">{{ item.quantity }}</td>
          <td class="border-white border p-2">{{ item.book?.price }}</td>
          <td class="border-white border p-2">
            {{ item.book?.price ?? 0 * item.quantity }}
          </td>
          <td class="border-white border p-2">
            <UButton
              ><NuxtLink :to="`/books/${item.book?.id}`"
                >View</NuxtLink
              ></UButton
            >
          </td>
        </tr>
      </tbody>
    </table>
  </details>
</template>

<script lang="ts" setup>
import type { Order } from '~/types';

const props = defineProps({
  order: {
    type: Object as PropType<Order>,
    required: true,
  },
});
console.log(props.order.order_lines);
</script>

<style scoped></style>
