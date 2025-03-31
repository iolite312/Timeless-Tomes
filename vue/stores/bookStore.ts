import axiosClient from "~/axios"
import type { Book, BookResponse, CreateBook } from "~/types"

export const useBookStore = defineStore('book', () => {
  function createBook(data: CreateBook): Promise<Book> {
    return new Promise((resolve, reject) => {
      axiosClient.post<BookResponse>('/seller/products', data)
        .then((response) => {
          resolve(response.data.book)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }

  function deleteBook(id: number) {
    return new Promise((resolve, reject) => {
      axiosClient.delete(`/seller/products/${id}`)
        .then((response) => {
          resolve(response.data)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }
  return { createBook, deleteBook }
})
