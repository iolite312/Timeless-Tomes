import axiosClient from "~/axios"
import type { Account, Login, Register, Update, UserResponse } from "~/types"

export const useAccountStore = defineStore('account', () => {
  const account = ref<Account | null>(null)
  const token = ref<string>('')

  const fullname = computed(() => {
    return `${account.value?.first_name} ${account.value?.last_name}`
  })
  const isAuthenticated = computed(() => {
    return !!token.value
  })

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

      axiosClient.post<UserResponse>('/profile/update', user)
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

      axiosClient.post<UserResponse>('/profile/delete')
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

  return { account, token, fullname, isAuthenticated, register, login, autoLogin, updateUser, deleteAccount, $reset }
}, { persist: true })
