import axiosClient from "~/axios"
import type { Book, BookResponse, CreateBook, UpdateBook } from "~/types"

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

  function updateBook(data: UpdateBook): Promise<Book> {
    return new Promise((resolve, reject) => {
      axiosClient.put<BookResponse>(`/seller/products/${data.id}`, data)
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
  return { createBook, updateBook, deleteBook }
})
