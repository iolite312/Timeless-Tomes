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
        $sql = 'INSERT INTO books (title, description, picture, author, language, genre, isbn, price, stock, seller_id) VALUES (:title, :description, :picture, :author, :language, :genre, :isbn, :price, :stock, :seller_id)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'picture' => $data['picture'],
            'author' => $data['author'],
            'language' => $data['language'],
            'genre' => json_encode($data['genre']),
            'isbn' => $data['isbn'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'seller_id' => $data['seller_id'],
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

    public function getAllBooks(): array
    {
        $sql = 'SELECT B.*, S.name AS "seller_name" FROM books AS B JOIN sellers AS S ON S.id = B.seller_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $books = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $bookArray = [];
        foreach ($books as $book) {
            $bookArray[] = new Book($book);
        }

        return $bookArray;
    }

    public function getAllBooksBySellerId(int $seller_id): array
    {
        $sql = 'SELECT B.*, S.name AS "seller_name" FROM books AS B JOIN sellers AS S ON S.id = B.seller_id WHERE B.seller_id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $seller_id]);
        $books = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $bookArray = [];
        foreach ($books as $book) {
            $bookArray[] = new Book($book);
        }

        return $bookArray;
    }

    public function checkBookExistsInOrder(int $id): bool
    {
        $sql = 'SELECT * FROM orders_books WHERE book_id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $book = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($book) {
            return true;
        }

        return false;
    }

    public function updateBook(Book $book): bool
    {
        $sql = 'UPDATE books SET title = :title, description = :description, picture = :picture, author = :author, language = :language, genre = :genre, isbn = :isbn, price = :price, stock = :stock WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $book->title,
            'description' => $book->description,
            'picture' => $book->picture,
            'author' => $book->author,
            'language' => $book->language,
            'genre' => json_encode($book->genre),
            'isbn' => $book->isbn,
            'price' => $book->price,
            'stock' => $book->stock,
            'id' => $book->id,
        ]);

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public function deleteBookById(int $id): void
    {
        $sql = 'DELETE FROM books WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
