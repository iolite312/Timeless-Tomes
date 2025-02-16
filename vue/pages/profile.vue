<template>
  <UForm
    ref="registerForm"
    :state="state"
    class="flex flex-col gap-4"
    @submit="updateUser"
  >
    <UFormField label="First name" name="first_name">
      <UInput v-model="state.first_name" class="w-full" />
    </UFormField>
    <UFormField label="Last name" name="last_name">
      <UInput v-model="state.last_name" class="w-full" />
    </UFormField>
    <UFormField label="Email" name="email">
      <UInput v-model="state.email" class="w-full" disabled />
    </UFormField>
    <UFormField label="Password" name="password">
      <UInput v-model="state.password" class="w-full" type="password" />
    </UFormField>
    <UFormField label="Confirm Password" name="confirmPassword">
      <UInput v-model="state.confirmPassword" class="w-full" type="password" />
    </UFormField>

    <UButton label="Update" type="submit" variant="solid" class="self-end" />
  </UForm>
</template>

<script setup lang="ts">
import type { Account } from '~/types';

definePageMeta({
  middleware: 'auth',
});

const userStore = useAccountStore();
const toast = useToast();

if (!userStore.account) {
  navigateTo('/login');
}

const account = userStore.account as Account;

const state = ref({
  first_name: account.first_name,
  last_name: account.last_name,
  email: account.email,
  password: '',
  confirmPassword: '',
  profile_picture: account.profile_picture,
});

async function updateUser() {
  userStore
    .updateUser(state.value)
    .then(() => {
      toast.add({
        title: 'Success',
        description: 'User updated successfully',
        color: 'success',
      });
    })
    .catch(() => {
      toast.add({
        title: 'Error',
        description: 'Something went wrong',
        color: 'error',
      });
    });
}
</script>

<style scoped></style>
