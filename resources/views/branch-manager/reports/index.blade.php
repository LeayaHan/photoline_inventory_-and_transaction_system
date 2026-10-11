@php
    $money = fn ($v) => '₱' . number_format((float) $v, 2);
    $num = fn ($v) => number_format((int) $v);
    $day = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('M d, Y') : '—';

    // name shown on the switch, title printed on paper, and one line saying what the report covers
    $tabs = [
        'audits' => ['Audits', 'Inventory audit report', 'Finalized audits only, by audit date. Shows where the shelf count did not match the system.'],
        'transactions' => ['Transactions', 'Customer transaction report', 'Completed transactions only, by transaction date. Pending and voided ones are not counted.'],
        'inventory' => ['Inventory', 'Inventory status report', 'Stock as recorded today, with every item that needs attention.'],
    ];
    [$tabName, $title, $basis] = $tabs[$type];

    // quick date buttons
    $presets = ['this_month' => 'This month', 'last_month' => 'Last month', 'this_year' => 'This year', 'all' => 'All time'];

    // what the tab links and the Export button carry along so the dates are not lost
    $carry = $range === 'custom'
        ? ['date_from' => $from, 'date_to' => $to]
        : ['range' => $range];

    $period = ($from || $to)
        ? ($from ? $day($from) : 'Beginning') . ' – ' . ($to ? $day($to) : 'Present')
        : 'All dates';
@endphp

