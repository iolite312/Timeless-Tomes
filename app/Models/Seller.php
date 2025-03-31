<?php

namespace App\Models;

class Seller extends User
{
    public int $seller_id;
    public string $seller_name;
    public string $user_id;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->seller_id = $data['seller_id'];
        $this->seller_name = $data['seller_name'];
        $this->user_id = $data['user_id'];
    }
}
