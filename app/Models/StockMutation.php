<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'reference_type',
        'reference_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function record(
        Product $product,
        string $referenceType,
        int $referenceId,
        string $type,
        int $quantity,
        ?string $description = null
    ): self {
        $before = $product->stock;
        $after = $type === 'in' ? ($before + $quantity) : ($before - $quantity);

        $product->update(['stock' => max(0, $after)]);

        return self::create([
            'product_id' => $product->id,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $after,
            'description' => $description,
        ]);
    }
}
