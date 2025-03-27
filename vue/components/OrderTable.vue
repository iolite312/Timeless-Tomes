<template>
  <details class="w-full">
    <summary class="text-xl">
      Order #{{ order.id }} Status:{{ order.payment_status }}
    </summary>
    <table class="w-full border-white border">
      <thead>
        <tr>
          <th class="border-white border">Product</th>
          <th class="border-white border">Quantity</th>
          <th class="border-white border">Price</th>
          <th class="border-white border">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in order.order_lines" :key="item.id">
          <td class="border-white border">{{ item.book?.title }}</td>
          <td class="border-white border">{{ item.quantity }}</td>
          <td class="border-white border">{{ item.book?.price }}</td>
          <td class="border-white border">
            {{ item.book?.price ?? 0 * item.quantity }}
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
