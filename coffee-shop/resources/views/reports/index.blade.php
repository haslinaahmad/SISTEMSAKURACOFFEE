@extends('layouts.app')

@section('content')
<div class="d-flex justify-between align-center mb-4">
    <h2>Laporan Bisnis</h2>
    <form action="{{ route('reports.index') }}" method="GET" class="d-flex gap-2">
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') ?? date('Y-m-01') }}">
        <span style="align-self:center;">s/d</span>
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') ?? date('Y-m-d') }}">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline">Reset</a>
    </form>
</div>

<div class="grid grid-2 mb-4">
    <div class="card">
        <h3>Ringkasan Penjualan</h3>
        <table class="table">
            <tr>
                <td>Total Transaksi</td>
                <td class="text-right font-weight-bold">{{ $sales->count() }}</td>
            </tr>
            <tr>
                <td>Total Omset (Gross)</td>
                <td class="text-right font-weight-bold text-primary">Rp {{ number_format($sales->sum('total_amount'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pajak</td>
                <td class="text-right">Rp {{ number_format($sales->sum('tax'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bersih (Net Sales)</td>
                <td class="text-right font-weight-bold text-success">Rp {{ number_format($sales->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h3>Arus Kas (Cashflow)</h3>
        <table class="table">
            <tr>
                <td>Pemasukan (Income)</td>
                <td class="text-right text-success">+ Rp {{ number_format($finances->where('type', 'income')->sum('amount'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pengeluaran (Expense)</td>
                <td class="text-right text-danger">- Rp {{ number_format($finances->where('type', 'expense')->sum('amount'), 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 2px solid #eee;">
                <td><strong>Net Profit (Operasional)</strong></td>
                @php 
                    $net = $finances->where('type', 'income')->sum('amount') - $finances->where('type', 'expense')->sum('amount');
                @endphp
                <td class="text-right font-weight-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($net, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="card">
    <h3>Detail Transaksi Penjualan</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Invoice</th>
                    <th>Metode</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $s)
                <tr>
                    <td>{{ $s->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $s->invoice_number }}</td>
                    <td>{{ $s->payment_method }}</td>
                    <td>Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection