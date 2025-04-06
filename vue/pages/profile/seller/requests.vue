<template>
  <div class="flex-1 w-full max-w-md mx-auto">
    <h3 class="text-2xl font-bold mb-4">Become a Seller</h3>
    <UForm :state="formState" @submit="onSubmit">
      <UFormGroup label="Seller Name" name="name">
        <UInput v-model="formState.name" placeholder="Enter your seller name" />
      </UFormGroup>
      <UButton type="submit" class="mt-4 ml-4"> Submit Request </UButton>
    </UForm>
  </div>
</template>

<script setup lang="ts">
import axiosClient from '~/axios';

definePageMeta({
  layout: 'profile',
  middleware: 'auth',
});

const formState = ref({
  name: '',
});

async function onSubmit() {
  axiosClient
    .post('/profile/seller', formState.value)
    .then((response) => {
      if (response.status === 200) {
        useToast().add({
          title: 'Success',
          description: 'Your seller request has been submitted',
          color: 'primary',
        });
      }
    })
    .catch((error) => {
      useToast().add({
        title: 'Error',
        description: error.error,
        color: 'error',
      });
    });
}
</script>

<style scoped></style>
