@component('mail::message')
# Digital Downloads Ready

Your order, **#{{ $order->orderNumber() }}** has some downloadable items. We've provided links to each of the items below.

## Downloads

@component('mail::table')
| Items       | Download      |
| :--------- | :------------- |
@foreach ($order->lineItems() as $lineItem)
@php
$product = \DoubleThreeDigital\SimpleCommerce\Facades\Product::find($lineItem['product']);
@endphp
| [{{ $product->get('title') }}]({{ optional($product->resource())->absoluteUrl() }}) | [Download]({{ $lineItem['metadata']['download_url'] }}) |
@endforeach
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
