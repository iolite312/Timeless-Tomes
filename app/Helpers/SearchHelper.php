<?php

namespace App\Helpers;

use App\Models\Book;
use App\Repositories\BooksRepository;
use Meilisearch\Client;

class SearchHelper
{
    private Client $client;
    private string $indexName = 'books';
    private BooksRepository $booksRepository;
    private static SearchHelper $instance;

    public function __construct()
    {
        $this->booksRepository = new BooksRepository();
        $this->client = new Client($_ENV['MEILI_HOST_URL'], $_ENV['MEILI_MASTER_KEY']);
    }

    public static function getInstance(): SearchHelper
    {
        if (!isset(self::$instance)) {
            self::$instance = new SearchHelper();
        }

        self::$instance->configureIndex();

        return self::$instance;
    }

    public function configureIndex(): void
    {
        if ($this->indexExists()) {
            return;
        }

        $index = $this->client->index($this->indexName);

        $index->updateSearchableAttributes([
            'title',
            'author',
            'isbn',
        ]);

        $index->updateFilterableAttributes([
            'genre',
        ]);

        $this->client->updateIndex($this->indexName, ['primaryKey' => 'id']);

        $this->client->createKey([
            'name' => 'Search key',
            'description' => 'Search key',
            'actions' => [
                'search',
            ],
            'indexes' => [
                $this->indexName,
            ],
            'expiresAt' => null,
            'uid' => 'adc88eb5-8b1a-49d5-8b39-fb2d9692e374',
        ]);

        $books = $this->booksRepository->getAllBooks();
        $this->indexBooks($books);
    }

    public function indexBook(Book $book): void
    {
        $bookData = $book->toArray();

        $this->client->index($this->indexName)->addDocuments([$bookData]);
    }

    public function indexBooks(array $books): void
    {
        $booksData = array_map(function (Book $book) {
            return $book->toArray();
        }, $books);

        if (!empty($booksData)) {
            $this->client->index($this->indexName)->addDocuments($booksData);
        }
    }

    public function removeBook(int $bookId): void
    {
        $this->client->index($this->indexName)->deleteDocument($bookId);
    }

    public function indexExists(): bool
    {
        try {
            $this->client->getIndex($this->indexName);
        } catch (\Exception) {
            return false;
        }

        return true;
    }

    public function getSearchKey(): string
    {
        $key = $this->client->getKey($_ENV['MEILI_SEARCH_KEY']);

        return $key->getKey();
    }
}
