<x-staff-layout title="New Request">
    <div class="staffui-pagehead">
        <div>
            <h1>{{ \App\Models\InventoryRequest::TYPES[$type] }}</h1>
            <p>{{ \App\Models\InventoryItem::TYPES[$itemType] }}</p>
        </div>

    </div>

    @if ($errors->any())
        <div class="staffui-alert staffui-alert-error">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('staff.inventory.requests.store') }}" method="POST" class="staffui-formcard">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="item_type" value="{{ $itemType }}">

        {{-- new items: pick Merchandise or Raw Materials first (this reloads the form with the right categories) --}}
        @if ($type === 'add')
            <x-inventory-switch :current="$itemType" route-name="staff.inventory.requests.create" :params="['type' => 'add']" :hint="false" />
        @endif

        @if ($item)
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <div class="staffui-itembox">
                <strong>{{ $item->name }}</strong>
                {{ $item->item_code }} &middot; {{ $item->quantity }} {{ $item->unit }} in stock
            </div>
        @endif

        @if ($type === 'add' || $type === 'edit')
            <div class="staffui-grid">
                <div class="staffui-field">
                    <label for="name">Item Name </label>
                    <input id="name" type="text" name="name" value="{{ old('name', $item?->name) }}" required>
                </div>

                <div class="staffui-field">
                    <label for="category">Category </label>
                    <select id="category" name="category" required>
                        <option value="" disabled {{ old('category', $item?->category) ? '' : 'selected' }}>Select category</option>
                        @foreach (\App\Models\InventoryItem::CATEGORIES[$itemType] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $item?->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="staffui-field">
                    <label for="unit">Unit </label>
                    @php
                        $currentUnit = old('unit', $item?->unit ?? 'pcs');
                        $unitOptions = \App\Models\InventoryItem::UNITS;
                        // an older item with its own spelling keeps working until someone changes it
                        if (! in_array($currentUnit, $unitOptions, true)) {
                            $unitOptions[] = $currentUnit;
                        }
                    @endphp
                    <select id="unit" name="unit" required>
                        @foreach ($unitOptions as $unit)
                            <option value="{{ $unit }}" {{ $currentUnit === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="staffui-field">
                    <label for="unit_price">{{ \App\Models\InventoryItem::PRICE_LABELS[$itemType] }} (₱) </label>
                    <input
                        id="unit_price"
                        type="number" step="0.01" min="0"
                        name="unit_price"
                        value="{{ old('unit_price', $item?->unit_price ?? '0.00') }}" required>
                </div>

                @if ($type === 'add')
                    <div class="staffui-field">
                        <label for="quantity">Opening Stock </label>
                        <input id="quantity" type="number" min="0" name="quantity" value="{{ old('quantity', 0) }}" required>
                    </div>
                @endif

                <div class="staffui-field">
                    <label for="reorder_level">Reorder Level </label>
                    <input
                        id="reorder_level"
                        type="number" min="0"
                        name="reorder_level"
                        value="{{ old('reorder_level', $item?->reorder_level ?? 5) }}" required>
                </div>

                <div class="staffui-field is-wide">
                    <label for="description">Description</label>
                    <textarea id="description" name="description">{{ old('description', $item?->description) }}</textarea>
                </div>

                <div class="staffui-field">
                    <label for="expiry_date">Expiry Date <span>(optional)</span></label>
                    <input id="expiry_date" type="date" name="expiry_date" value="{{ old('expiry_date', $item?->expiry_date?->format('Y-m-d')) }}">
                </div>

                <div class="staffui-field is-wide">
                    <label for="reason">Reason {!! $type === 'edit' ? '' : '(optional)' !!}</label>
                    <textarea
                        id="reason"
                        name="reason" {{ $type === 'edit' ? 'required' : '' }}
                        placeholder="Why is this needed?">{{ old('reason') }}</textarea>
                </div>
            </div>
        @elseif ($type === 'stock')
            <div class="staffui-grid">
                <div class="staffui-field">
                    <label for="direction">Action </label>
                    <select id="direction" name="direction" required>
                        <option value="in" {{ old('direction') === 'in' ? 'selected' : '' }}>Stock in (add)</option>
                        <option value="out" {{ old('direction') === 'out' ? 'selected' : '' }}>Stock out (remove)</option>
                    </select>
                </div>

                <div class="staffui-field">
                    <label for="quantity">Quantity ({{ $item->unit }}) </label>
                    <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity') }}" required>
                </div>

                <div class="staffui-field is-wide">
                    <label for="reason">Reason </label>
                    <textarea
                        id="reason"
                        name="reason" required
                        placeholder="e.g. New delivery received, damaged items, used for an order">{{ old('reason') }}</textarea>
                </div>
            </div>
        @else
            <p class="staffui-note">You are asking the manager to remove this item from the inventory.</p>

            <div class="staffui-field">
                <label for="reason">Reason </label>
                <textarea
                    id="reason"
                    name="reason" required
                    placeholder="Why should this item be removed?">{{ old('reason') }}</textarea>
            </div>
        @endif

        <div class="staffui-formactions">
            <a href="{{ route('staff.inventory', ['item_type' => $itemType]) }}" class="staffui-btn staffui-btn-outline">Cancel</a>
            <button type="submit" class="staffui-btn">Send Request</button>
        </div>
    </form>
</x-staff-layout>
