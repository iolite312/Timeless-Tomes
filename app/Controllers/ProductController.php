<?php

namespace App\Controllers;

use App\Application\Request;
use App\Repositories\BooksRepository;

class ProductController extends Controller
{
    private BooksRepository $booksRepository;
    public function __construct()
    {
        $this->booksRepository = new BooksRepository();
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
                "status" => 500,
                "message" => "Something went wrong"
            ];
        }

        if (!$book) {
            return [
                "status" => 404,
                "message" => "Book not found"
            ];
        }

        return [
            "status" => 200,
            "book" => $book->toArray()
        ];
    }
}