<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">profile</h2>
    <UForm
      ref="registerForm"
      :state="state"
      class="flex flex-col gap-4"
      @submit="updateUser"
    >
      <div class="flex flex-row gap-4">
        <div class="flex flex-col gap-4">
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
            <UInput
              v-model="state.confirmPassword"
              class="w-full"
              type="password"
            />
          </UFormField>
        </div>
        <div class="flex flex-col gap-4">
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
        </div>
      </div>
      <UButton label="Update" type="submit" variant="solid" class="self-end" />
    </UForm>
  </div>
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
  street: account.street,
  city: account.city,
  postalcode: account.postalcode,
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
