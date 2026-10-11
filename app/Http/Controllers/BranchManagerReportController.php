<?php

namespace App\Http\Controllers;

use App\Models\CustomerTransaction;
use App\Models\InventoryAudit;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

// Branch Manager reports (FR-3.1, 3.2, 3.4, 3.5). Read only: it never changes any record,
// it just adds up what the other pages already store (audits, transactions, inventory, requests).
class BranchManagerReportController extends Controller
{
    private const TYPES = ['audits', 'transactions', 'inventory'];
    private const RANGES = ['this_month', 'last_month', 'this_year', 'all'];

    public function index(Request $request)
    {
        $type = $this->reportType($request);
        [$from, $to, $range] = $this->period($request);
        $report = $this->build($type, $from, $to);

        return view('branch-manager.reports.index', compact('type', 'from', 'to', 'range', 'report'));
    }

    // ---------- Export (FR-3.5): the open report as a CSV file that Excel opens ----------
    public function export(Request $request)
    {
        $type = $this->reportType($request);
        [$from, $to] = $this->period($request);
        $report = $this->build($type, $from, $to);

        $period = $type === 'inventory'
            ? 'As of ' . today()->format('M d, Y')
            : (($from ? date('M d, Y', strtotime($from)) : 'Beginning') . ' to ' . ($to ? date('M d, Y', strtotime($to)) : 'Present'));

        $titles = [
            'audits' => 'Inventory audit report',
            'transactions' => 'Customer transaction report',
            'inventory' => 'Inventory status report',
        ];

        $sections = match ($type) {
            'audits' => $this->auditSections($report),
            'transactions' => $this->transactionSections($report),
            'inventory' => $this->inventorySections($report),
        };

        $filename = 'photoline-' . $type . '-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($titles, $type, $period, $sections) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // lets Excel read the peso sign and accents correctly

            fputcsv($out, ['Photoline Inventory & Transaction System']);
            fputcsv($out, [$titles[$type]]);
            fputcsv($out, ['Period', $period]);
            fputcsv($out, ['Generated', now()->format('M d, Y h:i A')]);
            fputcsv($out, ['Prepared by', auth()->user()->name ?? 'Branch Manager']);

            foreach ($sections as [$heading, $headers, $rows]) {
                fputcsv($out, []);
                fputcsv($out, [$heading]);
                fputcsv($out, $headers);
                foreach ($rows as $row) {
                    fputcsv($out, $row);
                }
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ---------- Shared: which report and which dates ----------
    private function reportType(Request $request): string
    {
        $type = $request->query('type', 'audits');

        return in_array($type, self::TYPES, true) ? $type : 'audits';
    }

    // Returns [from, to, range]. range is the quick button that is on (or "custom").
    private function period(Request $request): array
    {
        $range = $request->query('range');

        if (in_array($range, self::RANGES, true)) {
            [$from, $to] = $this->presetDates($range);
        } elseif ($request->has('date_from') || $request->has('date_to')) {
            // Dates typed in the From / To form. Empty dates mean no limit on that side.
            $from = $request->query('date_from') ?: null;
            $to = $request->query('date_to') ?: null;
            if ($from && $to && $from > $to) {
                [$from, $to] = [$to, $from]; // wrong way round, swap
            }
        } else {
            [$from, $to] = $this->presetDates('this_month'); // first visit
        }

        // Light up the quick button if the dates happen to match one
        $range = 'custom';
        foreach (self::RANGES as $preset) {
            if ($this->presetDates($preset) === [$from, $to]) {
                $range = $preset;
                break;
            }
        }

        return [$from, $to, $range];
    }

    private function presetDates(string $range): array
    {
        return match ($range) {
            'last_month' => [
                now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                now()->subMonthNoOverflow()->endOfMonth()->toDateString(),
            ],
            'this_year' => [now()->startOfYear()->toDateString(), now()->toDateString()],
            'all' => [null, null],
            default => [now()->startOfMonth()->toDateString(), now()->toDateString()],
        };
    }

    // Only the open report is calculated
    private function build(string $type, ?string $from, ?string $to): array
    {
        return match ($type) {
            'audits' => $this->auditReport($from, $to),
            'transactions' => $this->transactionReport($from, $to),
            'inventory' => $this->inventoryReport(),
        };
    }

    // ---------- Inventory audits (FR-3.4) ----------
    // Every number here counts ITEMS (products), not pieces. Pieces, sheets, packs and rolls
    // cannot be added together, so the quantity is only shown per item, with that item's own unit.
    private function auditReport($from, $to): array
    {
        // Only finalized audits count (drafts are still being counted), and only items that were counted
        $audits = InventoryAudit::where('status', 'finalized')
            ->when($from, fn ($q) => $q->whereDate('audit_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('audit_date', '<=', $to))
            ->with(['items' => fn ($q) => $q->whereNotNull('physical_quantity')])
            ->orderByDesc('audit_date')
            ->orderByDesc('id')
            ->get();

        $perItem = []; // item code => how many times it was counted / was off
        $rows = [];    // one line per audit
        $total = ['counted' => 0, 'matched' => 0, 'short' => 0, 'over' => 0];

        foreach ($audits as $audit) {
            $row = [
                'id' => $audit->id,
                'number' => $audit->audit_number,
                'date' => $audit->audit_date,
                'auditor' => $audit->auditor_name,
                'counted' => 0,
                'matched' => 0,
                'short' => 0, // items where the shelf had LESS than the system says
                'over' => 0,  // items where the shelf had MORE than the system says
            ];

            foreach ($audit->items as $item) {
                $difference = $item->physical_quantity - $item->recorded_quantity;
                $row['counted']++;

                if (! isset($perItem[$item->item_code])) {
                    $perItem[$item->item_code] = [
                        'item_code' => $item->item_code,
                        'item_name' => $item->item_name,
                        'unit' => $item->unit,
                        'times_counted' => 0,
                        'times_off' => 0,
                        'missing' => 0,
                        'extra' => 0,
                    ];
                }
                $perItem[$item->item_code]['times_counted']++;

                if ($difference == 0) {
                    $row['matched']++;
                } elseif ($difference < 0) {
                    $row['short']++;
                    $perItem[$item->item_code]['times_off']++;
                    $perItem[$item->item_code]['missing'] += abs($difference);
                } else {
                    $row['over']++;
                    $perItem[$item->item_code]['times_off']++;
                    $perItem[$item->item_code]['extra'] += $difference;
                }
            }

            foreach (['counted', 'matched', 'short', 'over'] as $key) {
                $total[$key] += $row[$key];
            }
            $rows[] = (object) $row;
        }

        $total['accuracy'] = $total['counted'] > 0 ? round($total['matched'] / $total['counted'] * 100, 1) : null;

        // The 10 items that were off the most often
        $repeat = collect($perItem)
            ->where('times_off', '>', 0)
            ->sortByDesc(fn ($row) => [$row['times_off'], $row['missing'] + $row['extra']])
            ->take(10)
            ->map(fn ($row) => (object) $row)
            ->values();

        return [
            'audits' => $audits->count(),
            'rows' => collect($rows),
            'total' => (object) $total,
            'repeat' => $repeat,
        ];
    }

    private function auditSections(array $r): array
    {
        $list = $r['rows']->map(fn ($a) => [
            $a->number, $a->date->format('Y-m-d'), $a->auditor,
            $a->counted, $a->matched, $a->short, $a->over,
        ])->all();

        $t = $r['total'];
        $list[] = ['Total', '', '', $t->counted, $t->matched, $t->short, $t->over];

        return [
            ['Finalized audits', ['Audit no.', 'Date', 'Auditor', 'Items counted', 'Matched', 'Less than recorded', 'More than recorded'], $list],
            ['Items that were wrong most often', ['Code', 'Item', 'Unit', 'Times counted', 'Times wrong', 'Quantity missing', 'Quantity extra'],
                $r['repeat']->map(fn ($x) => [$x->item_code, $x->item_name, $x->unit, $x->times_counted, $x->times_off, $x->missing, $x->extra])->all()],
        ];
    }

    // ---------- Customer transactions (FR-3.4) ----------
    // Only COMPLETED transactions are counted (fully paid, stock already deducted).
    // Pending ones are still being worked on, so they stay out of every number.
    private function transactionReport($from, $to): array
    {
        $completed = CustomerTransaction::with(['details', 'payments'])
            ->where('status', 'Completed')
            ->when($from, fn ($q) => $q->whereDate('transaction_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('transaction_date', '<=', $to))
            ->get();

        // Voided ones are not counted either. They are only listed at the bottom as a record.
        $voided = CustomerTransaction::where('status', 'Voided')
            ->when($from, fn ($q) => $q->whereDate('transaction_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('transaction_date', '<=', $to))
            ->orderByDesc('transaction_date')
            ->get();

        // Billed by service: add up every line of every completed transaction
        $services = [];
        foreach ($completed as $transaction) {
            foreach ($transaction->details as $detail) {
                $name = $detail->service_type ?: 'Other';
                if (! isset($services[$name])) {
                    $services[$name] = ['label' => $name, 'qty' => 0, 'amount' => 0];
                }
                $services[$name]['qty'] += $detail->quantity;
                $services[$name]['amount'] += $detail->quantity * $detail->amount;
            }
        }

        // Collected by payment method (Cash, GCash, Card)
        $methods = [];
        foreach ($completed as $transaction) {
            foreach ($transaction->payments as $payment) {
                $name = $payment->payment_type;
                if (! isset($methods[$name])) {
                    $methods[$name] = ['label' => $name, 'payments' => 0, 'amount' => 0];
                }
                $methods[$name]['payments']++;
                $methods[$name]['amount'] += $payment->amount;
            }
        }

        $servicesTotal = round(collect($services)->sum('amount'), 2);
        $billed = round($completed->sum('total_amount'), 2);

        return [
            'total' => $completed->count(),
            'billed' => $billed,
            'collected' => round($completed->sum('amount_paid'), 2),
            // every service is listed (no top 10) so the rows really add up to the subtotal
            'services' => collect($services)->sortByDesc('amount')->map(fn ($row) => (object) $row)->values(),
            'services_total' => $servicesTotal,
            'discounts' => round($servicesTotal - $billed, 2),
            'methods' => collect($methods)->sortByDesc('amount')->map(fn ($row) => (object) $row)->values(),
            'voided_count' => $voided->count(),
            'voided' => $voided->take(10)->values(),
        ];
    }

    private function transactionSections(array $r): array
    {
        $services = $r['services']->map(fn ($s) => [$s->label, $s->qty, round($s->amount, 2)])->all();
        $services[] = ['Subtotal', '', $r['services_total']];
        $services[] = ['Discounts', '', -$r['discounts']];
        $services[] = ['Billed', '', $r['billed']];

        $methods = $r['methods']->map(fn ($m) => [$m->label, $m->payments, round($m->amount, 2)])->all();
        $methods[] = ['Total collected', '', $r['collected']];

        return [
            ['Completed transactions', ['Transactions', 'Billed (PHP)', 'Collected (PHP)'], [[$r['total'], $r['billed'], $r['collected']]]],
            ['Billed by service', ['Service', 'Qty', 'Amount (PHP)'], $services],
            ['Collected by payment method', ['Method', 'Payments', 'Amount (PHP)'], $methods],
            ['Voided transactions (not counted above)', ['Control no.', 'Customer', 'Date', 'Reason'],
                $r['voided']->map(fn ($t) => [$t->control_number, $t->customer_name, $t->transaction_date->format('Y-m-d'), $t->void_reason ?: ''])->all()],
        ];
    }

    // ---------- Inventory status: stock as of today (FR-3.1, FR-3.2) ----------
    private function inventoryReport(): array
    {
        $items = InventoryItem::orderBy('name')->get();

        // Summary per kind of stock (Merchandise / Raw Materials), then a total line
        $summary = [];
        foreach (InventoryItem::TYPES as $key => $label) {
            $summary[$key] = (object) ['label' => $label, 'items' => 0, 'ok' => 0, 'low' => 0, 'out' => 0, 'expired' => 0, 'expiring' => 0];
        }

        $needAttention = collect();

        foreach ($items as $item) {
            $flags = [];
            $row = $summary[$item->item_type] ?? null;
            if ($row) {
                $row->items++;
            }

            if ($item->isOut()) {
                $flags[] = 'Out of stock';
                if ($row) { $row->out++; }
            } elseif ($item->isLow()) {
                $flags[] = 'Low stock';
                if ($row) { $row->low++; }
            } elseif ($row) {
                $row->ok++;
            }

            // Expiry only matters while there is still stock
            if ($item->quantity > 0 && $item->expiry_date) {
                if ($item->expiry_date->lt(today())) {
                    $flags[] = 'Expired';
                    if ($row) { $row->expired++; }
                } elseif ($item->expiry_date->lte(today()->addDays(30))) {
                    $flags[] = 'Expiring soon';
                    if ($row) { $row->expiring++; }
                }
            }

            if ($flags) {
                $item->flags = $flags;
                // Most urgent first: out of stock, expired, low stock, expiring soon
                $item->urgency = match (true) {
                    in_array('Out of stock', $flags) => 0,
                    in_array('Expired', $flags) => 1,
                    in_array('Low stock', $flags) => 2,
                    default => 3,
                };
                $needAttention->push($item);
            }
        }

        $total = (object) ['label' => 'Total'];
        foreach (['items', 'ok', 'low', 'out', 'expired', 'expiring'] as $key) {
            $total->$key = collect($summary)->sum($key);
        }

        return [
            'summary' => collect($summary)->values(),
            'total' => $total,
            // every item that needs attention is listed, most urgent first
            'rows' => $needAttention->sortBy('urgency')->values(),
        ];
    }

    private function inventorySections(array $r): array
    {
        $summary = $r['summary']->push($r['total'])
            ->map(fn ($s) => [$s->label, $s->items, $s->ok, $s->low, $s->out, $s->expired, $s->expiring])->all();

        return [
            ['Stock summary', ['Type', 'Items', 'In stock', 'Low stock', 'Out of stock', 'Expired', 'Expiring in 30 days'], $summary],
            ['Items that need attention', ['Code', 'Item', 'Type', 'On hand', 'Unit', 'Reorder at', 'Expiry', 'Flags'],
                $r['rows']->map(fn ($i) => [
                    $i->item_code, $i->name, InventoryItem::TYPES[$i->item_type] ?? $i->item_type, $i->quantity, $i->unit,
                    $i->reorder_level, $i->expiry_date ? $i->expiry_date->format('Y-m-d') : '', implode(', ', $i->flags),
                ])->all()],
        ];
    }
}
