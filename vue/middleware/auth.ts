export default defineNuxtRouteMiddleware((to, from) => {
  const accountStore = useAccountStore()

  if (!accountStore.isAuthenticated) {
    return navigateTo('/login')
  }

  if (to.path === '/login' && accountStore.isAuthenticated) {
    return navigateTo(from.path)
  }
})
