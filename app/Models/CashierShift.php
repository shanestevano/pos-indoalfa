<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'starting_cash',
        'cash_sales_total',
        'non_cash_sales_total',
        'expected_cash',
        'actual_closing_cash',
        'difference',
        'started_at',
        'closed_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'starting_cash' => 'decimal:2',
            'cash_sales_total' => 'decimal:2',
            'non_cash_sales_total' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'actual_closing_cash' => 'decimal:2',
            'difference' => 'decimal:2',
            'started_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function recalculateTotals(): void
    {
        $cashSales = (float) $this->sales()
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $nonCashSales = (float) $this->sales()
            ->where('status', 'completed')
            ->whereIn('payment_method', ['qris', 'edc'])
            ->sum('total_amount');

        $this->cash_sales_total = $cashSales;
        $this->non_cash_sales_total = $nonCashSales;
        $this->expected_cash = (float) $this->starting_cash + $cashSales;
        $this->save();
    }
}
