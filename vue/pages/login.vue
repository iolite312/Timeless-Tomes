<template>
  <UTabs
    :items="items"
    variant="link"
    class="gap-4 w-2xs"
    :ui="{ trigger: 'flex-1' }"
  >
    <template #login>
      <UForm
        :schema="loginSchema"
        :state="state"
        class="flex flex-col gap-4"
        @submit="validateLogin"
      >
        <UFormField label="Email" name="email">
          <UInput v-model="state.email" class="w-full" />
        </UFormField>
        <UFormField label="Password" name="password">
          <UInput v-model="state.password" class="w-full" type="password" />
        </UFormField>

        <UButton
          label="Log in"
          type="submit"
          variant="solid"
          class="self-end"
        />
      </UForm>
    </template>

    <template #register>
      <UForm
        :schema="registerSchema"
        :state="state"
        class="flex flex-col gap-4"
        @submit="validateRegister"
      >
        <UFormField label="First name" name="firstname">
          <UInput v-model="state.firstname" class="w-full" />
        </UFormField>
        <UFormField label="Last name" name="lastname">
          <UInput v-model="state.lastname" class="w-full" />
        </UFormField>
        <UFormField label="Email" name="email">
          <UInput v-model="state.email" class="w-full" />
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

        <UButton
          label="Register"
          type="submit"
          variant="solid"
          class="self-end"
        />
      </UForm>
    </template>
  </UTabs>
</template>

<script setup lang="ts">
import * as z from 'zod';
import type { FormSubmitEvent } from '@nuxt/ui';
import type { Login } from '~/types';

const items = [
  {
    label: 'Login',
    icon: 'i-material-symbols-account-circle-full',
    slot: 'login',
  },
  {
    label: 'Register',
    icon: 'i-material-symbols-edit-square-outline-rounded',
    slot: 'register',
  },
];

const loginSchema = z.object({
  email: z.string().email('Invalid email'),
  password: z.string().min(6, 'Password must be at least 6 characters'),
});

const registerSchema = z
  .object({
    firstname: z.string().min(2, 'First name must be at least 2 characters'),
    lastname: z.string().min(2, 'Last name must be at least 2 characters'),
    email: z.string().email('Invalid email'),
    password: z.string().min(6, 'Password must be at least 6 characters'),
    confirmPassword: z.string(),
  })
  .refine((data) => data.password === data.confirmPassword, {
    message: 'Passwords must match',
    path: ['confirmPassword'],
  });

type LoginSchema = z.output<typeof loginSchema>;
type RegisterSchema = z.output<typeof registerSchema>;

const state = ref({
  firstname: '',
  lastname: '',
  email: 'test@gmail.com',
  password: '12345678',
  confirmPassword: '',
});

const toast = useToast();
const userStore = useAccountStore();
async function validateLogin(event: FormSubmitEvent<LoginSchema>) {
  toast.add({
    title: 'Success',
    description: 'The form has been submitted.',
    color: 'success',
  });
  userStore.login(event.data as Login);
  console.log(userStore.account);
}

async function validateRegister(event: FormSubmitEvent<RegisterSchema>) {
  toast.add({
    title: 'Success',
    description: 'The form has been submitted.',
    color: 'success',
  });
  console.log(event.data);
}
</script>
