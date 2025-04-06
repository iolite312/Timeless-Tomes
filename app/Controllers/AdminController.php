<?php

namespace App\Controllers;

use App\Application\Request;
use App\Repositories\UserRepository;
use App\Repositories\OrderRepository;

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

    public function getAllOrders(): array
    {
        try {
            $orders = $this->orderRepository->getAllOrders();
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        return [
            'status' => 200,
            'orders' => $orders ? array_map(function ($order) {
                return $order->toArray();
            }, $orders) : [],
        ];
    }

    public function getNonApprovedSellers(): array
    {
        try {
            $sellers = $this->userRepository->getNonApprovedSellers();
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        return [
            'status' => 200,
            'users' => $sellers ? array_map(function ($seller) {
                return $seller->toArray();
            }, $sellers) : [],
        ];
    }

    public function approveSeller(): array
    {
        try {
            $result = $this->userRepository->approveSeller(Request::getParam('id'));
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        if (!$result) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        return [
            'status' => 200,
            'message' => 'Seller approved',
        ];
    }
}
