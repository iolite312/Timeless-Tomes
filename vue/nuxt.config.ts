// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  modules: [
    '@nuxt/ui', 
    '@pinia/nuxt', 
    '@vueuse/nuxt', 
    '@nuxt/eslint'
  ],
  css: ['~/assets/css/main.css'],
  ssr: false,
  icon: {
    serverBundle: "local"
  }
})