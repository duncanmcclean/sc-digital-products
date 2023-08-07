<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\DigitalProducts\Http\Requests\VerificationRequest;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderStatus;
use Illuminate\Routing\Controller;

class VerificationController extends Controller
{
    public function index(VerificationRequest $request)
    {
        $orders = collect(Order::all())
            ->filter(function ($order) {
                return in_array($order->get('order_status'), [
                    OrderStatus::Placed->value,
                    OrderStatus::Dispatched->value,
                ]);
            })
            ->map(function ($order) use ($request) {
                foreach ($order->get('items') as $item) {
                    if (isset($item['metadata']['license_key']) && $item['metadata']['license_key'] === $request->license_key) {
                        return ['result' => true];
                    }
                }

                return ['result' => false];
            })
            ->where('result', true)
            ->flatten()
            ->toArray();

        if (isset($orders[0])) {
            return $this->validResponse($request);
        }

        return $this->invalidResponse($request);
    }

    protected function validResponse($request)
    {
        return [
            'license_key' => $request->license_key,
            'valid' => true,
        ];
    }

    protected function invalidResponse($request)
    {
        return [
            'license_key' => $request->license_key,
            'valid' => false,
        ];
    }
}
