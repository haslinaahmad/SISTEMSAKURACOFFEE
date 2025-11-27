<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Account;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan baris ini ada agar tidak error "Class Pdf not found"

class TransactionController extends Controller
{
    // Halaman POS (Point of Sale)
    public function index()
    {
        $products = Product::where('is_active', true)->where('stock', '>', 0)->get();
        $categories = Category::all();
        $accounts = Account::where('is_active', true)->get();
        
        return view('pos.index', compact('products', 'categories', 'accounts'));
    }

    // Proses Simpan Transaksi (Checkout)
    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'total_amount' => 'required|numeric',
            'cash_amount' => 'required|numeric|gte:total_amount',
            'payment_method' => 'required',
            'account_id' => 'required|exists:accounts,id'
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat Header Transaksi
            $transaction = Transaction::create([
                'invoice_number' => 'TRX-' . date('YmdHis') . '-' . rand(100, 999),
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name ?? 'Guest',
                'subtotal' => $request->subtotal,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'total_amount' => $request->total_amount,
                'cash_amount' => $request->cash_amount,
                'change_amount' => $request->cash_amount - $request->total_amount,
                'payment_method' => $request->payment_method,
                'account_id' => $request->account_id,
                'status' => 'paid',
                'notes' => $request->notes
            ]);

            // 2. Loop Cart Items
            foreach ($request->cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                
                if (!$product || $product->stock < $item['qty']) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                // Simpan Item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $product->sell_price,
                    'subtotal' => $product->sell_price * $item['qty']
                ]);

                // Kurangi Stok
                $oldStock = $product->stock;
                $product->decrement('stock', $item['qty']);

                // Catat Log Pergerakan Stok
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $item['qty'],
                    'stock_before' => $oldStock,
                    'stock_after' => $product->stock,
                    'reference_number' => $transaction->invoice_number,
                    'notes' => 'Penjualan POS'
                ]);
            }

            // 3. Update Saldo Rekening/Kas
            $account = Account::lockForUpdate()->find($request->account_id);
            $account->increment('balance', $request->total_amount);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil!',
                'transaction_id' => $transaction->id,
                'redirect_url' => route('transactions.show', $transaction->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Riwayat Transaksi
    public function history()
    {
        $transactions = Transaction::with('user')->latest()->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    // Detail Transaksi
    public function show($id)
    {
        $transaction = Transaction::with(['items.product', 'user', 'account'])->findOrFail($id);
        return view('transactions.show', compact('transaction'));
    }

    // Cetak Struk PDF
    public function print($id)
    {
        // Ambil data transaksi beserta relasinya
        $transaction = Transaction::with(['items.product', 'user', 'account'])->findOrFail($id);
        
        // Load view receipt yang sudah kita buat
        $pdf = Pdf::loadView('transactions.receipt', compact('transaction'));
        
        // Set ukuran kertas (80mm x Auto) atau (58mm x Auto) sesuai printer thermal
        // array(0, 0, 226.77, 600) -> 226.77 points adalah kira-kira lebar 80mm
        // Tinggi 600 diset agar struk panjang bisa muat (kertas thermal biasanya roll continuous)
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait'); 
        
        // Stream untuk membuka di browser (bukan download)
        return $pdf->stream('Struk-'.$transaction->invoice_number.'.pdf');
    }
}