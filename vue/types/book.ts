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

export interface CreateOrder {
  first_name: string;
  last_name: string;
  street: string;
  city: string;
  postalcode: string;
}

export interface Order extends CreateOrder {
  orderlines: CartItem[];
}

export interface OrderResponse {
  status: number;
  message: string | Array<CartItem>;
}


