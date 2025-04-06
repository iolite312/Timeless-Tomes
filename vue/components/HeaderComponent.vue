<template>
  <div class="flex items-center justify-between mx-12 h-12">
    <div class="lg:flex-1 flex items-center">
      <NuxtLink to="/">
        <h1 class="text-2xl font-bold">Timeless Tomes</h1>
      </NuxtLink>
    </div>
    <UNavigationMenu :items="navbarItems" class="hidden lg:flex" />
    <div class="lg:flex-1 items-center justify-end flex gap-2">
      <UDropdownMenu
        v-if="userStore.isAuthenticated"
        arrow
        :items="dropdownItems"
        :ui="{
          content: 'w-48',
        }"
      >
        <UButton
          :avatar="{
            src:
              useRuntimeConfig().public.BASE_URL +
              '/images/' +
              (userStore.account?.profile_picture === 'profile_placeholder.png'
                ? userStore.account?.profile_picture
                : 'uploads/profile/' + userStore.account?.profile_picture),
          }"
          color="neutral"
          variant="outline"
          :label="userStore.fullname"
        />
      </UDropdownMenu>

      <NuxtLink v-else to="/login">
        <UButton
          icon="i-material-symbols-account-circle-full"
          color="neutral"
          variant="ghost"
        />
      </NuxtLink>
      <NuxtLink v-if="userStore.isAuthenticated" to="/cart">
        <UChip
          v-if="userStore.cartCount > 0"
          :text="userStore.cartCount"
          size="3xl"
        >
          <UButton
            icon="i-lucide-shopping-basket"
            color="neutral"
            variant="outline"
          />
        </UChip>
        <UButton
          v-else
          icon="i-lucide-shopping-basket"
          color="neutral"
          variant="outline"
        />
      </NuxtLink>
      <ClientOnly v-if="!colorMode?.forced">
        <UButton
          :icon="isDark ? 'i-lucide-moon' : 'i-lucide-sun'"
          color="neutral"
          variant="ghost"
          @click="toggleDarkMode"
        />

        <template #fallback>
          <div class="size-8" />
        </template>
      </ClientOnly>
      <USlideover
        side="right"
        class="lg:hidden"
        title="Timeless Tomes"
        close-icon="i-lucide-arrow-right"
      >
        <UButton icon="i-lucide-menu" color="neutral" variant="outline" />
        <template #body>
          <UNavigationMenu orientation="vertical" :items="navbarItems" />
        </template>
      </USlideover>
    </div>
  </div>
</template>

<script setup lang="ts">
import { UChip } from '#components';

const navbarItems = ref([
  {
    label: 'Home',
    icon: 'i-material-symbols-home-outline-rounded',
    to: '/',
  },
  {
    label: 'Search',
    icon: 'i-material-symbols-search-rounded',
    to: '/search',
  },
]);

const dropdownItems = ref([
  [
    {
      label: 'Profile',
      icon: 'i-material-symbols-account-circle-full',
      to: '/profile',
    },
    {
      label: 'Logout',
      icon: 'i-material-symbols-exit-to-app-rounded',
      type: 'checkbox' as const,
      onUpdateChecked() {
        userStore.$reset();
        useRouter().push('/');
      },
    },
  ],
]);
const colorMode = useColorMode();
const userStore = useAccountStore();

const isDark = computed(() => colorMode.value === 'dark');

function toggleDarkMode() {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark';
}
</script>

<style scoped></style>
