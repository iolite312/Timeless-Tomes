import axiosClient from "~/axios"
import type { Account, Login, Register, UserResponse } from "~/types"

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
      console.log("Hi from below");
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
  function autoLogin() {
    if (!token.value) {
      return
    }

    axiosClient.defaults.headers.common.Authorization = `Bearer ${token.value}`

    axiosClient.get<UserResponse>('/me')
      .then((response) => {
        account.value = response.data.user
      })
      .catch(() => {
        token.value = ''
        account.value = null
      })
  }

  return { account, token, fullname, isAuthenticated, register, login, autoLogin }
}, { persist: true })
