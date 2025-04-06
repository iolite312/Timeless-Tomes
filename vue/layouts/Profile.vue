<template>
  <div>
    <HeaderComponent />
    <div class="flex flex-row max-height">
      <UNavigationMenu
        orientation="vertical"
        :items="items"
        class="data-[orientation=vertical]:w-54 border-r-1 border-t-1 p-4 border-[#475569]"
      />
      <div
        class="flex flex-col items-center flex-1 pt-12 border-t-1 p-4 border-[#475569]"
      >
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { NavigationMenuItem } from '@nuxt/ui';

const items = ref<NavigationMenuItem[][]>([
  [
    {
      label: 'Home',
      icon: 'i-material-symbols-home-outline-rounded',
      to: '/profile',
    },
    {
      label: 'My orders',
      icon: 'i-mdi-paper-text',
      to: '/profile/orders',
    },
    {
      label: 'Manage profile',
      icon: 'i-material-symbols-edit-square-outline-rounded',
      to: '/profile/manage',
    },
  ],
]);

onBeforeMount(async () => {
  const result = await useAccountStore().checkRole();

  if (
    (result.user.role == 'seller' && result.user.approved) ||
    result.user.role == 'admin'
  ) {
    items.value.push([
      {
        label: 'Products',
        icon: 'i-streamline-production-belt',
        to: `/profile/seller/products${
          result.user.seller_id ? `?id=${result.user.seller_id}` : ''
        }`,
      },
    ]);
  }

  if (result.user.role == 'admin') {
    items.value.push([
      {
        label: 'Users',
        icon: 'i-mdi-account-multiple',
        to: '/profile/admin/users',
      },
      {
        label: 'Orders',
        icon: 'i-mdi-paper-text',
        to: '/profile/admin/orders',
      },
      {
        label: 'Seller requests',
        icon: 'i-mdi-account-cash',
        to: '/profile/admin/requests',
      },
      {
        label: 'Products',
        icon: 'i-streamline-production-belt',
        to: '/profile/admin/products',
      },
    ]);
  }
});
</script>

<style scoped>
.max-height {
  height: calc(100vh - 48px);
}
</style>
