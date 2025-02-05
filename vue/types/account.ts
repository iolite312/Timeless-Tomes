export interface Account {
  id: number,
  email: string,
  first_name: string,
  last_name: string,
  role: Role,
  street?: string,
  city?: string,
}

export interface Login {
  email: string,
  password: string
}
export interface Register extends Login {
  firstname: string,
  lastname: string
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
