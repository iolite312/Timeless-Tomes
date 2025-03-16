export interface Book {
  id: number;
  title: string;
  description: string;
  picture: string;
  author: string;
  language: string;
  genre: string[];
  isbn: string;
  price: number;
  stock: number;
  seller_id: number;
  seller_name: string;
}

export interface CartItem {
  id: number;
  quantity: number;
}

export interface BookResponse {
  status: number;
  book: Book;
}

export interface Order {
  first_name: string;
  last_name: string;
  street: string;
  city: string;
  postalcode: string;
  orderlines: CartItem[];
}


