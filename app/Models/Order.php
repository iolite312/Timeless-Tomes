<?php

namespace App\Models;

class Order extends Model
{
    public int $id;
    public string $first_name;
    public string $last_name;
    public string $street;
    public string $city;
    public string $postalcode;
    public int $user_id;
    public ?string $stripe_intent;
    public string $payment_status;
    public string $created_at;
    public array $order_lines;
    public array $hidden = ['user_id'];

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->street = $data['street'];
        $this->city = $data['city'];
        $this->postalcode = $data['postalcode'];
        $this->user_id = $data['user_id'];
        $this->stripe_intent = $data['stripe_intent'] ?? null;
        $this->payment_status = $data['payment_status'];
        $this->created_at = $data['created_at'];
    }
}
