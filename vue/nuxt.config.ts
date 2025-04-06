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
    },
    optimizeDeps: {
      exclude: [
        '@stripe/stripe-js',
        'scule'
      ]
    },
  },
  runtimeConfig: {
    public: {
      BASE_URL: process.env.BASE_URL,
      STRIPE_PUBLIC_KEY: process.env.STRIPE_PUBLIC_KEY,
      MEILI_HOST_URL: process.env.MEILI_HOST_URL_FRONTEND
    }
  },
  piniaPluginPersistedstate: {
    storage: 'localStorage'
  }
})
