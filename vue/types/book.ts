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

export interface CreateBook {
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
}

export interface UpdateBook extends CreateBook {
  id: number;
}

export interface CartItem {
  id: number;
  quantity: number;
  book?: Book | null;
}

export interface BookResponse {
  status: number;
  book: Book;
}

export interface BooksResponse {
  status: number;
  books: Book[];
}

export interface CreateOrder {
  first_name: string;
  last_name: string;
  street: string;
  city: string;
  postalcode: string;
}

export interface Order extends CreateOrder {
  id?: number;
  payment_status?: string;
  order_lines: CartItem[];
}

export enum PaymentStatus {
  COMPLETED = 'completed',
  PENDING = 'pending',
  FAILED = 'failed',
}

export interface OrderResponse {
  status: number;
  message: string | Array<CartItem>;
  orders?: Order[];
}


