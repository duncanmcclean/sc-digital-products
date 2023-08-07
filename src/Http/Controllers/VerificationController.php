<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\DigitalProducts\Http\Requests\VerificationRequest;
use DoubleThreeDigital\SimpleCommerce\Orders\EloquentOrderRepository;
use DoubleThreeDigital\SimpleCommerce\Orders\EntryOrderRepository;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderStatus;
use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use Illuminate\Routing\Controller;
use Statamic\Facades\Entry;

class VerificationController extends Controller
{
    public function index(VerificationRequest $request)
    {
        if ($this->isOrExtendsClass(SimpleCommerce::orderDriver()['repository'], EntryOrderRepository::class)) {
            $orderQuery = Entry::query()
                ->where('collection', SimpleCommerce::orderDriver()['collection'])
                ->whereIn('order_status', [
                    OrderStatus::Placed->value,
                    OrderStatus::Dispatched->value,
                ])
                ->where('items->0->metadata->license_key', $request->license_key)
                ->orWhere('items->1->metadata->license_key', $request->license_key)
                ->orWhere('items->2->metadata->license_key', $request->license_key)
                ->orWhere('items->3->metadata->license_key', $request->license_key)
                ->orWhere('items->4->metadata->license_key', $request->license_key)
                ->orWhere('items->5->metadata->license_key', $request->license_key)
                ->orWhere('items->6->metadata->license_key', $request->license_key)
                ->orWhere('items->7->metadata->license_key', $request->license_key)
                ->orWhere('items->8->metadata->license_key', $request->license_key)
                ->orWhere('items->9->metadata->license_key', $request->license_key)
                ->limit(1)
                ->get();
        }

        if ($this->isOrExtendsClass(SimpleCommerce::orderDriver()['repository'], EloquentOrderRepository::class)) {
            $orderModel = new (SimpleCommerce::orderDriver()['model']);

            $orderQuery = $orderModel::query()
                ->whereIn('order_status', [
                    OrderStatus::Placed->value,
                    OrderStatus::Dispatched->value,
                ])
                ->whereRaw("JSON_EXTRACT(items, '$[0].metadata.license_key') = ?", [$request->license_key])
                ->limit(1)
                ->get();
        }

        return $orderQuery->count() > 0
            ? $this->validResponse($request)
            : $this->invalidResponse($request);
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

    protected function isOrExtendsClass(string $class, string $classToCheckAgainst): bool
    {
        return is_subclass_of($class, $classToCheckAgainst)
            || $class === $classToCheckAgainst;
    }
}
