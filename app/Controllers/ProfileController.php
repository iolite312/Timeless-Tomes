<?php

namespace App\Controllers;

use App\Helpers\FileHelper;
use App\Application\Request;
use App\Helpers\TokenHelper;
use App\Validation\UniqueRule;
use Rakit\Validation\Validator;
use App\Repositories\UserRepository;
use App\Repositories\OrderRepository;

class ProfileController extends Controller
{
    private UserRepository $userRepository;
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->orderRepository = new OrderRepository();
    }

    public function index()
    {
        $decodedToken = TokenHelper::decode(Request::getAuthToken());
        $user = $this->userRepository->getUserByEmail($decodedToken->claims()->get('user')['email']);
        if (!$user) {
            return [
                'status' => 404,
                'message' => 'User not found',
            ];
        }

        $jwtToken = TokenHelper::generateToken($user);

        return [
            'status' => 200,
            'user' => $user->toArray(),
            'token' => $jwtToken,
        ];
    }

    public function update()
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $decodedToken = TokenHelper::decode(Request::getAuthToken());

        try {
            $user = $this->userRepository->getUserByEmail($decodedToken->claims()->get('user')['email']);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        if (!$user) {
            return [
                'status' => 404,
                'message' => 'User not found',
            ];
        }

        $user->first_name = $data['first_name'] ?? $user->first_name;
        $user->last_name = $data['last_name'] ?? $user->last_name;
        $user->street = $data['street'] ?? $user->street;
        $user->city = $data['city'] ?? $user->city;
        $user->postalcode = $data['postalcode'] ?? $user->postalcode;
        if ($data['profile_picture']) {
            $result = FileHelper::saveFile($data['profile_picture'], 'profile');
            if ($result) {
                FileHelper::deleteFile($user->profile_picture, 'profile');
                $user->profile_picture = $result;
            }
        }
        if ($data['password']) {
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        try {
            $this->userRepository->updateUser($user);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        $jwtToken = TokenHelper::generateToken($user);

        return [
            'status' => 200,
            'message' => 'User updated',
            'token' => $jwtToken,
            'user' => $user->toArray(),
        ];
    }

    public function delete()
    {
        $decodedToken = TokenHelper::decode(Request::getAuthToken());
        $user = $this->userRepository->getUserByEmail($decodedToken->claims()->get('user')['email']);
        if (!$user) {
            return [
                'status' => 404,
                'message' => 'User not found',
            ];
        }
        try {
            $this->userRepository->deleteUser($user->id);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        return [
            'status' => 200,
            'message' => 'User deleted',
        ];
    }

    public function getAllOrders()
    {
        $decodedToken = TokenHelper::decode(Request::getAuthToken());
        $user = $this->userRepository->getUserByEmail($decodedToken->claims()->get('user')['email']);
        if (!$user) {
            return [
                'status' => 404,
                'message' => 'User not found',
            ];
        }
        try {
            $orders = $this->orderRepository->getOrdersByUserId($user->id);
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

    public function createSellerAccount()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $decodedToken = TokenHelper::decode(Request::getAuthToken());
        $user = $this->userRepository->getUserByEmail($decodedToken->claims()->get('user')['email']);

        if (!$user) {
            return [
                'status' => 404,
                'message' => 'User not found',
            ];
        }

        $data['user_id'] = $user->id;

        $validator = new Validator();
        $validator->addValidator('unique', new UniqueRule());

        $validation = $validator->validate($data, [
            'name' => 'required|unique:sellers,name|max:255',
            'user_id' => 'required|unique:sellers,user_id',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'error' => 'Name is already in use or application is pending',
            ];
        }

        try {
            $this->userRepository->createSeller($data);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        return [
            'status' => 200,
            'message' => 'Seller account created',
        ];
    }
}
