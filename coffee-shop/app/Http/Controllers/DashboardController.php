<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Debt;
use App\Models\Finance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Summary Cards
        $stats = [
            'sales_today' => Transaction::whereDate('created_at', $today)->sum('total_amount'),
            'sales_month' => Transaction::whereBetween('created_at', [$startOfMonth, Carbon::now()])->sum('total_amount'),
            'total_products' => Product::count(),
            'low_stock_count' => Product::whereColumn('stock', '<=', 'min_stock_alert')->count(),
            'pending_receivables' => Debt::where('type', 'receivable')->whereIn('status', ['pending', 'partial'])->sum('remaining_amount'),
            'pending_payables' => Debt::where('type', 'payable')->whereIn('status', ['pending', 'partial'])->sum('remaining_amount'),
        ];

        // 2. Chart: Penjualan 7 Hari Terakhir
        $salesChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $salesChart['labels'][] = $date->format('d M');
            $salesChart['data'][] = Transaction::whereDate('created_at', $date)->sum('total_amount');
        }

        // 3. Produk Terlaris Bulan Ini
        $topProducts = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$startOfMonth, Carbon::now()])
            ->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 4. Transaksi Terakhir
        $recentTransactions = Transaction::with('user')->latest()->limit(5)->get();

        return view('dashboard.index', compact('stats', 'salesChart', 'topProducts', 'recentTransactions'));
    }
}