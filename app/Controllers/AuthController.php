<?php

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Helpers\StripeHelper;
use App\Validation\UniqueRule;
use Rakit\Validation\Validator;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $validator = new Validator();
        $validator->addValidator('unique', new UniqueRule());

        $validation = $validator->validate($data, [
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:8',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->firstOfAll(),
            ];
        }

        $data = [
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ];

        try {
            $stripe = new StripeHelper();
            $data['stripe_customer'] = $stripe->createCustomer($data['email'], $data['first_name'] . ' ' . $data['last_name']);
            $user = $this->userRepository->createUser($data);
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        $jwtToken = TokenHelper::generateToken($user);

        return [
            'status' => 201,
            'user' => $user->toArray(),
            'token' => $jwtToken,
        ];
    }

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();

        $validation = $validator->validate($data, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        try {
            $user = $this->userRepository->getUserByEmail($data['email']);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        if (!$user || !password_verify($data['password'], $user->password)) {
            return [
                'status' => 401,
                'error' => 'Invalid credentials',
            ];
        }

        $jwtToken = TokenHelper::generateToken($user);

        return [
            'token' => $jwtToken,
            'user' => $user->toArray(),
        ];
    }
}
