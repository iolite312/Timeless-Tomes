<?php

namespace App\Controllers;

use App\Enums\RoleEnum;
use App\Helpers\FileHelper;
use App\Application\Request;
use App\Helpers\TokenHelper;
use App\Helpers\SearchHelper;
use App\Repositories\UserRepository;
use App\Repositories\BooksRepository;

class ProductController extends Controller
{
    private BooksRepository $booksRepository;
    private UserRepository $userRepository;
    private SearchHelper $searchHelper;

    public function __construct()
    {
        $this->booksRepository = new BooksRepository();
        $this->userRepository = new UserRepository();
        $this->searchHelper = SearchHelper::getInstance();
    }

    public function index()
    {
        return ['message' => 'Hello World'];
    }

    public function show()
    {
        try {
            $book = $this->booksRepository->getBookById(Request::getParam('id'));
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        if (!$book) {
            return [
                'status' => 404,
                'message' => 'Book not found',
            ];
        }

        return [
            'status' => 200,
            'book' => $book->toArray(),
        ];
    }

    public function getAllProducts()
    {
        $user = TokenHelper::decode(Request::getAuthToken())->claims()->get('user');
        try {
            if (!Request::getUrlParam('id') && RoleEnum::from($user['role']) === RoleEnum::ADMIN) {
                $books = $this->booksRepository->getAllBooks();
            } else {
                $seller = $this->userRepository->getSellerById(Request::getUrlParam('id'));
                if ($seller['user_id'] !== $user['id'] && RoleEnum::from($user['role']) !== RoleEnum::ADMIN) {
                    return [
                        'status' => 403,
                        'message' => 'Forbidden',
                    ];
                }
                $books = $this->booksRepository->getAllBooksBySellerId(Request::getUrlParam('id'));
            }
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        return [
            'status' => 200,
            'books' => $books ? array_map(fn ($book) => $book->toArray(), $books) : [],
        ];
    }

    public function createProduct()
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if ($data['picture']) {
            $result = FileHelper::saveFile($data['picture'], 'books');
            if ($result) {
                $data['picture'] = $result;
            }
        }

        try {
            $book = $this->booksRepository->create($data);
            $this->searchHelper->indexBook($book);
        } catch (\Exception $e) {
            FileHelper::deleteFile($data['picture'], 'books');

            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        return [
            'status' => 201,
            'book' => $book->toArray(),
        ];
    }

    public function deleteProduct()
    {
        try {
            $book = $this->booksRepository->getBookById(Request::getParam('id'));
            if (!$book) {
                return [
                    'status' => 404,
                    'message' => 'Book not found',
                ];
            }
            $bookInOrder = $this->booksRepository->checkBookExistsInOrder(Request::getParam('id'));
            if ($bookInOrder) {
                return [
                    'status' => 400,
                    'message' => 'Book is in order',
                ];
            }
            $this->booksRepository->deleteBookById(Request::getParam('id'));
            $this->searchHelper->removeBook(Request::getParam('id'));
            FileHelper::deleteFile($book->picture, 'books');
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        return [
            'status' => 200,
            'message' => 'Book deleted',
        ];
    }

    public function updateProduct()
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        try {
            $book = $this->booksRepository->getBookById(Request::getParam('id'));
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        if (!$book || ($book->seller_id !== TokenHelper::decode(Request::getAuthToken())->claims()->get('user')['seller_id'] && RoleEnum::from(TokenHelper::decode(Request::getAuthToken())->claims()->get('user')['role']) !== RoleEnum::ADMIN)) {
            return [
                'status' => 404,
                'message' => 'Book not found',
            ];
        }

        $book->title = $data['title'] ?? $book->title;
        $book->description = $data['description'] ?? $book->description;
        $book->author = $data['author'] ?? $book->author;
        $book->language = $data['language'] ?? $book->language;
        $book->genre = $data['genre'] ?? $book->genre;
        $book->isbn = $data['isbn'] ?? $book->isbn;
        $book->price = $data['price'] ?? $book->price;
        $book->stock = $data['stock'] ?? $book->stock;

        if ($data['picture'] !== $book->picture) {
            $result = FileHelper::saveFile($data['picture'], 'books');
            if ($result) {
                FileHelper::deleteFile($book->picture, 'books');
                $book->picture = $result;
            }
        }

        try {
            $this->booksRepository->updateBook($book);
            $this->searchHelper->indexBook($book);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        return [
            'status' => 200,
            'message' => 'Book updated',
            'book' => $book->toArray(),
        ];
    }

    public function searchKey()
    {
        return [
            'status' => 200,
            'key' => $this->searchHelper->getSearchKey(),
        ];
    }
}
