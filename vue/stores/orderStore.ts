import axiosClient from '~/axios'
import type { CreateOrder, Order } from '~/types'

export const useOrderStore = defineStore('order', () => {
  const order = ref<Order | null>(null)
  let clientSecret = ref<string>()

  function createOrder(data: CreateOrder) {
    axiosClient.defaults.headers.common.Authorization = `Bearer ${useAccountStore().token}`
    return new Promise((resolve, reject) => {
      let order: Order = { ...data, orderlines: useAccountStore().cart }
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
  return { order, clientSecret, createOrder }
})
