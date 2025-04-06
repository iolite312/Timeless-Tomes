<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Seller;
use App\Enums\RoleEnum;

class UserRepository extends DatabaseRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = $this->getConnection();
    }

    public function createUser(array $data): User
    {
        $sql = 'INSERT INTO users (email, password, first_name, last_name, role, profile_picture, stripe_customer) VALUES (:email, :password, :first_name, :last_name, :role, :profile_picture, :stripe_customer)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => RoleEnum::USER->value,
            'profile_picture' => 'profile_placeholder.png',
            'stripe_customer' => $data['stripe_customer'],
        ]);

        $userId = $this->pdo->lastInsertId();

        return $this->getUserById($userId);
    }

    public function getUserById(int $id): User|Seller|null
    {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user) {
            $seller = $this->getSellerByUserId($id);
            if ($seller) {
                $user = array_merge($user, $seller);

                return new Seller($user);
            }

            return new User($user);
        }

        return null;
    }

    public function getUserByEmail(string $email): ?User
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user) {
            $seller = $this->getSellerByUserId($user['id']);
            if ($seller) {
                $user = array_merge($user, $seller);

                return new Seller($user);
            }

            return new User($user);
        }

        return null;
    }

    private function getSellerByUserId(int $id): ?array
    {
        $sql = 'SELECT id AS "seller_id", name AS "seller_name", user_id, approved FROM sellers WHERE user_id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $seller = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($seller) {
            return $seller;
        }

        return null;
    }

    public function getSellerById(int $id): ?array
    {
        $sql = 'SELECT id AS "seller_id", name AS "seller_name", user_id FROM sellers WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $seller = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($seller) {
            return $seller;
        }

        return null;
    }

    public function getAllUsers(): array
    {
        $sql = 'SELECT * FROM users ORDER BY last_name ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($user) {
            $seller = $this->getSellerByUserId($user['id']);
            if ($seller) {
                $user = array_merge($user, $seller);

                return new Seller($user);
            }

            return new User($user);
        }, $users);
    }

    public function updateUser(User $user): bool
    {
        $sql = 'UPDATE users SET email = :email, password = :password, first_name = :first_name, last_name = :last_name, role = :role, profile_picture = :profile_picture, street = :street, city = :city, postalcode = :postalcode WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $user->email,
            'password' => $user->password,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role->value,
            'profile_picture' => $user->profile_picture,
            'street' => $user->street,
            'city' => $user->city,
            'postalcode' => $user->postalcode,
            'id' => $user->id,
        ]);

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;

    }

    public function deleteUser(int $id): bool
    {
        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public function createSeller(array $data): bool
    {
        $sql = 'INSERT INTO sellers (name, user_id) VALUES (:name, :user_id)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'user_id' => $data['user_id'],
        ]);

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public function getNonApprovedSellers(): array
    {
        $sql = 'SELECT * FROM sellers WHERE approved = 0';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $sellers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($seller) {
            return $this->getUserById($seller['user_id']);
        }, $sellers);
    }

    public function approveSeller(int $id): bool
    {
        $sql = 'UPDATE sellers SET approved = 1 WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() == 0) {
            return false;
        }

        $sql = 'SELECT user_id FROM sellers WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user_id = $stmt->fetch(\PDO::FETCH_ASSOC);

        $sql = 'UPDATE users SET role = :role WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'role' => RoleEnum::SELLER->value,
            'id' => $user_id['user_id'],
        ]);

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }
}
