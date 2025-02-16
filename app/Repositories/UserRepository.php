<?php

namespace App\Repositories;

use App\Models\User;
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
        $sql = 'INSERT INTO users (email, password, first_name, last_name, role, profile_picture) VALUES (:email, :password, :first_name, :last_name, :role, :profile_picture)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => RoleEnum::USER->value,
            'profile_picture' => 'profile_placeholder.png',
        ]);

        $userId = $this->pdo->lastInsertId();

        return $this->getUserById($userId);
    }

    public function getUserById(int $id): ?User
    {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user) {
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
            return new User($user);
        }

        return null;
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
}
