export interface Book {
  id: number;
  title: string;
  description: string;
  author: string;
  genre: Genre;
  isbn: string;
  price: number;
  stock: number;
  seller_id: number;
  seller_name: string;
}

export interface Genre {
  genres: string[];
}

export interface BookResponse {
  status: number;
  book: Book;
}


