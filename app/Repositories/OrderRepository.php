<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderLine;
use App\Enums\PaymentStatusEnum;

class OrderRepository extends DatabaseRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = $this->getConnection();
    }

    public function getOrderById(int $id): ?Order
    {
        $sql = 'SELECT * FROM orders WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($order) {
            $tempOrder = new Order($order);
            $tempOrder->order_lines = $this->getOrderlinesByOrderId($id);

            return $tempOrder;
        }

        return null;
    }

    public function getOrdersByUserId(int $id): ?array
    {
        $sql = 'SELECT * FROM orders WHERE user_id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $orderArray = null;
        if ($orders) {
            foreach ($orders as $order) {
                $tempOrder = new Order($order);
                $tempOrder->order_lines = $this->getOrderlinesByOrderId($order['id']);
                $orderArray[] = $tempOrder;
            }
        }

        return $orderArray;
    }

    private function getOrderlinesByOrderId(int $id): array
    {
        $sql = 'SELECT OB.order_id, B.id AS book_id, B.title, B.description, B.picture, B.author, B.language, B.genre, B.isbn, B.price, B.seller_id, S.name AS seller_name, OB.quantity 
                FROM orders_books AS OB 
                JOIN books AS B ON B.id = OB.book_id 
                JOIN sellers AS S ON S.id = B.seller_id 
                WHERE OB.order_id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $orderlines = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $orderLinesArray = [];
        foreach ($orderlines as $orderline) {
            $orderLinesArray[] = new OrderLine($orderline);
        }
        return $orderLinesArray;
    }

    public function createOrder(array $data, int $userId): ?Order
    {
        try {
            $this->pdo->beginTransaction();
            $sql = 'INSERT INTO orders (first_name, last_name, street, city, postalcode, user_id) VALUES (:first_name, :last_name, :street, :city, :postalcode, :user_id)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'street' => $data['street'],
                'city' => $data['city'],
                'postalcode' => $data['postalcode'],
                'user_id' => $userId,
            ]);
            $orderId = $this->pdo->lastInsertId();
            //TODO: make it only subtract if payment is successful
            foreach ($data['orderlines'] as $orderline) {
                $sql = 'INSERT INTO orders_books (order_id, book_id, quantity) VALUES (:order_id, :book_id, :quantity)';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'order_id' => $orderId,
                    'book_id' => $orderline['id'],
                    'quantity' => $orderline['quantity'],
                ]);
                $sql = 'UPDATE books SET stock = stock - 1 WHERE id = :id';
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'id' => $orderline['id'],
                ]);
            }
            $this->pdo->commit();

            return $this->getOrderById($orderId);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateOrderIntent(int $orderId, string $intent): void
    {
        $sql = 'UPDATE orders SET stripe_intent = :intent WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'intent' => $intent,
            'id' => $orderId,
        ]);
    }

    public function updateOrderStatus(int $orderId, PaymentStatusEnum $status): void
    {
        $sql = 'UPDATE orders SET payment_status = :status WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'status' => $status->value,
            'id' => $orderId,
        ]);
    }

    public function checkAvailability(array $data): array
    {
        $availablity = [];
        foreach ($data['orderlines'] as $orderline) {
            $sql = 'SELECT stock - :quantity AS availability FROM books WHERE id = :book_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'quantity' => $orderline['quantity'],
                'book_id' => $orderline['id'],
            ]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $availablity[] = [
                'id' => $orderline['id'],
                'availability' => $result['availability'] >= 0,
            ];
        }

        return $availablity;
    }
}
