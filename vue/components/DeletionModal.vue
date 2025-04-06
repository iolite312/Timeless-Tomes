<template>
  <UModal :title="`${title}`">
    <template #body>
      <p>{{ description }}</p>
    </template>
    <template #footer>
      <div class="flex gap-2">
        <UButton color="neutral" label="Close" @click="onClose" />
        <UButton color="error" label="Delete" @click="onDeletion" />
      </div>
    </template>
  </UModal>
</template>
<script lang="ts" setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(['deletion', 'close']);

const onDeletion = () => {
  emit('deletion');
};

const onClose = () => {
  emit('close');
};
</script>

<style></style>
