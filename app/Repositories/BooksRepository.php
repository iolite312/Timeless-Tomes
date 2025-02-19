<?php

namespace App\Repositories;

use App\Models\Book;

class BooksRepository extends DatabaseRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = $this->getConnection();
    }
    public function create(array $data)
    {
        $sql = "INSERT INTO books (title, description, author, genre, isbn, price, stock, seller_id) VALUES (:title, :description, :author, :genre, :isbn, :price, :stock, :seller_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'author' => $data['author'],
            'genre' => json_encode($data['genre']),
            'isbn' => $data['isbn'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'seller_id' => $data['seller_id']
        ]);

        $bookId = $this->pdo->lastInsertId();

        return $this->getBookById($bookId);
    }
    public function getBookById(int $id): ?Book
    {
        $sql = 'SELECT B.*, S.name AS "seller_name" FROM books AS B JOIN sellers AS S ON S.id = B.seller_id WHERE B.id = :id ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $book = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($book) {
            return new Book($book);
        }

        return null;
    }
}