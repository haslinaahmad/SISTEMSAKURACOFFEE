@extends('layouts.app')

@section('content')
<div class="d-flex justify-between align-center mb-4">
    <h2>Detail Transaksi: {{ $transaction->invoice_number }}</h2>
    
    {{-- PERHATIKAN BAGIAN INI --}}
    {{-- Tombol ini menggunakan tag <a> (Anchor/Link), BUKAN <button> --}}
    {{-- target="_blank" berfungsi agar PDF terbuka di tab baru --}}
    <a href="{{ route('transactions.print', $transaction->id) }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
        <i class="fas fa-print"></i> Cetak Struk
    </a>
</div>

<div class="grid grid-3">
    <!-- Info Utama (Tabel Item) -->
    <div class="card" style="grid-column: span 2;">
        <h4>Item Pembelian</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Subtotal</strong></td>
                    <td>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Pajak (11%)</strong></td>
                    <td>Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right" style="font-size: 1.1em;"><strong>TOTAL</strong></td>
                    <td class="text-primary font-weight-bold" style="font-size: 1.1em;">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Info Pembayaran (Sidebar Kanan) -->
    <div class="card">
        <h4>Info Pembayaran</h4>
        <div class="mt-4">
            <div class="mb-3">
                <small class="text-muted">Metode Pembayaran</small><br>
                <strong>{{ $transaction->payment_method }}</strong>
            </div>
            <div class="mb-3">
                <small class="text-muted">Akun Penerima</small><br>
                <strong>{{ $transaction->account->name ?? 'Kasir' }}</strong>
            </div>
            <div class="mb-3">
                <small class="text-muted">Waktu Transaksi</small><br>
                {{ $transaction->created_at->format('d M Y H:i:s') }}
            </div>
            <div class="mb-3">
                <small class="text-muted">Kasir Bertugas</small><br>
                {{ $transaction->user->name }}
            </div>
            <hr style="border: 0; border-top: 1px dashed #ddd; margin: 15px 0;">
            <div class="mb-2">
                <small class="text-muted">Uang Dibayar</small><br>
                Rp {{ number_format($transaction->cash_amount, 0, ',', '.') }}
            </div>
            <div class="mb-2">
                <small class="text-muted">Kembalian</small><br>
                <span class="text-success font-weight-bold">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection