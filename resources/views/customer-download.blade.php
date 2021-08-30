Hi {{ $customer->name() }}, <br>

Your order, {{ $order->get('title') }} has some downloadable items. We've provided links to each of the items below.

@foreach($order->data()->get('items') as $item)
    <a href="{{ $item['metadata']['download_url'] }}">{{ \Statamic\Facades\Entry::find($item['product'])->title }}</a>
@endforeach

{{ config('app.name') }}
