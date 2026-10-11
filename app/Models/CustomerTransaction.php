<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerTransaction extends Model
{
    protected $table = 'customer_transactions';

    protected $fillable = [
        'control_number',
        'staff_id',
        'customer_name',
        'customer_contact_number',
        'customer_address',
        'payment_type',
        'transaction_date',
        'discount_type',
        'discount_value',
        'deposit_amount',
        'status',
        'void_reason',
        'voided_by_staff_id',
        'voided_at',
        'claimed_at',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'discount_value' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'claimed_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    // the staff member who voided this transaction
    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'voided_by_staff_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TransactionPayment::class, 'customer_transaction_id')->orderBy('paid_at')->orderBy('id');
    }

    public function getAmountPaidAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            return round((float) $this->payments->sum(fn ($payment) => (float) $payment->amount), 2);
        }

        return round((float) $this->payments()->sum('amount'), 2);
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->status === 'Voided') {
            return 'Voided';
        }

        // nothing to pay (e.g. 100% discount) counts as paid
        if ($this->total_amount <= 0) {
            return 'Paid';
        }

        if ($this->amount_paid <= 0) {
            return 'Unpaid';
        }

        return $this->amount_paid >= $this->total_amount ? 'Paid' : 'Partially Paid';
    }

    // "Partially Paid" -> "payment-status-partially-paid" (used for the colored label in the views)
    public function getPaymentStatusClassAttribute(): string
    {
        return 'payment-status-' . strtolower(str_replace(' ', '-', $this->payment_status));
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->details->sum(fn ($detail) => $detail->quantity * (float) $detail->amount);
    }

    public function getDiscountAmountAttribute(): float
    {
        $subtotal = $this->subtotal;
        $value = max(0, (float) $this->discount_value);
        $discount = $this->discount_type === 'percentage' ? $subtotal * min($value, 100) / 100 : $value;

        return round(min($subtotal, $discount), 2);
    }

    public function getTotalAmountAttribute(): float
    {
        return round(max(0, $this->subtotal - $this->discount_amount), 2);
    }

    public function getBalanceAttribute(): float
    {
        return round(max(0, $this->total_amount - $this->amount_paid), 2);
    }

    public function isEditable(): bool
    {
        return $this->status === 'Pending';
    }

    // Pending and Completed transactions can be voided. A voided one is final.
    public function isVoidable(): bool
    {
        return in_array($this->status, ['Pending', 'Completed'], true);
    }
}
