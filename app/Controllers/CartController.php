<?php

namespace App\Controllers;

use App\Application\Request;
use App\Helpers\SearchHelper;
use App\Helpers\TokenHelper;
use App\Helpers\StripeHelper;
use App\Enums\PaymentStatusEnum;
use App\Repositories\BooksRepository;
use App\Repositories\OrderRepository;

class CartController extends Controller
{
    private OrderRepository $orderRepository;
    private StripeHelper $stripeHelper;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->stripeHelper = new StripeHelper();
    }

    public function createIntent()
    {
        $json = file_get_contents('php://input');
        $json = json_decode($json, true);
        $userid = TokenHelper::decode(Request::getAuthToken())->claims()->get('user')['id'];
        $stripe_customer = TokenHelper::decode(Request::getAuthToken())->claims()->get('user')['stripe_customer'];
        try {
            $order = $this->orderRepository->createOrder($json, $userid);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }
        if (!$order) {
            return [
                'status' => 500,
                'message' => 'Order could not be created',
            ];
        }
        $amount = StripeHelper::calculateOrderAmount($order->order_lines);
        $intent = $this->stripeHelper->createIntent($amount, $order->id, $stripe_customer);

        return [
            'status' => 200,
            'clientSecret' => $intent['clientSecret'],
        ];
    }

    public function webhook()
    {
        $payload = file_get_contents('php://input');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $_SERVER['HTTP_STRIPE_SIGNATURE'],
                $_ENV['STRIPE_WEBHOOK_SECRET']
            );
        } catch (\Exception $e) {
            return [
                'status' => 400,
                'message' => $e->getMessage(),
            ];
        }

        switch ($event->type) {
            case 'payment_intent.created':
                /** @var \Stripe\PaymentIntent $paymentIntent */
                $paymentIntent = $event->data->object;
                $this->orderRepository->updateOrderIntent($paymentIntent->metadata->order_id, $paymentIntent->id);
                break;
            case 'payment_intent.succeeded':
                /** @var \Stripe\PaymentIntent $paymentIntent */
                $paymentIntent = $event->data->object;
                $this->orderRepository->updateOrderStatus($paymentIntent->metadata->order_id, PaymentStatusEnum::COMPLETED);
                $this->orderRepository->updateStock($paymentIntent->metadata->order_id);
                $meili = SearchHelper::getInstance();
                $booksRepo = new BooksRepository();
                $order = $this->orderRepository->getOrderById($paymentIntent->metadata->order_id);
                foreach ($order->order_lines as $orderLine) {
                    $book = $booksRepo->getBookById($orderLine->book->id);
                    $meili->indexBook($book);
                }
                break;
            case 'payment_intent.payment_failed':
                /** @var \Stripe\PaymentIntent $paymentIntent */
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                $this->orderRepository->updateOrderStatus($paymentIntent->metadata->order_id, PaymentStatusEnum::FAILED);
                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }
    }

    public function checkAvailability()
    {
        $json = file_get_contents('php://input');
        $json = json_decode($json, true);
        try {
            $availability = $this->orderRepository->checkAvailability($json);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
        }

        $hasUnavailableBooks = false;
        foreach ($availability as $book) {
            if (!$book['availability']) {

                $hasUnavailableBooks = true;
                break;
            }
        }

        return [
            'status' => $hasUnavailableBooks ? 400 : 200,
            'message' => $availability,
        ];
    }
}
