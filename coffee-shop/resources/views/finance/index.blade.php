@extends('layouts.app')

@section('content')
<div class="grid grid-3 mb-4">
    {{-- KARTU SALDO (Warna Coklat Cream) --}}
    <div class="card text-white" style="background: linear-gradient(135deg, #8D6E63 0%, #5D4037 100%); border: none; color:white;">
        <div class="small opacity-75">Saldo Kas Total</div>
        <h2>Rp {{ number_format($accounts->sum('balance'), 0, ',', '.') }}</h2>
    </div>

    <div class="card text-success">
        <div class="small text-muted">Pemasukan Bulan Ini</div>
        <h3><i class="fas fa-arrow-down"></i> Rp {{ number_format($income, 0, ',', '.') }}</h3>
    </div>
    <div class="card text-danger">
        <div class="small text-muted">Pengeluaran Bulan Ini</div>
        <h3><i class="fas fa-arrow-up"></i> Rp {{ number_format($expense, 0, ',', '.') }}</h3>
    </div>
</div>

<div class="d-flex gap-4" style="align-items: flex-start;">
    <!-- Form Input (Sticky) -->
    <div class="card" style="flex: 1; position: sticky; top: 20px;">
        <h3>Catat Transaksi Baru</h3>
        <form action="{{ route('finance.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Tipe Transaksi</label>
                <select name="type" class="form-control" onchange="toggleCategory(this.value)">
                    <option value="expense">Pengeluaran (Expense)</option>
                    <option value="income">Pemasukan (Income)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="category" class="form-control" id="catSelect">
                    <option value="Bahan Baku">Bahan Baku (Restock)</option>
                    <option value="Operasional">Operasional (Listrik/Air)</option>
                    <option value="Gaji">Gaji Karyawan</option>
                    <option value="Marketing">Marketing / Iklan</option>
                    <option value="Maintenance">Maintenance Alat</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Sumber Dana / Akun</label>
                <select name="account_id" class="form-control">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance,0,',','.') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Nominal (Rp)</label>
                <input type="number" name="amount" class="form-control" required min="1">
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Bukti Foto (Optional)</label>
                <input type="file" name="proof" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100">Simpan Data</button>
        </form>
    </div>

    <!-- History List -->
    <div class="card" style="flex: 2;">
        <h3>Riwayat Keuangan</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Akun</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($finances as $item)
                <tr>
                    <td>{{ $item->transaction_date->format('d M Y') }}</td>
                    <td>{{ $item->category }}</td>
                    <td>
                        {{ $item->description }}
                        @if($item->reference_proof)
                            <a href="{{ asset('storage/'.$item->reference_proof) }}" target="_blank" class="text-primary"><i class="fas fa-paperclip"></i></a>
                        @endif
                    </td>
                    <td><span class="badge badge-warning">{{ $item->account->name }}</span></td>
                    <td class="{{ $item->type == 'income' ? 'text-success' : 'text-danger' }} font-weight-bold">
                        {{ $item->type == 'income' ? '+' : '-' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $finances->links() }}
        </div>
    </div>
</div>

<script>
    function toggleCategory(type) {
        const select = document.getElementById('catSelect');
        select.innerHTML = '';
        if(type === 'expense') {
            const opts = ['Bahan Baku', 'Operasional', 'Gaji', 'Marketing', 'Maintenance', 'Lainnya'];
            opts.forEach(o => select.add(new Option(o, o)));
        } else {
            const opts = ['Suntikan Modal', 'Refund', 'Penjualan Aset', 'Lainnya'];
            opts.forEach(o => select.add(new Option(o, o)));
        }
    }
</script>
@endsection