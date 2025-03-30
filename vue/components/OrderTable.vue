<template>
  <div class="flex-1 divide-y divide-(--ui-border-accented) w-full">
    <div class="flex items-center gap-2 px-4 py-3.5 overflow-x-auto">
      <UInput
        :model-value="(table?.tableApi?.getColumn('name')?.getFilterValue() as string)"
        class="max-w-sm min-w-[12ch]"
        placeholder="Filter by customer name..."
        @update:model-value="
          table?.tableApi?.getColumn('name')?.setFilterValue($event)
        "
      />

      <UDropdownMenu
        :items="table?.tableApi?.getAllColumns().filter(column => column.getCanHide()).map(column => ({
          label: upperFirst(column.id),
          type: 'checkbox' as const,
          checked: column.getIsVisible(),
          onUpdateChecked(checked: boolean) {
            table?.tableApi?.getColumn(column.id)?.toggleVisibility(!!checked)
          },
          onSelect(e?: Event) {
            e?.preventDefault()
          }
        }))"
        :content="{ align: 'end' }"
      >
        <UButton
          label="Columns"
          color="neutral"
          variant="outline"
          trailing-icon="i-lucide-chevron-down"
          class="ml-auto"
          aria-label="Columns select dropdown"
        />
      </UDropdownMenu>
    </div>

    <UTable ref="table" :data="orders" :columns="columns" sticky class="h-fit">
      <template #expanded="{ row }">
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-md m-4">
          <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">
            Order Details
          </h3>
          <div
            class="overflow-hidden overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <table
              class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
            >
              <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Book ID
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Title
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Author
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Quantity
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Price
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    Subtotal
                  </th>
                </tr>
              </thead>
              <tbody
                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
              >
                <tr
                  v-for="(item, index) in row.original.order_lines"
                  :key="index"
                >
                  <td
                    class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                  >
                    {{ item.book?.id }}
                  </td>
                  <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">
                    {{ item.book?.title }}
                  </td>
                  <td
                    class="px-4 py-4 text-sm text-gray-500 dark:text-gray-300"
                  >
                    {{ item.book?.author }}
                  </td>
                  <td
                    class="px-4 py-4 text-sm text-gray-500 dark:text-gray-300"
                  >
                    {{ item.quantity }}
                  </td>
                  <td
                    class="px-4 py-4 text-sm text-gray-500 dark:text-gray-300"
                  >
                    {{
                      new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'EUR',
                      }).format(item.book!.price)
                    }}
                  </td>
                  <td
                    class="px-4 py-4 text-sm text-gray-500 dark:text-gray-300"
                  >
                    {{
                      new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'EUR',
                      }).format(item.book!.price * item.quantity)
                    }}
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <td
                    colspan="5"
                    class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-300"
                  >
                    Total:
                  </td>
                  <td
                    class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"
                  >
                    {{
                      new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'EUR',
                      }).format(calculateTotalPrice(row.original))
                    }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
              <h4
                class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2"
              >
                Shipping Address
              </h4>
              <div class="text-sm dark:text-gray-200">
                <p class="font-medium">
                  {{ row.original.first_name }} {{ row.original.last_name }}
                </p>
                <p>{{ row.original.street }}</p>
                <p>{{ row.original.city }}, {{ row.original.postalcode }}</p>
              </div>
            </div>
            <div>
              <h4
                class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2"
              >
                Order Information
              </h4>
              <div class="text-sm dark:text-gray-200">
                <p>
                  <span class="font-medium">Order ID:</span> #{{
                    row.original.id
                  }}
                </p>
                <p>
                  <span class="font-medium">Status: </span>
                  <UBadge
                    class="capitalize"
                    variant="subtle"
                    :color="
                      {
                        completed: 'success',
                        pending: 'warning',
                        failed: 'error',
                      }[row.original!.payment_status as string]
                    "
                    >{{ row.original.payment_status }}</UBadge
                  >
                </p>
              </div>
            </div>
          </div>
        </div>
      </template>
    </UTable>
  </div>
</template>

<script lang="ts" setup>
import { h, resolveComponent } from 'vue';
import { upperFirst } from 'scule';
import type { TableColumn } from '@nuxt/ui';
import type { Order } from '~/types';

defineProps({
  orders: {
    type: Array as PropType<Order[]>,
    required: true,
  },
});

const UButton = resolveComponent('UButton');
const UBadge = resolveComponent('UBadge');
const UDropdownMenu = resolveComponent('UDropdownMenu');

const toast = useToast();

const columns: TableColumn<Order>[] = [
  {
    accessorKey: 'id',
    header: 'Order ID',
    cell: ({ row }) => `#${row.getValue('id')}`,
  },
  {
    accessorKey: 'name',
    header: 'Customer Name',
    cell: ({ row }) => `${row.original.first_name} ${row.original.last_name}`,
  },
  {
    accessorKey: 'street',
    header: 'Street',
    cell: ({ row }) => row.getValue('street'),
  },
  {
    accessorKey: 'city',
    header: 'City',
    cell: ({ row }) => row.getValue('city'),
  },
  {
    accessorKey: 'postalcode',
    header: 'Postal Code',
    cell: ({ row }) => row.getValue('postalcode'),
  },
  {
    accessorKey: 'payment_status',
    header: 'Payment status',
    cell: ({ row }) => {
      const statusValue = row.getValue('payment_status') as string;
      const color = {
        completed: 'success' as const,
        pending: 'warning' as const,
        failed: 'error' as const,
      }[statusValue];

      return h(
        UBadge,
        { class: 'capitalize', variant: 'subtle', color },
        () => statusValue
      );
    },
  },
  {
    accessorKey: 'totalPrice',
    header: () => h('div', { class: 'text-right' }, 'Total Price'),
    cell: ({ row }) => {
      const amount = calculateTotalPrice(row.original);

      const formatted = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'EUR',
      }).format(amount);

      return h('div', { class: 'text-right font-medium' }, formatted);
    },
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const items = [
        {
          type: 'label',
          label: 'Actions',
        },
        {
          label: 'Copy order ID',
          onSelect() {
            navigator.clipboard.writeText(row.original.id?.toString() ?? '');

            toast.add({
              title: 'Order ID copied to clipboard!',
              color: 'success',
              icon: 'i-lucide-circle-check',
            });
          },
        },
        {
          label: row.getIsExpanded() ? 'Collapse' : 'Expand',
          onSelect() {
            row.toggleExpanded();
          },
        },
        {
          type: 'separator',
        },
        {
          label: 'View customer details',
        },
        {
          label: 'View order history',
        },
      ];

      return h(
        'div',
        { class: 'text-right' },
        h(
          UDropdownMenu,
          {
            content: {
              align: 'end',
            },
            items,
            'aria-label': 'Actions dropdown',
          },
          () =>
            h(UButton, {
              icon: 'i-lucide-ellipsis-vertical',
              color: 'neutral',
              variant: 'ghost',
              class: 'ml-auto',
              'aria-label': 'Actions dropdown',
            })
        )
      );
    },
  },
];

const table = useTemplateRef('table');

const calculateTotalPrice = (order: Order) =>
  order.order_lines.reduce(
    (sum, item) => sum + item.book!.price * item.quantity,
    0
  );
</script>

<style scoped></style>
