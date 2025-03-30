<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;

class AdminController extends Controller
{
    private UserRepository $userRepository;
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->orderRepository = new OrderRepository();
    }

    public function getAllUsers(): array
    {
        try {
            $users = $this->userRepository->getAllUsers();
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }
        return [
            'status' => 200,
            'users' => $users ? array_map(function ($user) {
                return $user->toArray();
            }, $users) : [],
        ];
    }
}