// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  app: {
    head: {
      link: [
        { rel: 'stylesheet', href: '/css/satoshi.css' }
      ]
    }
  },
  modules: [
    '@nuxt/ui',
    '@nuxt/image',
    '@pinia/nuxt',
    'pinia-plugin-persistedstate/nuxt',
    '@vueuse/nuxt',
    '@nuxt/eslint',
    'nuxt-meilisearch',
  ],
  css: ['~/assets/css/main.css'],
  ssr: false,
  icon: {
    serverBundle: "local"
  },
  vite: {
    envDir: '../',
    server: {
      allowedHosts: true,
    }
  },
  runtimeConfig: {
    public: {
      BASE_URL: process.env.BASE_URL
    }
  },
  meilisearch: {
  },
  piniaPluginPersistedstate: {
    storage: 'localStorage'
  }
})
