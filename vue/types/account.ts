export interface Account {
  id: number,
  email: string,
  first_name: string,
  last_name: string,
  role: Role,
  profile_picture: string,
  street?: string,
  city?: string,
  postalcode?: string,
  seller_name?: string
  seller_id?: number
}

export interface AccountResponse {
  status: number,
  users: Account[],
}

export interface Update {
  first_name: string,
  last_name: string,
  email: string,
  password: string,
  profile_picture: string,
  street?: string,
  city?: string,
  postalcode?: string,
}

export interface Login {
  email: string,
  password: string
}
export interface Register extends Login {
  first_name: string,
  last_name: string
}
export interface UserResponse {
  status: number,
  user: Account,
  token: string
}

export enum Role {
  ADMIN = 'admin',
  SELLER = 'seller',
  USER = 'user',
}