<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/inventory-switch.css') }}">
    <link rel="stylesheet" href="{{ asset('css/branch-manager/reports.css') }}">

    <div class="users-container report-page">

        {{-- ===== Screen only: title, export + print buttons, switch, date filter ===== --}}
        <div class="users-header report-screen-only">
            <div>
                <h1 class="users-title">Reports</h1>
            </div>
            <div class="report-actions">
                <a class="users-btn users-btn-secondary" href="{{ route('branch-manager.reports.export', ['type' => $type] + $carry) }}">Export CSV</a>
                <button type="button" class="users-btn users-btn-primary" onclick="window.print()">Print report</button>
            </div>
        </div>

        <div class="report-screen-only report-controls">
            {{-- the switch: same look as the Merchandise / Raw Materials switch --}}
            <div class="inv-switch">
                @foreach ($tabs as $key => $tab)
                    <a href="{{ route('branch-manager.reports.index', ['type' => $key] + $carry) }}"
                       class="inv-switch-btn {{ $type === $key ? 'is-on' : '' }}">{{ $tab[0] }}</a>
                @endforeach
            </div>

            {{-- stock is "as of today", so there is nothing to filter by date on the Inventory tab --}}
            @if ($type !== 'inventory')
                <div class="report-filter">
                    <div class="report-presets">
                        @foreach ($presets as $key => $label)
                            <a href="{{ route('branch-manager.reports.index', ['type' => $type, 'range' => $key]) }}"
                               class="report-preset {{ $range === $key ? 'is-on' : '' }}">{{ $label }}</a>
                        @endforeach
                    </div>

                    <form method="GET" action="{{ route('branch-manager.reports.index') }}" class="report-dates">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <label>From <input type="date" name="date_from" value="{{ $from }}"></label>
                        <label>To <input type="date" name="date_to" value="{{ $to }}"></label>
                        <button type="submit" class="users-btn users-btn-primary">Apply</button>
                    </form>
                </div>
            @endif
        </div>

        {{-- ===== Print only: heading for the paper copy ===== --}}
        <header class="report-sheet-head">
            <div>
                <p class="report-sheet-org">Photoline Inventory &amp; Transaction System</p>
                <h1>{{ $title }}</h1>
                <p class="report-sheet-basis">{{ $basis }}</p>
            </div>
            <dl>
                @if ($type !== 'inventory')
                    <div><dt>Period</dt><dd>{{ $period }}</dd></div>
                @endif
                <div><dt>Generated</dt><dd>{{ now()->format('M d, Y h:i A') }}</dd></div>
                <div><dt>Prepared by</dt><dd>{{ auth()->user()->name ?? 'Branch Manager' }}</dd></div>
            </dl>
        </header>

        {{-- ===== AUDITS ===== --}}
        @if ($type === 'audits')
            @if ($report['audits'] === 0)
                <p class="report-empty">No finalized audits in this period.</p>
            @else
                <h3 class="report-caption report-caption-first">Finalized audits</h3>
                <div class="users-card">
                    <table class="users-table report-table">
                        <thead>
                            <tr>
                                <th>Audit no.</th>
                                <th>Date</th>
                                <th>Auditor</th>
                                <th class="num">Items counted</th>
                                <th class="num">Matched</th>
                                <th class="num">Less than recorded</th>
                                <th class="num">More than recorded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report['rows'] as $audit)
                                <tr>
                                    <td>
                                        <a class="report-link" href="{{ route('branch-manager.audits.show', $audit->id) }}">{{ $audit->number }}</a>
                                    </td>
                                    <td>{{ $day($audit->date) }}</td>
                                    <td>{{ $audit->auditor }}</td>
                                    <td class="num">{{ $num($audit->counted) }}</td>
                                    <td class="num">{{ $num($audit->matched) }}</td>
                                    <td class="num">{{ $num($audit->short) }}</td>
                                    <td class="num">{{ $num($audit->over) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Total</td>
                                <td class="num">{{ $num($report['total']->counted) }}</td>
                                <td class="num">{{ $num($report['total']->matched) }}</td>
                                <td class="num">{{ $num($report['total']->short) }}</td>
                                <td class="num">{{ $num($report['total']->over) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($report['repeat']->isEmpty())
                    <p class="report-empty">Every counted item matched what was recorded.</p>
                @else
                    <h3 class="report-caption">Items that were wrong most often</h3>
                    <div class="users-card">
                        <table class="users-table report-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Item</th>
                                    <th class="num">Times counted</th>
                                    <th class="num">Times wrong</th>
                                    <th class="num">Missing</th>
                                    <th class="num">Extra</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['repeat'] as $row)
                                    <tr>
                                        <td>{{ $row->item_code }}</td>
                                        <td>{{ $row->item_name }}</td>
                                        <td class="num">{{ $num($row->times_counted) }}</td>
                                        <td class="num">{{ $num($row->times_off) }}</td>
                                        <td class="num">{{ $row->missing > 0 ? $num($row->missing) . ' ' . $row->unit : '—' }}</td>
                                        <td class="num">{{ $row->extra > 0 ? $num($row->extra) . ' ' . $row->unit : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        @endif

        {{-- ===== TRANSACTIONS ===== --}}
        @if ($type === 'transactions')
            @if ($report['total'] === 0)
                <p class="report-empty">No completed transactions in this period.</p>
            @else
                <h3 class="report-caption report-caption-first">Completed transactions</h3>
                <div class="users-card">
                    <table class="users-table report-table">
                        <thead>
                            <tr>
                                <th class="num">Transactions</th>
                                <th class="num">Billed</th>
                                <th class="num">Collected</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="num">{{ $num($report['total']) }}</td>
                                <td class="num">{{ $money($report['billed']) }}</td>
                                <td class="num">{{ $money($report['collected']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="report-pair">
                    <div>
                        <h3 class="report-caption">Billed by service</h3>
                        <div class="users-card">
                            <table class="users-table report-table">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th class="num">Qty</th>
                                        <th class="num">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($report['services'] as $row)
                                        <tr>
                                            <td>{{ $row->label }}</td>
                                            <td class="num">{{ $num($row->qty) }}</td>
                                            <td class="num">{{ $money($row->amount) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="users-empty">No service lines recorded.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">Subtotal</td>
                                        <td class="num">{{ $money($report['services_total']) }}</td>
                                    </tr>
                                    <tr class="report-foot-minor">
                                        <td colspan="2">Less discounts</td>
                                        <td class="num">−{{ $money($report['discounts']) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Billed</td>
                                        <td class="num">{{ $money($report['billed']) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h3 class="report-caption">Collected by payment method</h3>
                        <div class="users-card">
                            <table class="users-table report-table">
                                <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th class="num">Payments</th>
                                        <th class="num">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($report['methods'] as $row)
                                        <tr>
                                            <td>{{ $row->label }}</td>
                                            <td class="num">{{ $num($row->payments) }}</td>
                                            <td class="num">{{ $money($row->amount) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="users-empty">No payments recorded.</td></tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">Total collected</td>
                                        <td class="num">{{ $money($report['collected']) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($report['voided']->isNotEmpty())
                    <h3 class="report-caption">Voided transactions <span class="report-sub">(not counted above)</span></h3>
                    <div class="users-card">
                        <table class="users-table report-table">
                            <thead>
                                <tr>
                                    <th>Control no.</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['voided'] as $row)
                                    <tr>
                                        <td>{{ $row->control_number }}</td>
                                        <td>{{ $row->customer_name }}</td>
                                        <td>{{ $day($row->transaction_date) }}</td>
                                        <td>{{ $row->void_reason ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($report['voided_count'] > $report['voided']->count())
                        <p class="report-note">Latest {{ $report['voided']->count() }} of {{ $num($report['voided_count']) }} voided transactions.</p>
                    @endif
                @endif
            @endif
        @endif

        {{-- ===== INVENTORY ===== --}}
        @if ($type === 'inventory')
            <h3 class="report-caption report-caption-first">Stock summary</h3>
            <div class="users-card">
                <table class="users-table report-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th class="num">Items</th>
                            <th class="num">In stock</th>
                            <th class="num">Low stock</th>
                            <th class="num">Out of stock</th>
                            <th class="num">Expired</th>
                            <th class="num">Expiring in 30 days</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report['summary'] as $row)
                            <tr>
                                <td>{{ $row->label }}</td>
                                <td class="num">{{ $num($row->items) }}</td>
                                <td class="num">{{ $num($row->ok) }}</td>
                                <td class="num">{{ $num($row->low) }}</td>
                                <td class="num">{{ $num($row->out) }}</td>
                                <td class="num">{{ $num($row->expired) }}</td>
                                <td class="num">{{ $num($row->expiring) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td class="num">{{ $num($report['total']->items) }}</td>
                            <td class="num">{{ $num($report['total']->ok) }}</td>
                            <td class="num">{{ $num($report['total']->low) }}</td>
                            <td class="num">{{ $num($report['total']->out) }}</td>
                            <td class="num">{{ $num($report['total']->expired) }}</td>
                            <td class="num">{{ $num($report['total']->expiring) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <h3 class="report-caption">Low-stock and expiry alerts</h3>
            @if ($report['rows']->isEmpty())
                <p class="report-empty">No items need attention.</p>
            @else
                <div class="users-card">
                    <table class="users-table report-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Item</th>
                                <th class="num">On hand</th>
                                <th class="num">Reorder at</th>
                                <th>Expiry</th>
                                <th>Flag</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report['rows'] as $item)
                                <tr>
                                    <td>{{ $item->item_code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td class="num">{{ $num($item->quantity) }} {{ $item->unit }}</td>
                                    <td class="num">{{ $num($item->reorder_level) }}</td>
                                    <td>{{ $item->expiry_date ? $day($item->expiry_date) : '—' }}</td>
                                    <td>
                                        @foreach ($item->flags as $flag)
                                            <span class="report-flag {{ in_array($flag, ['Out of stock', 'Expired'], true) ? 'report-flag-critical' : 'report-flag-warn' }}">{{ $flag }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif


    </div>
</x-app-layout>
