import axiosClient from "~/axios"
import type { Account, CartItem, Login, Register, Update, UserResponse } from "~/types"

export const useAccountStore = defineStore('account', () => {
  const account = ref<Account | null>(null)
  const token = ref<string>('')
  const cart = ref<CartItem[]>([])

  const fullname = computed(() => {
    return `${account.value?.first_name} ${account.value?.last_name}`
  })
  const isAuthenticated = computed(() => {
    return !!token.value
  })
  const cartCount = computed(() => {
    const count = cart.value.reduce((acc, item) => acc + item.quantity, 0)
    return count
  })

  function addToCart(book: CartItem) {
    if (cart.value.find((item) => item.id === book.id)) {
      cart.value = cart.value.map((item) => {
        if (item.id === book.id) {
          return {
            ...item,
            quantity: item.quantity + 1,
          }
        }
        return item
      })
      return
    }
    cart.value.push(book)
  }

  function removeFromCart(id: number) {
    cart.value = cart.value.filter((item) => item.id !== id)
  }

  function register(data: Register): Promise<UserResponse> {
    return new Promise((resolve, reject) => {
      axiosClient.post<UserResponse>('/register', data)
        .then((response) => {
          token.value = response.data.token
          account.value = response.data.user
          resolve(response.data)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }

  function login(data: Login): Promise<UserResponse> {
    return new Promise((resolve, reject) => {
      axiosClient.post<UserResponse>('/login', data)
        .then((response) => {
          token.value = response.data.token
          account.value = response.data.user
          resolve(response.data)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }

  function updateUser(user: Update) {
    return new Promise((resolve, reject) => {
      axiosClient.defaults.headers.common.Authorization = `Bearer ${token.value}`

      axiosClient.put<UserResponse>('/profile/update', user)
        .then((response) => {
          token.value = response.data.token
          account.value = response.data.user
          resolve(response.data)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }

  function deleteAccount() {
    return new Promise((resolve, reject) => {
      axiosClient.defaults.headers.common.Authorization = `Bearer ${token.value}`

      axiosClient.delete<UserResponse>('/profile/delete')
        .then((response) => {
          token.value = ""
          account.value = null
          $reset()
          resolve(response.data)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }

  function $reset() {
    token.value = ''
    account.value = null
    axiosClient.defaults.headers.common.Authorization = ''
  }

  return { account, token, fullname, isAuthenticated, cart, cartCount, addToCart, removeFromCart, register, login, updateUser, deleteAccount, $reset }
}, { persist: true })
