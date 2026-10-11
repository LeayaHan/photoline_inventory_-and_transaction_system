<?php

namespace App\Http\Controllers;

use App\Models\CustomerTransaction;
use App\Models\InventoryAudit;
use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use App\Models\Staff;

// The two dashboards. They only READ data, nothing here changes any record.
class DashboardController extends Controller
{
    // How many lines each "recent" list shows
    private const RECENT = 5;

    // ---------- Branch manager ----------
    public function manager()
    {
        // Completed sales this month (completed = fully paid and stock already deducted).
        // "Today" is picked out of the same list, so we only ask the database once.
        $monthSales = CustomerTransaction::with(['details', 'payments'])
            ->where('status', 'Completed')
            ->whereDate('transaction_date', '>=', now()->startOfMonth())
            ->get();
        $todaySales = $monthSales->filter(fn ($transaction) => $transaction->transaction_date->isToday());

        $sales = [
            'today' => $this->salesTotals($todaySales),
            'month' => $this->salesTotals($monthSales),
        ];

        // Items that need attention, most urgent first. Only the first few are shown.
        $stockAlerts = $this->stockAlerts();

        $summary = [
            'low_stock' => array_sum(InventoryItem::lowStockCounts()), // low + out of stock
            'pending_requests' => InventoryRequest::where('status', 'pending')->count(),
            'draft_audits' => InventoryAudit::where('status', 'draft')->count(),
            'staff' => Staff::count(),
        ];

        $recentTransactions = CustomerTransaction::with(['details', 'payments'])
            ->latest('transaction_date')->latest('id')
            ->take(self::RECENT)->get();

        $recentRequests = InventoryRequest::with('staff')
            ->latest()
            ->take(self::RECENT)->get();

        $recentAudits = InventoryAudit::latest('audit_date')->latest('id')
            ->take(self::RECENT)->get();

        return view('branch-manager.dashboard', [
            'sales' => $sales,
            'summary' => $summary,
            'stockAlertTotal' => $stockAlerts->count(),
            'stockAlerts' => $stockAlerts->take(self::RECENT),
            'recentTransactions' => $recentTransactions,
            'recentRequests' => $recentRequests,
            'recentAudits' => $recentAudits,
        ]);
    }

    // ---------- Staff (only the logged-in staff member's own records) ----------
    public function staff()
    {
        $staff = auth('staff')->user();

        $pending = CustomerTransaction::with(['details', 'payments'])
            ->where('staff_id', $staff->id)
            ->where('status', 'Pending')
            ->get();

        $summary = [
            'pending_transactions' => $pending->count(),
            'balance_owed' => round($pending->sum('balance'), 2), // what customers still owe on those
            'completed_today' => CustomerTransaction::where('staff_id', $staff->id)
                ->where('status', 'Completed')
                ->whereDate('transaction_date', today())
                ->count(),
            'draft_audits' => InventoryAudit::where('staff_id', $staff->id)->where('status', 'draft')->count(),
            'pending_requests' => $staff->inventoryRequests()->where('status', 'pending')->count(),
        ];

        $recentTransactions = CustomerTransaction::with(['details', 'payments'])
            ->where('staff_id', $staff->id)
            ->latest('transaction_date')->latest('id')
            ->take(self::RECENT)->get();

        $recentRequests = $staff->inventoryRequests()
            ->latest()
            ->take(self::RECENT)->get();

        $recentAudits = InventoryAudit::where('staff_id', $staff->id)
            ->latest('audit_date')->latest('id')
            ->take(self::RECENT)->get();

        return view('staff.dashboard', compact('staff', 'summary', 'recentTransactions', 'recentRequests', 'recentAudits'));
    }

    // ---------- Helpers ----------

    // How many transactions, and the money on them (same rules as the Reports page)
    private function salesTotals($transactions): array
    {
        return [
            'count' => $transactions->count(),
            'billed' => round($transactions->sum('total_amount'), 2),    // after discounts
            'collected' => round($transactions->sum('amount_paid'), 2),  // payments received
        ];
    }

    // Every item with a warning (out of stock, low, expired, expiring soon), most urgent first
    private function stockAlerts()
    {
        $alerts = collect();

        foreach (InventoryItem::orderBy('name')->get() as $item) {
            $flags = $item->stockFlags();

            if (! $flags) {
                continue;
            }

            $item->flags = $flags;
            // 0 = most urgent
            $item->urgency = match (true) {
                in_array('Out of stock', $flags) => 0,
                in_array('Expired', $flags) => 1,
                in_array('Low stock', $flags) => 2,
                default => 3,
            };
            $alerts->push($item);
        }

        return $alerts->sortBy('urgency')->values();
    }
}
