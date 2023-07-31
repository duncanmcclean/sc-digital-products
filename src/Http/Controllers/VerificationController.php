<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\DigitalProducts\Http\Requests\VerificationRequest;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use Illuminate\Routing\Controller;

class VerificationController extends Controller
{
    public function index(VerificationRequest $request)
    {
        $order = Order::query()
            ->where('is_paid', '=', 'true') // make sure the order is paid
            ->where('items->metadata->license_key', '=', $request->license_key)
            ->limit(1) // explicitly set a limit to return faster in the SELECT
            ->first(); // first will call GET which will run the query and return the first result
        return $order === null ? 
            $this->invalidResponse($request) : $this->validResponse($request);
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
