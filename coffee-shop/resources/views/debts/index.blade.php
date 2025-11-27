@extends('layouts.app')

@section('content')
<div class="d-flex justify-between align-center mb-4">
    <div>
        <h2>Manajemen Hutang & Piutang</h2>
        <p class="text-muted">Catat hutang (Payable) dan piutang (Receivable).</p>
    </div>
    <button class="btn btn-primary" onclick="openDebtModal()">
        <i class="fas fa-plus"></i> Catat Baru
    </button>
</div>

<!-- Statistik Ringkas -->
<div class="grid grid-2 mb-4">
    <div class="card border-left-danger">
        <div class="text-muted small">Total Hutang (Harus Dibayar)</div>
        <h3 class="text-danger">Rp {{ number_format($debts->where('type', 'payable')->where('status', '!=', 'paid')->sum('remaining_amount'), 0, ',', '.') }}</h3>
    </div>
    <div class="card border-left-success">
        <div class="text-muted small">Total Piutang (Akan Diterima)</div>
        <h3 class="text-success">Rp {{ number_format($debts->where('type', 'receivable')->where('status', '!=', 'paid')->sum('remaining_amount'), 0, ',', '.') }}</h3>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Jatuh Tempo</th>
                    <th>Tipe</th>
                    <th>Pihak (Nama)</th>
                    <th>Total</th>
                    <th>Sisa (Belum Lunas)</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debts as $debt)
                <tr>
                    <td>
                        {{ $debt->due_date ? $debt->due_date->format('d M Y') : '-' }}
                        @if($debt->due_date && $debt->due_date < now() && $debt->status != 'paid')
                            <span class="badge badge-danger">Overdue</span>
                        @endif
                    </td>
                    <td>
                        @if($debt->type == 'payable')
                            <span class="badge badge-danger">Hutang</span>
                        @else
                            <span class="badge badge-success">Piutang</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $debt->party_name }}</strong><br>
                        <small class="text-muted">{{ $debt->description }}</small>
                    </td>
                    <td>Rp {{ number_format($debt->amount, 0, ',', '.') }}</td>
                    <td class="font-weight-bold">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</td>
                    <td>
                        @if($debt->status == 'paid')
                            <span class="badge badge-success">Lunas</span>
                        @elseif($debt->status == 'partial')
                            <span class="badge badge-warning">Dicicil</span>
                        @else
                            <span class="badge badge-danger">Belum Bayar</span>
                        @endif
                    </td>
                    <td>
                        @if($debt->status != 'paid')
                        <button class="btn btn-primary btn-sm" onclick="openPaymentModal({{ $debt->id }}, '{{ $debt->party_name }}', {{ $debt->remaining_amount }}, '{{ $debt->type }}')">
                            <i class="fas fa-money-bill-wave"></i> Bayar
                        </button>
                        @else
                        <button class="btn btn-outline btn-sm" disabled><i class="fas fa-check"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada data hutang/piutang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $debts->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Hutang/Piutang -->
<div class="modal" id="debtModal">
    <div class="modal-content">
        <div class="d-flex justify-between align-center mb-4">
            <h3>Catat Transaksi Baru</h3>
            <button onclick="closeDebtModal()" style="background:none; border:none; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('debts.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Tipe</label>
                <select name="type" class="form-control">
                    <option value="payable">Hutang (Saya Berhutang)</option>
                    <option value="receivable">Piutang (Orang Berhutang)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Pihak</label>
                <input type="text" name="party_name" class="form-control" placeholder="Nama Supplier / Pelanggan" required>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah (Rp)</label>
                <input type="number" name="amount" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Jatuh Tempo</label>
                <input type="date" name="due_date" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan</button>
        </form>
    </div>
</div>

<!-- Modal Bayar Cicilan -->
<div class="modal" id="paymentModal">
    <div class="modal-content">
        <div class="d-flex justify-between align-center mb-4">
            <h3 id="payModalTitle">Pembayaran</h3>
            <button onclick="closePaymentModal()" style="background:none; border:none; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form id="paymentForm" method="POST">
            @csrf
            <div class="alert alert-info mb-4" id="payModalInfo"></div>
            
            <div class="form-group">
                <label class="form-label">Sumber Dana / Masuk ke Akun</label>
                <select name="account_id" class="form-control">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Jumlah Bayar (Rp)</label>
                <input type="number" name="amount" id="payAmount" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Bayar</label>
                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" class="form-control" placeholder="Cicilan ke-1, Pelunasan, dll">
            </div>

            <button type="submit" class="btn btn-primary w-100">Proses Pembayaran</button>
        </form>
    </div>
</div>

<script>
    function openDebtModal() {
        document.getElementById('debtModal').classList.add('show');
    }
    function closeDebtModal() {
        document.getElementById('debtModal').classList.remove('show');
    }

    function openPaymentModal(id, name, remaining, type) {
        document.getElementById('paymentModal').classList.add('show');
        const form = document.getElementById('paymentForm');
        form.action = `/debts/${id}/payment`; // Set URL dinamis
        
        const title = type === 'payable' ? 'Bayar Hutang' : 'Terima Pembayaran Piutang';
        document.getElementById('payModalTitle').innerText = title;
        document.getElementById('payModalInfo').innerText = `Sisa Tagihan: Rp ${remaining.toLocaleString('id-ID')}`;
        document.getElementById('payAmount').max = remaining; // Cegah bayar lebih
    }
    
    function closePaymentModal() {
        document.getElementById('paymentModal').classList.remove('show');
    }
</script>
@endsection