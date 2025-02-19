export interface Book {
  id: number;
  title: string;
  description: string;
  picture: string;
  author: string;
  language: string;
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

export interface CartItem extends Book {
  quantity: number;
}

export interface BookResponse {
  status: number;
  book: Book;
}


