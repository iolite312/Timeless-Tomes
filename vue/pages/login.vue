<template>
  <UTabs
    :items="items"
    variant="link"
    class="gap-4 w-2xs"
    :ui="{ trigger: 'flex-1' }"
  >
    <template #login>
      <UForm
        ref="loginForm"
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
        ref="registerForm"
        :schema="registerSchema"
        :state="state"
        class="flex flex-col gap-4"
        @submit="validateRegister"
      >
        <UFormField label="First name" name="first_name">
          <UInput v-model="state.first_name" class="w-full" />
        </UFormField>
        <UFormField label="Last name" name="last_name">
          <UInput v-model="state.last_name" class="w-full" />
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
import type { FormSubmitEvent, FormError } from '@nuxt/ui';
import type { Login, Register } from '~/types';

definePageMeta({
  middleware: 'auth',
});

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
const registerForm = useTemplateRef('registerForm');
const loginForm = useTemplateRef('loginForm');

const loginSchema = z.object({
  email: z.string().email('Invalid email'),
  password: z.string().min(6, 'Password must be at least 6 characters'),
});

const registerSchema = z
  .object({
    first_name: z.string().min(2, 'First name must be at least 2 characters'),
    last_name: z.string().min(2, 'Last name must be at least 2 characters'),
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
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  confirmPassword: '',
});

const toast = useToast();
const userStore = useAccountStore();
const formError: FormError[] = [];

async function validateLogin(event: FormSubmitEvent<LoginSchema>) {
  userStore
    .login(event.data as Login)
    .then(() => {
      useRouter().push('/');
    })
    .catch(() => {
      formError.push(
        {
          name: 'password',
          message: 'Invalid email or password',
        },
        {
          name: 'email',
          message: 'Invalid email or password',
        }
      );
      loginForm?.value?.setErrors(formError);
      toast.add({
        title: 'Error',
        description: 'Something went wrong',
        color: 'error',
      });
    });
}

async function validateRegister(event: FormSubmitEvent<RegisterSchema>) {
  formError.length = 0;
  userStore
    .register(event.data as Register)
    .then(() => {
      useRouter().push('/');
    })
    .catch((error) => {
      if (error.response.data.status === 422) {
        console.log(error.response.data.errors);
        for (const [key, value] of Object.entries(error.response.data.errors)) {
          formError.push({
            name: key,
            message: value as string,
          });
        }
        registerForm?.value?.setErrors(formError);
      }
      toast.add({
        title: 'Error',
        description: 'Something went wrong',
        color: 'error',
      });
    });
}
</script>
