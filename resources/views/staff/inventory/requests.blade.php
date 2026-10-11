<x-staff-layout title="My Inventory Requests">
    <div class="staffui-pagehead">
        <div>
            <h1>My Inventory Requests</h1>
            <p>Track the changes you've sent to the branch manager.</p>
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('staff.inventory') }}" class="staffui-btn staffui-btn-outline">Back to Inventory</a>
        </div>
    </div>

    @if (session('success'))
        <div class="staffui-alert staffui-alert-success">{{ session('success') }}</div>
    @endif

    <div class="staffui-tabs">
        @foreach (['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'declined' => 'Declined'] as $key => $label)
            <a class="staffui-tab {{ $status === $key ? 'is-active' : '' }}"
               href="{{ route('staff.inventory.requests', ['status' => $key]) }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="staffui-reqlist">
        @forelse ($requests as $req)
            <div class="staffui-req">
                <div class="staffui-req-head">
                    <div>
                        <div class="staffui-req-type">{{ $req->type_label }}</div>
                        <h3>{{ $req->item_name }}</h3>
                        <div class="staffui-req-meta">Sent {{ $req->created_at->format('M d, Y g:i A') }}</div>
                    </div>
                    <div><span class="staffui-pill staffui-pill-{{ $req->status }}">{{ ucfirst($req->status) }}</span></div>
                </div>

                <ul class="staffui-req-lines">
                    @foreach ($req->describe() as $label => $text)
                        <li><strong>{{ $label }}</strong><span>{{ $text }}</span></li>
                    @endforeach
                </ul>

                @if ($req->reason)
                    <p class="staffui-req-note"><strong>Your reason:</strong> {{ $req->reason }}</p>
                @endif

                @if (! $req->isPending())
                    <div class="staffui-req-review {{ $req->status === 'declined' ? 'is-declined' : '' }}">
                        {{ ucfirst($req->status) }} {{ $req->reviewed_at?->format('M d, Y g:i A') }}
                        @if ($req->manager_remarks) &middot; Manager: "{{ $req->manager_remarks }}" @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="staffui-empty" style="max-width:none;">
                <h3>No requests yet</h3>
                <p>Requests you send will show up here with their status.</p>
                <a href="{{ route('staff.inventory') }}" class="staffui-btn">Go to Inventory</a>
            </div>
        @endforelse
    </div>

    <div class="staffui-pager">{{ $requests->links() }}</div>
</x-staff-layout>
