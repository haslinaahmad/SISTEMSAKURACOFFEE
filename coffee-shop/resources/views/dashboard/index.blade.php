@extends('layouts.app')

@section('content')
<div class="d-flex justify-between align-center mb-4">
    <div>
        <h2>Dashboard Overview</h2>
        <p class="text-muted">Ringkasan performa Sakura Coffee hari ini.</p>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-primary">
        <i class="fas fa-cash-register"></i> Buka Kasir
    </a>
</div>

<!-- Summary Cards -->
<div class="grid grid-4">
    <div class="card">
        <div class="text-muted small">Penjualan Hari Ini</div>
        <div class="d-flex align-center gap-2 mt-4">
            <i class="fas fa-coins fa-2x text-primary"></i>
            <h3>Rp {{ number_format($stats['sales_today'], 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="card">
        <div class="text-muted small">Penjualan Bulan Ini</div>
        <div class="d-flex align-center gap-2 mt-4">
            <i class="fas fa-calendar-alt fa-2x text-success"></i>
            <h3>Rp {{ number_format($stats['sales_month'], 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="card">
        <div class="text-muted small">Total Menu</div>
        <div class="d-flex align-center gap-2 mt-4">
            <i class="fas fa-coffee fa-2x text-warning"></i>
            <h3>{{ $stats['total_products'] }} <span class="small text-muted">Item</span></h3>
        </div>
    </div>
    <div class="card {{ $stats['low_stock_count'] > 0 ? 'border-danger' : '' }}">
        <div class="text-muted small">Stok Menipis</div>
        <div class="d-flex align-center gap-2 mt-4">
            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
            <h3 class="{{ $stats['low_stock_count'] > 0 ? 'text-danger' : '' }}">{{ $stats['low_stock_count'] }} <span class="small text-muted">Item</span></h3>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <!-- Sales Chart -->
    <div class="card">
        <h4>Grafik Penjualan 7 Hari Terakhir</h4>
        <canvas id="salesChart" height="200"></canvas>
    </div>

    <!-- Top Products & Recent Transactions -->
    <div class="d-flex" style="flex-direction: column; gap: 1.5rem;">
        <!-- Top Products -->
        <div class="card" style="flex:1;">
            <h4>Menu Terlaris Bulan Ini</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td><strong>{{ $product->total_qty }}</strong></td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-muted">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Recent Transactions -->
        <div class="card" style="flex:1;">
            <h4>Transaksi Terakhir</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $trx)
                    <tr>
                        <td>
                            <a href="{{ route('transactions.show', $trx->id) }}" class="text-primary" style="text-decoration:none;">
                                {{ $trx->invoice_number }}
                            </a>
                            <br>
                            <small class="text-muted">{{ $trx->created_at->diffForHumans() }}</small>
                        </td>
                        <td>Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        <td><span class="badge badge-success">Paid</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesChart['labels']) !!},
            datasets: [{
                label: 'Total Penjualan (Rp)',
                data: {!! json_encode($salesChart['data']) !!},
                
                // --- PENGATURAN WARNA TEMA COFFEE ---
                borderColor: '#6F4E37',                // Garis Coklat Tua
                backgroundColor: 'rgba(166, 123, 91, 0.2)', // Isi Cream Transparan
                pointBackgroundColor: '#6F4E37',       // Titik Data Coklat
                pointBorderColor: '#fff',              // Border Titik Putih
                // ------------------------------------
                
                borderWidth: 2,
                tension: 0.4, // Garis melengkung halus
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { display: false } 
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' } // Garis grid tipis
                },
                x: {
                    grid: { display: false } // Hilangkan grid vertikal agar bersih
                }
            }
        }
    });
</script>
@endpush