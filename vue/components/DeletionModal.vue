<template>
  <UModal :title="`${title}`">
    <template #body>
      <p>{{ description }}</p>
    </template>
    <template #footer>
      <div class="flex gap-2">
        <UButton color="neutral" label="Close" @click="modal.close()" />
        <UButton color="error" label="Delete" @click="onDeletion" />
      </div>
    </template>
  </UModal>
</template>
<script lang="ts" setup>
const modal = useModal();

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

const emit = defineEmits(['deletion']);

const onDeletion = () => {
  emit('deletion');
};
</script>

<style></style>
