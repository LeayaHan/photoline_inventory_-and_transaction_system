@extends('layouts.panel')

@section('title', 'Replenishment')

@push('styles')
<style>
    .rep-summary { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 22px; }
    .rep-stat {
        background: #fff;
        border: 1px solid var(--pl-line);
        border-radius: 10px;
        padding: 14px 20px;
        flex: 1 1 140px;
        max-width: 220px;
    }
    .rep-stat strong { display: block; font-size: 28px; line-height: 1.1; }
    .rep-stat span { color: var(--pl-muted); font-size: 13px; }
    .rep-stat.out strong { color: var(--pl-red); }
    .rep-stat.low strong { color: var(--pl-orange); }

    .rep-card { padding: 0; }
    /* Keep the columns readable on phones: the table scrolls sideways instead of squeezing. */
    .rep-card table { margin: 0; min-width: 700px; }
    .rep-card th, .rep-card td { padding: 13px 16px; vertical-align: middle; }
    .rep-card th { text-transform: none; letter-spacing: 0; font-size: 13px; }
    .rep-card tbody tr:last-child td { border-bottom: 0; }
    .rep-card tr.is-out td { background: #fff8f8; }
    .rep-card .num { text-align: right; white-space: nowrap; }
    .rep-card .item-name { font-weight: 600; }
    .rep-card .item-sku { color: var(--pl-muted); font-size: 12px; }
    .rep-card .stock { font-weight: 600; }
    .rep-card .stock.out { color: var(--pl-red); }
    .rep-card .stock.low { color: var(--pl-orange); }

    .rep-empty { text-align: center; padding: 48px 20px; }
    .rep-empty h2 { margin: 0 0 6px; font-size: 18px; }
    .rep-empty p { margin: 0; color: var(--pl-muted); }
</style>
@endpush

@section('content')
@php
    $outCount = $products->filter(fn ($p) => (int) $p->quantity <= 0)->count();
    $lowCount = $products->count() - $outCount;
@endphp

<div class="container">

    <div class="page-header">
        <div>
            <h1>Replenishment</h1>
            <p>Items at or below their reorder level, lowest stock first.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn gray">Manage inventory</a>
    </div>

    @if($products->isNotEmpty())
        <div class="rep-summary">
            <div class="rep-stat out">
                <strong>{{ $outCount }}</strong>
                <span>Out of stock</span>
            </div>
            <div class="rep-stat low">
                <strong>{{ $lowCount }}</strong>
                <span>Low stock</span>
            </div>
        </div>
    @endif

    <div class="card table-wrap rep-card">
        @if($products->isEmpty())
            <div class="rep-empty">
                <h2>Nothing to restock</h2>
                <p>Every item is above its reorder level.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th class="num">In stock</th>
                        <th class="num">Reorder level</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        @php $isOut = (int) $product->quantity <= 0; @endphp
                        <tr class="{{ $isOut ? 'is-out' : '' }}">
                            <td>
                                <div class="item-name">{{ $product->product_name }}</div>
                                <div class="item-sku">{{ $product->sku }}</div>
                            </td>
                            <td>{{ $product->category }}</td>
                            <td class="num">
                                <span class="stock {{ $isOut ? 'out' : 'low' }}">{{ $product->quantity }}</span>
                                {{ $product->unit }}
                            </td>
                            <td class="num">{{ $product->reorder_level }}</td>
                            <td>
                                @if($isOut)
                                    <span class="badge red">Out of stock</span>
                                @else
                                    <span class="badge orange">Low stock</span>
                                @endif
                            </td>
                            <td class="num">
                                <a href="{{ route('products.edit', $product) }}" class="btn small">Update stock</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection