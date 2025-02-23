<?php

namespace App\Models;

class OrderLine extends Model
{
    public int $orderId;
    public Book $book;
    public int $quantity;

    public array $hidden = ['orderId'];

    public function __construct(array $data)
    {
        $this->orderId = $data['order_id'];
        $this->book = new Book($data);
        $this->quantity = $data['quantity'];
    }
}
