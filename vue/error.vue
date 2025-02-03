<template>
    <div>
        <UApp>
            <NuxtLayout>
                <h1>{{ props.error.statusCode }}</h1>
                <p>{{ props.error.message }}</p>
                <NuxtLink to="/">Go back home</NuxtLink>
            </NuxtLayout>
        </UApp>
    </div>
</template>
<script setup lang="ts">
import type { NuxtError } from '#app'

const props = defineProps<{
    error: NuxtError
}>()
</script>
