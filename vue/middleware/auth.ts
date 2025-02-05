export default defineNuxtRouteMiddleware((to, from) => {
  const accountStore = useAccountStore()

  if (!accountStore.isAuthenticated && to.path !== '/login') {
    return abortNavigation();
  }

  if (to.path === '/login' && accountStore.isAuthenticated) {
    return navigateTo(from.path);
  }
})
