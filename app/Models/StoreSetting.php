<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'store_code',
        'address',
        'phone',
        'tax_percentage',
        'receipt_header',
        'receipt_footer',
        'printer_paper_width',
    ];

    protected function casts(): array
    {
        return [
            'tax_percentage' => 'decimal:2',
        ];
    }

    public static function current(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'INDOMARET POINT',
                'store_code' => 'IDM-4029',
                'address' => 'Jl. Raya Darmo No. 45, Surabaya, Jawa Timur',
                'phone' => '031-5678901',
                'tax_percentage' => 0.00,
                'receipt_header' => 'SELAMAT DATANG DI INDOMARET',
                'receipt_footer' => 'TERIMA KASIH TELAH BERBELANJA',
                'printer_paper_width' => '58mm',
            ]
        );
    }
}
