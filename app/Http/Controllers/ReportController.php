<?php

namespace App\Http\Controllers;

use App\Models\InventoryAudit;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MANAGER REPORTS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Manager Only
        |--------------------------------------------------------------------------
        */

        if (!auth()->user()->isManager()) {
            abort(403, 'Manager access only.');
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION REPORT
        |--------------------------------------------------------------------------
        */

        $transactionQuery = Transaction::with('creator');


        if ($request->filled('transaction_from')) {

            $transactionQuery->whereDate(
                'transaction_date',
                '>=',
                $request->transaction_from
            );
        }


        if ($request->filled('transaction_to')) {

            $transactionQuery->whereDate(
                'transaction_date',
                '<=',
                $request->transaction_to
            );
        }


        if ($request->filled('transaction_status')) {

            $transactionQuery->where(
                'status',
                $request->transaction_status
            );
        }


        if ($request->filled('service_type')) {

            $transactionQuery->where(
                'service_type',
                $request->service_type
            );
        }


        $transactions = $transactionQuery
            ->latest('transaction_date')
            ->latest('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION SUMMARY
        |--------------------------------------------------------------------------
        */

        $transactionSummary = [

            'total' => $transactions->count(),

            'pending' => $transactions
                ->where('status', 'Pending')
                ->count(),

            'claimed' => $transactions
                ->where('status', 'Claimed')
                ->count(),

            'voided' => $transactions
                ->where('status', 'Voided')
                ->count(),

        ];


        /*
        |--------------------------------------------------------------------------
        | INVENTORY AUDIT REPORT
        |--------------------------------------------------------------------------
        */

        $auditQuery = InventoryAudit::with([
            'user',
            'details.product'
        ]);


        if ($request->filled('audit_from')) {

            $auditQuery->whereDate(
                'audit_date',
                '>=',
                $request->audit_from
            );
        }


        if ($request->filled('audit_to')) {

            $auditQuery->whereDate(
                'audit_date',
                '<=',
                $request->audit_to
            );
        }


        if ($request->filled('auditor')) {

            $auditQuery->where(
                'user_id',
                $request->auditor
            );
        }


        if ($request->filled('audit_product')) {

            $auditQuery->whereHas(
                'details',
                function ($query) use ($request) {

                    $query->where(
                        'product_id',
                        $request->audit_product
                    );

                }
            );
        }


        $audits = $auditQuery
            ->latest('audit_date')
            ->latest('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | AUDIT DETAILS
        |--------------------------------------------------------------------------
        */

        $auditDetails = $audits
            ->flatMap(
                fn ($audit) => $audit->details
            )
            ->values();


        /*
        |--------------------------------------------------------------------------
        | AUDIT SUMMARY
        |--------------------------------------------------------------------------
        */

        $auditSummary = [

            'audits' => $audits->count(),

            'items' => $auditDetails->count(),

            'shortage' => $auditDetails
                ->where('discrepancy', '<', 0)
                ->count(),

            'excess' => $auditDetails
                ->where('discrepancy', '>', 0)
                ->count(),

            'matched' => $auditDetails
                ->where('discrepancy', 0)
                ->count(),

        ];


        /*
        |--------------------------------------------------------------------------
        | FILTER OPTIONS
        |--------------------------------------------------------------------------
        */

        $serviceTypes = Transaction::query()
            ->select('service_type')
            ->whereNotNull('service_type')
            ->distinct()
            ->orderBy('service_type')
            ->pluck('service_type');


        $products = Product::orderBy(
            'product_name'
        )->get();


        $auditors = InventoryAudit::with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | MANAGER REPORT VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'manager.reports.index',
            compact(
                'transactions',
                'transactionSummary',
                'audits',
                'auditSummary',
                'serviceTypes',
                'products',
                'auditors'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT TRANSACTION REPORT
    |--------------------------------------------------------------------------
    */

    public function exportTransactions(
        Request $request
    ): StreamedResponse {

        if (!auth()->user()->isManager()) {
            abort(403, 'Manager access only.');
        }


        $query = Transaction::with('creator');


        $this->applyTransactionFilters(
            $query,
            $request
        );


        $transactions = $query
            ->latest('transaction_date')
            ->latest('id')
            ->get();


        return response()->streamDownload(

            function () use ($transactions) {

                $handle = fopen(
                    'php://output',
                    'w'
                );


                fputcsv($handle, [

                    'Control Number',
                    'Customer Name',
                    'Service Type',
                    'Transaction Date',
                    'Item Description',
                    'Status',
                    'Recorded By',

                ]);


                foreach ($transactions as $transaction) {

                    fputcsv($handle, [

                        $transaction->control_number,

                        $transaction->customer_name,

                        $transaction->service_type,

                        $transaction
                            ->transaction_date
                            ?->format('Y-m-d'),

                        $transaction->item_description,

                        $transaction->status,

                        $transaction->creator?->name,

                    ]);
                }


                fclose($handle);
            },

            'transaction-report.csv',

            [
                'Content-Type' => 'text/csv',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT AUDIT REPORT
    |--------------------------------------------------------------------------
    */

    public function exportAudits(
        Request $request
    ): StreamedResponse {

        if (!auth()->user()->isManager()) {
            abort(403, 'Manager access only.');
        }


        $query = InventoryAudit::with([
            'user',
            'details.product'
        ]);


        $this->applyAuditFilters(
            $query,
            $request
        );


        $audits = $query
            ->latest('audit_date')
            ->latest('id')
            ->get();


        return response()->streamDownload(

            function () use ($audits) {

                $handle = fopen(
                    'php://output',
                    'w'
                );


                fputcsv($handle, [

                    'Audit ID',
                    'Audit Date',
                    'Auditor',
                    'Audit Status',
                    'Product',
                    'SKU',
                    'Recorded Quantity',
                    'Counted Quantity',
                    'Discrepancy',
                    'Result',

                ]);


                foreach ($audits as $audit) {

                    foreach ($audit->details as $detail) {

                        $result = match (true) {

                            $detail->discrepancy < 0
                                => 'Shortage',

                            $detail->discrepancy > 0
                                => 'Excess',

                            default
                                => 'Matched',

                        };


                        fputcsv($handle, [

                            $audit->id,

                            $audit->audit_date
                                ?->format('Y-m-d'),

                            $audit->user?->name,

                            $audit->status,

                            $detail->product
                                ?->product_name,

                            $detail->product?->sku,

                            $detail->recorded_qty,

                            $detail->counted_qty,

                            $detail->discrepancy,

                            $result,

                        ]);
                    }
                }


                fclose($handle);
            },

            'inventory-audit-report.csv',

            [
                'Content-Type' => 'text/csv',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION FILTERS
    |--------------------------------------------------------------------------
    */

    private function applyTransactionFilters(
        $query,
        Request $request
    ): void {

        if ($request->filled('transaction_from')) {

            $query->whereDate(
                'transaction_date',
                '>=',
                $request->transaction_from
            );
        }


        if ($request->filled('transaction_to')) {

            $query->whereDate(
                'transaction_date',
                '<=',
                $request->transaction_to
            );
        }


        if ($request->filled('transaction_status')) {

            $query->where(
                'status',
                $request->transaction_status
            );
        }


        if ($request->filled('service_type')) {

            $query->where(
                'service_type',
                $request->service_type
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | AUDIT FILTERS
    |--------------------------------------------------------------------------
    */

    private function applyAuditFilters(
        $query,
        Request $request
    ): void {

        if ($request->filled('audit_from')) {

            $query->whereDate(
                'audit_date',
                '>=',
                $request->audit_from
            );
        }


        if ($request->filled('audit_to')) {

            $query->whereDate(
                'audit_date',
                '<=',
                $request->audit_to
            );
        }


        if ($request->filled('auditor')) {

            $query->where(
                'user_id',
                $request->auditor
            );
        }


        if ($request->filled('audit_product')) {

            $query->whereHas(
                'details',
                function ($query) use ($request) {

                    $query->where(
                        'product_id',
                        $request->audit_product
                    );

                }
            );
        }
    }
}