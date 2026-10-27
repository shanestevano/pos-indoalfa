<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\StoreSetting;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $warehouseRole = Role::firstOrCreate(['name' => 'warehouse']);

        // 2. Setup Users
        $admin = User::firstOrCreate(
            ['employee_code' => 'ADM001'],
            [
                'name' => 'Head Store Manager',
                'email' => 'admin@indomaret.co.id',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
        $admin->assignRole($adminRole);

        $cashier = User::firstOrCreate(
            ['employee_code' => 'KSR001'],
            [
                'name' => 'Siti Rahmawati (Kasir)',
                'email' => 'kasir@indomaret.co.id',
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'is_active' => true,
            ]
        );
        $cashier->assignRole($cashierRole);

        $warehouse = User::firstOrCreate(
            ['employee_code' => 'GDG001'],
            [
                'name' => 'Budi Santoso (Gudang)',
                'email' => 'gudang@indomaret.co.id',
                'phone' => '081234567892',
                'password' => Hash::make('password'),
                'role' => 'warehouse',
                'is_active' => true,
            ]
        );
        $warehouse->assignRole($warehouseRole);

        // 3. Store Setting
        StoreSetting::current();

        // 4. Units
        $units = [
            'Pcs' => 'pcs',
            'Botol' => 'btl',
            'Kaleng' => 'can',
            'Pack' => 'pck',
            'Dus' => 'box',
            'Sachet' => 'sct',
            'Kg' => 'kg',
        ];
        $unitModels = [];
        foreach ($units as $name => $symbol) {
            $unitModels[$name] = Unit::firstOrCreate(
                ['name' => $name],
                ['symbol' => $symbol]
            );
        }

        // 5. Categories
        $categories = [
            'Makanan & Snack' => ['slug' => 'makanan-snack', 'icon' => 'cake'],
            'Minuman Segar' => ['slug' => 'minuman-segar', 'icon' => 'cup-soda'],
            'Sembako & Kebutuhan Dapur' => ['slug' => 'sembako-kebutuhan-dapur', 'icon' => 'shopping-cart'],
            'Rokok & Tembakau' => ['slug' => 'rokok-tembakau', 'icon' => 'flame'],
            'Perawatan Diri' => ['slug' => 'perawatan-diri', 'icon' => 'sparkles'],
            'Obat & Kesehatan' => ['slug' => 'obat-kesehatan', 'icon' => 'cross'],
            'Kebutuhan Rumah Tangga' => ['slug' => 'kebutuhan-rumah-tangga', 'icon' => 'home'],
        ];
        $categoryModels = [];
        foreach ($categories as $catName => $data) {
            $categoryModels[$catName] = Category::firstOrCreate(
                ['name' => $catName],
                ['slug' => $data['slug'], 'icon' => $data['icon']]
            );
        }

        // 6. Suppliers
        $suppliers = [
            [
                'code' => 'DC-SUB-01',
                'name' => 'PT Indomarco Prismatama DC Surabaya',
                'phone' => '031-7482001',
                'email' => 'dc.surabaya@indomaret.co.id',
                'address' => 'Kawasan Industri Rungkut Megah No. 12, Surabaya',
            ],
            [
                'code' => 'SUP-INDF-01',
                'name' => 'PT Indofood CBP Sukses Makmur Tbk',
                'phone' => '021-57958822',
                'email' => 'sales@indofood.co.id',
                'address' => 'Sudirman Plaza, Indofood Tower, Jakarta',
            ],
            [
                'code' => 'SUP-MYR-01',
                'name' => 'PT Mayora Indah Tbk',
                'phone' => '021-80637000',
                'email' => 'customercare@mayora.co.id',
                'address' => 'Jl. Tomang Raya 21-23, Jakarta Barat',
            ],
            [
                'code' => 'SUP-UNIL-01',
                'name' => 'PT Unilever Indonesia Tbk',
                'phone' => '021-80827000',
                'email' => 'suara.konsumen@unilever.com',
                'address' => 'BSD City Kav. Barat Lot B1, Tangerang',
            ],
        ];
        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['code' => $sup['code']], $sup);
        }

        // 7. Products Catalog
        $products = [
            [
                'name' => 'Indomie Goreng Spesial 85g',
                'barcode' => '8998866200222',
                'sku' => 'IDM-MKN-001',
                'category' => 'Makanan & Snack',
                'unit' => 'Pcs',
                'buy_price' => 2800,
                'sell_price' => 3500,
                'stock' => 150,
                'min_stock' => 20,
            ],
            [
                'name' => 'Indomie Kuah Ayam Bawang 69g',
                'barcode' => '8998866200116',
                'sku' => 'IDM-MKN-002',
                'category' => 'Makanan & Snack',
                'unit' => 'Pcs',
                'buy_price' => 2700,
                'sell_price' => 3300,
                'stock' => 120,
                'min_stock' => 20,
            ],
            [
                'name' => 'Chitato Sapi Panggang 68g',
                'barcode' => '8992775311020',
                'sku' => 'IDM-MKN-003',
                'category' => 'Makanan & Snack',
                'unit' => 'Pcs',
                'buy_price' => 9200,
                'sell_price' => 12000,
                'stock' => 45,
                'min_stock' => 10,
            ],
            [
                'name' => 'Silverqueen Milk Chocolate 62g',
                'barcode' => '8991001100221',
                'sku' => 'IDM-MKN-004',
                'category' => 'Makanan & Snack',
                'unit' => 'Pcs',
                'buy_price' => 13500,
                'sell_price' => 17500,
                'stock' => 30,
                'min_stock' => 10,
            ],
            [
                'name' => 'Roma Kelapa Biskuit 300g',
                'barcode' => '8992775411030',
                'sku' => 'IDM-MKN-005',
                'category' => 'Makanan & Snack',
                'unit' => 'Pack',
                'buy_price' => 9000,
                'sell_price' => 11500,
                'stock' => 35,
                'min_stock' => 10,
            ],
            [
                'name' => 'Aqua Air Mineral 600ml',
                'barcode' => '8992775211118',
                'sku' => 'IDM-MNM-001',
                'category' => 'Minuman Segar',
                'unit' => 'Botol',
                'buy_price' => 2800,
                'sell_price' => 4000,
                'stock' => 95,
                'min_stock' => 25,
            ],
            [
                'name' => 'Teh Botol Sosro Kotak 250ml',
                'barcode' => '8992741911028',
                'sku' => 'IDM-MNM-002',
                'category' => 'Minuman Segar',
                'unit' => 'Pack',
                'buy_price' => 3200,
                'sell_price' => 4500,
                'stock' => 70,
                'min_stock' => 15,
            ],
            [
                'name' => 'Ultra Milk Cokelat 250ml',
                'barcode' => '8992753112001',
                'sku' => 'IDM-MNM-003',
                'category' => 'Minuman Segar',
                'unit' => 'Pack',
                'buy_price' => 5500,
                'sell_price' => 7200,
                'stock' => 50,
                'min_stock' => 15,
            ],
            [
                'name' => 'Pocari Sweat Can 330ml',
                'barcode' => '8992761133010',
                'sku' => 'IDM-MNM-004',
                'category' => 'Minuman Segar',
                'unit' => 'Kaleng',
                'buy_price' => 6500,
                'sell_price' => 8500,
                'stock' => 40,
                'min_stock' => 10,
            ],
            [
                'name' => 'Coca-Cola Pet 390ml',
                'barcode' => '8992761222010',
                'sku' => 'IDM-MNM-005',
                'category' => 'Minuman Segar',
                'unit' => 'Botol',
                'buy_price' => 4500,
                'sell_price' => 6000,
                'stock' => 45,
                'min_stock' => 10,
            ],
            [
                'name' => 'Kopi Kenangan Mantancino 220ml',
                'barcode' => '8992761333019',
                'sku' => 'IDM-MNM-006',
                'category' => 'Minuman Segar',
                'unit' => 'Botol',
                'buy_price' => 7800,
                'sell_price' => 9900,
                'stock' => 30,
                'min_stock' => 10,
            ],
            [
                'name' => 'Minyak Goreng Bimoli Pouch 2L',
                'barcode' => '8992775611040',
                'sku' => 'IDM-SMB-001',
                'category' => 'Sembako & Kebutuhan Dapur',
                'unit' => 'Pack',
                'buy_price' => 34000,
                'sell_price' => 39000,
                'stock' => 40,
                'min_stock' => 10,
            ],
            [
                'name' => 'Gula Pasir Gulaku Premium 1kg',
                'barcode' => '8992775711050',
                'sku' => 'IDM-SMB-002',
                'category' => 'Sembako & Kebutuhan Dapur',
                'unit' => 'Pack',
                'buy_price' => 15000,
                'sell_price' => 18000,
                'stock' => 50,
                'min_stock' => 10,
            ],
            [
                'name' => 'Beras Ramos Setra Indomaret 5kg',
                'barcode' => '8992775811060',
                'sku' => 'IDM-SMB-003',
                'category' => 'Sembako & Kebutuhan Dapur',
                'unit' => 'Pack',
                'buy_price' => 68000,
                'sell_price' => 75000,
                'stock' => 25,
                'min_stock' => 5,
            ],
            [
                'name' => 'Sampoerna A Mild 16 Batang',
                'barcode' => '8992775911070',
                'sku' => 'IDM-RKK-001',
                'category' => 'Rokok & Tembakau',
                'unit' => 'Pack',
                'buy_price' => 31000,
                'sell_price' => 34500,
                'stock' => 60,
                'min_stock' => 15,
            ],
            [
                'name' => 'Pepsodent Pencegah Gigi Berlubang 190g',
                'barcode' => '8999999052210',
                'sku' => 'IDM-PRW-001',
                'category' => 'Perawatan Diri',
                'unit' => 'Pcs',
                'buy_price' => 14000,
                'sell_price' => 17500,
                'stock' => 28,
                'min_stock' => 8,
            ],
            [
                'name' => 'Lifebuoy Sabun Cair Total 10 400ml',
                'barcode' => '8999999553311',
                'sku' => 'IDM-PRW-002',
                'category' => 'Perawatan Diri',
                'unit' => 'Pack',
                'buy_price' => 22000,
                'sell_price' => 27500,
                'stock' => 18,
                'min_stock' => 5,
            ],
            [
                'name' => 'Panadol Extra Merah 10 Kaplet',
                'barcode' => '8992775922080',
                'sku' => 'IDM-OBT-001',
                'category' => 'Obat & Kesehatan',
                'unit' => 'Pack',
                'buy_price' => 12500,
                'sell_price' => 15000,
                'stock' => 45,
                'min_stock' => 10,
            ],
            [
                'name' => 'Tolak Angin Sido Muncul Box 5 Sachet',
                'barcode' => '8992775933090',
                'sku' => 'IDM-OBT-002',
                'category' => 'Obat & Kesehatan',
                'unit' => 'Dus',
                'buy_price' => 18000,
                'sell_price' => 22500,
                'stock' => 35,
                'min_stock' => 10,
            ],
            [
                'name' => 'Tissue Indomaret Facial 220s',
                'barcode' => '8992775944100',
                'sku' => 'IDM-RMT-001',
                'category' => 'Kebutuhan Rumah Tangga',
                'unit' => 'Pack',
                'buy_price' => 10000,
                'sell_price' => 13000,
                'stock' => 55,
                'min_stock' => 15,
            ],
        ];

        foreach ($products as $prod) {
            $catId = $categoryModels[$prod['category']]->id;
            $unitId = $unitModels[$prod['unit']]->id;

            $created = Product::firstOrCreate(
                ['barcode' => $prod['barcode']],
                [
                    'name' => $prod['name'],
                    'sku' => $prod['sku'],
                    'category_id' => $catId,
                    'unit_id' => $unitId,
                    'buy_price' => $prod['buy_price'],
                    'sell_price' => $prod['sell_price'],
                    'stock' => $prod['stock'],
                    'min_stock' => $prod['min_stock'],
                    'is_active' => true,
                ]
            );

            // Record initial stock mutation
            StockMutation::firstOrCreate(
                [
                    'product_id' => $created->id,
                    'reference_type' => 'initial_seed',
                    'reference_id' => 1,
                ],
                [
                    'type' => 'in',
                    'quantity' => $prod['stock'],
                    'stock_before' => 0,
                    'stock_after' => $prod['stock'],
                    'description' => 'Saldo awal stok sistem toko',
                ]
            );
        }
    }
}
