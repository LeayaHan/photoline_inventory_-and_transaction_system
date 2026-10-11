<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/branch-manager/inventory.css') }}">

    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Inventory Requests</h1>
                <p class="users-subtitle">Review changes your staff want to make to the inventory.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="users-alert users-alert-success">{{ session('success') }}</div>
        @endif

        <div class="inv-tabs">
            <a
                class="inv-tab {{ $status === 'pending' ? 'active' : '' }}"
                href="{{ route('branch-manager.inventory-requests.index', ['status' => 'pending']) }}">Pending <em>{{ $counts['pending'] }}</em></a>
            <a
                class="inv-tab {{ $status === 'approved' ? 'active' : '' }}"
                href="{{ route('branch-manager.inventory-requests.index', ['status' => 'approved']) }}">Approved <em>{{ $counts['approved'] }}</em></a>
            <a
                class="inv-tab {{ $status === 'declined' ? 'active' : '' }}"
                href="{{ route('branch-manager.inventory-requests.index', ['status' => 'declined']) }}">Declined <em>{{ $counts['declined'] }}</em></a>
            <a
                class="inv-tab {{ $status === 'all' ? 'active' : '' }}"
                href="{{ route('branch-manager.inventory-requests.index', ['status' => 'all']) }}">All</a>
        </div>

        <div class="invreq-list">
            @forelse ($requests as $req)
                <div class="invreq-card">
                    <div class="invreq-head">
                        <div>
                            <div class="invreq-type">{{ $req->type_label }}</div>
                            <h3 class="invreq-title">{{ $req->item_name }}</h3>
                            <div class="invreq-meta">
                                Requested by <strong>{{ $req->staff?->full_name ?? 'Deleted staff account' }}</strong>
                                @if ($req->staff) ({{ $req->staff->staff_id }}) @endif
                                &middot; {{ $req->created_at->format('M d, Y g:i A') }}
                            </div>
                        </div>
                        <div><span class="inv-pill inv-pill-{{ $req->status }}">{{ ucfirst($req->status) }}</span></div>
                    </div>

                    <ul class="invreq-lines">
                        @foreach ($req->describe() as $label => $text)
                            <li><strong>{{ $label }}</strong><span>{{ $text }}</span></li>
                        @endforeach
                    </ul>

                    @if ($req->reason)
                        <p class="invreq-reason"><strong>Reason:</strong> {{ $req->reason }}</p>
                    @endif

                    @error('request_' . $req->id)
                        <div class="users-alert users-alert-error">{{ $message }}</div>
                    @enderror

                    @if ($req->isPending())
                        <div class="invreq-actions">
                            <form
                                action="{{ route('branch-manager.inventory-requests.approve', ['inventoryRequest' => $req->id]) }}"
                                method="POST">
                                @csrf
                                <input type="text" name="remarks" placeholder="Remarks (optional)" maxlength="500">
                                <button type="submit" class="users-btn users-btn-approve">Approve</button>
                            </form>

                            <form
                                action="{{ route('branch-manager.inventory-requests.decline', ['inventoryRequest' => $req->id]) }}"
                                method="POST">
                                @csrf
                                <input type="text" name="remarks" placeholder="Reason for declining" maxlength="500" required>
                                <button type="submit" class="users-btn users-btn-decline">Decline</button>
                            </form>
                        </div>
                    @else
                        <div class="invreq-review">
                            {{ ucfirst($req->status) }} {{ $req->reviewed_at?->format('M d, Y g:i A') }}
                            @if ($req->manager_remarks) &middot; "{{ $req->manager_remarks }}" @endif
                        </div>
                    @endif
                </div>
            @empty
                <div
                    class="users-card"><p
                    class="users-empty"
                    style="margin:0;">No {{ $status === 'all' ? '' : $status }} requests.</p></div>
            @endforelse
        </div>

        <div class="users-pagination">{{ $requests->links() }}</div>
    </div>
</x-app-layout>
