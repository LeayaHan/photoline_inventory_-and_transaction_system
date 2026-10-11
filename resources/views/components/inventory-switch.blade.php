{{--
    The Merchandise / Raw Materials switch.
    Clicking a side just reloads the page with ?item_type=... and the controller
    (or the form) shows only that type.

    On the inventory tables:
    <x-inventory-switch :current="$itemType" :low-counts="$lowCounts" route-name="staff.inventory" />

    On the "add item" form (no badges, but keeps type=add in the link):
    <x-inventory-switch :current="$itemType" route-name="staff.inventory.requests.create" :params="['type' => 'add']" />
--}}
@props(['current', 'routeName', 'lowCounts' => [], 'params' => []])

<link rel="stylesheet" href="{{ asset('css/inventory-switch.css') }}">

<div class="inv-switch">
    @foreach (\App\Models\InventoryItem::TYPES as $key => $label)
        <a href="{{ route($routeName, $params + ['item_type' => $key]) }}"
           class="inv-switch-btn {{ $current === $key ? 'is-on' : '' }}">
            {{ $label }}

            {{-- little badge when something on that side needs restocking --}}
            @if (($lowCounts[$key] ?? 0) > 0)
                <span class="inv-switch-badge" title="Low or out of stock">{{ $lowCounts[$key] }}</span>
            @endif
        </a>
    @endforeach
</div>

<p class="inv-switch-hint">{{ \App\Models\InventoryItem::DESCRIPTIONS[$current] }}</p>
