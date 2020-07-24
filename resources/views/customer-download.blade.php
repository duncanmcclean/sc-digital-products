Hi {{ $customer['name'] }}, <br>

Your order, {{ $cart['title'] }} has some downloadable items. We've provided links to each of the items below. 

@foreach($cart['items'] as $item)
    <a href="{{ $item['download_url'] }}">{{ \Statamic\Facades\Entry::find($item['product'])->title }}</a>
@endforeach

{{ config('app.name') }}