<?php

namespace App\Models;

use App\Enums\RoleEnum;

class User extends Model
{
    public int $id;
    public string $email;
    public string $password;
    public string $first_name;
    public string $last_name;
    public RoleEnum $role;
    public ?string $street;
    public ?string $city;
    public ?string $postalcode;
    public string $created_at;

    public array $hidden = ['password'];

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->role = RoleEnum::from($data['role']);
        $this->street = $data['street'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->postalcode = $data['postalcode'] ?? null;
        $this->created_at = $data['created_at'];
    }
}
