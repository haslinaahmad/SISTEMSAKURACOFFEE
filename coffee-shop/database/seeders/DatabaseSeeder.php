<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Account;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        User::create([
            'name' => 'Owner Coffee',
            'email' => 'admin@kopsen.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Barista Jhon',
            'email' => 'kasir@kopsen.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);

        // 2. Accounts
        Account::create(['name' => 'Cash Drawer', 'type' => 'cash', 'balance' => 1500000]); // Modal awal
        Account::create(['name' => 'BCA Corporate', 'type' => 'bank', 'balance' => 50000000, 'account_number' => '8273645123']);
        Account::create(['name' => 'QRIS (GoPay/OVO)', 'type' => 'ewallet', 'balance' => 0]);

        // 3. Categories
        $catCoffee = Category::create(['name' => 'Coffee Based', 'slug' => 'coffee-based', 'description' => 'Espresso based drinks']);
        $catNonCoffee = Category::create(['name' => 'Non-Coffee', 'slug' => 'non-coffee', 'description' => 'Tea, Milk, etc']);
        $catFood = Category::create(['name' => 'Pastry & Food', 'slug' => 'pastry-food', 'description' => 'Snacks and heavy meals']);
        $catBeans = Category::create(['name' => 'Coffee Beans', 'slug' => 'coffee-beans', 'description' => 'Whole beans 250gr/1kg']);

        // 4. Products (Real Coffee Shop Data)
        
        // Coffee
        Product::create([
            'category_id' => $catCoffee->id,
            'name' => 'Caffe Latte Ice',
            'code' => 'COF-001',
            'buy_price' => 12000,
            'sell_price' => 28000,
            'stock' => 100,
            'unit' => 'cup',
            'min_stock_alert' => 10
        ]);

        Product::create([
            'category_id' => $catCoffee->id,
            'name' => 'Americano Hot',
            'code' => 'COF-002',
            'buy_price' => 8000,
            'sell_price' => 22000,
            'stock' => 150,
            'unit' => 'cup',
            'min_stock_alert' => 10
        ]);

        Product::create([
            'category_id' => $catCoffee->id,
            'name' => 'Caramel Macchiato',
            'code' => 'COF-003',
            'buy_price' => 15000,
            'sell_price' => 35000,
            'stock' => 80,
            'unit' => 'cup',
            'min_stock_alert' => 10
        ]);

        // Non Coffee
        Product::create([
            'category_id' => $catNonCoffee->id,
            'name' => 'Matcha Latte Premium',
            'code' => 'NC-001',
            'buy_price' => 14000,
            'sell_price' => 32000,
            'stock' => 50,
            'unit' => 'cup',
            'min_stock_alert' => 5
        ]);

        // Food
        Product::create([
            'category_id' => $catFood->id,
            'name' => 'Butter Croissant',
            'code' => 'FD-001',
            'buy_price' => 10000,
            'sell_price' => 25000,
            'stock' => 20,
            'unit' => 'pcs',
            'min_stock_alert' => 5
        ]);

        // Beans
        Product::create([
            'category_id' => $catBeans->id,
            'name' => 'House Blend Arabica 250gr',
            'code' => 'BN-001',
            'buy_price' => 65000,
            'sell_price' => 110000,
            'stock' => 10,
            'unit' => 'pack',
            'min_stock_alert' => 3
        ]);
    }
}