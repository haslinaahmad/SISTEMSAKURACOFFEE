@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2>Riwayat Transaksi</h2>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th>Metode</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td><a href="{{ route('transactions.show', $trx->id) }}" class="text-primary font-weight-bold">{{ $trx->invoice_number }}</a></td>
                <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                <td>{{ $trx->user->name }}</td>
                <td>{{ $trx->customer_name }}</td>
                <td>Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                <td>{{ $trx->payment_method }}</td>
                <td>
                    <a href="{{ route('transactions.show', $trx->id) }}" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('transactions.print', $trx->id) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $transactions->links() }}</div>
</div>
@endsection