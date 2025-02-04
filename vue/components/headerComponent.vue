<template>
    <div class="flex items-center justify-between mx-12 h-12">
        <div class="lg:flex-1 flex items-center">
            <NuxtLink to="/">
                <h1 class="text-2xl font-bold">Timeless Tomes</h1>
            </NuxtLink>
        </div>
        <UNavigationMenu :items="items" class="hidden lg:flex" />
        <div class="lg:flex-1 items-center justify-end flex gap-2">
            <NuxtLink to="/login">
                <UButton icon="i-material-symbols-account-circle-full" color="neutral" variant="ghost" />
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
            <USlideover side="right" class="lg:hidden" title="Timeless Tomes" close-icon="i-lucide-arrow-right">
                <UButton icon="i-lucide-menu" color="neutral" variant="outline" />
                <template #body>
                    <UNavigationMenu orientation="vertical" :items="items" />
                </template>
            </USlideover>
        </div>
    </div>
</template>

<script setup lang="ts">
const items = ref([
    {
        label: 'Home',
        icon: 'i-material-symbols-home-outline-rounded',
        to: '/',
    },
    {
        label: 'Search',
        icon: 'i-material-symbols-search-rounded',
        to: '/search',
    }

])
const colorMode = useColorMode()

const isDark = computed(() => colorMode.value === 'dark')

function toggleDarkMode() {
    colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}
</script>

<style scoped></style>