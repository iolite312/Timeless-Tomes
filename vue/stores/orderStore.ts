import axiosClient from '~/axios'
import type { CreateOrder, Order } from '~/types'

export const useOrderStore = defineStore('order', () => {
  const orders = ref<Order[]>([])
  const clientSecret = ref<string>()

  function createOrder(data: CreateOrder) {
    axiosClient.defaults.headers.common.Authorization = `Bearer ${useAccountStore().token}`
    return new Promise((resolve, reject) => {
      const order: Order = { ...data, order_lines: useAccountStore().cart }
      axiosClient.post('/cart/create', order)
        .then((response) => {
          clientSecret.value = response.data.clientSecret
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }

  function getUserOrders() {
    axiosClient.defaults.headers.common.Authorization = `Bearer ${useAccountStore().token}`
    return new Promise((resolve, reject) => {
      axiosClient.get('/profile/orders')
        .then((response) => {
          orders.value = response.data.orders
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }

  function $reset() {
    orders.value = []
    clientSecret.value = ''
  }
  return { orders, clientSecret, createOrder, getUserOrders, $reset }
})
