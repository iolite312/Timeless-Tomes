<template>
    <div>
        <UButton label="Show toast" color="neutral" variant="outline" @click="showToast" />
    </div>
</template>

<script setup lang="ts">
import type { ToastProps } from '@nuxt/ui'

const toast = useToast()

function showToast() {
  toast.add({
    title: 'Uh oh! Something went wrong.',
    description: 'There was a problem with your request.',
    icon: 'i-lucide-wifi',
    color: "error"
  })
}
</script>

<style scoped>

</style>