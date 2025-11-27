@extends('layouts.app')

@section('content')
<div class="d-flex justify-between align-center mb-4">
    <h2>Manajemen Akun & Kas</h2>
    <button class="btn btn-primary" onclick="document.getElementById('accModal').classList.add('show')"><i class="fas fa-plus"></i> Tambah Akun</button>
</div>

<div class="grid grid-3">
    @foreach($accounts as $acc)
    <div class="card {{ $acc->is_active ? '' : 'text-muted' }}" style="border-left: 5px solid {{ $acc->type == 'cash' ? '#10b981' : ($acc->type == 'bank' ? '#3b82f6' : '#f59e0b') }}">
        <div class="d-flex justify-between">
            <h4 class="mb-0">{{ $acc->name }}</h4>
            <span class="badge badge-warning">{{ strtoupper($acc->type) }}</span>
        </div>
        <small class="text-muted">{{ $acc->account_number ?? 'No Number' }}</small>
        <h2 class="mt-4 text-primary">Rp {{ number_format($acc->balance, 0, ',', '.') }}</h2>
    </div>
    @endforeach
</div>

<!-- Modal -->
<div class="modal" id="accModal">
    <div class="modal-content">
        <div class="d-flex justify-between align-center mb-4">
            <h3>Tambah Akun Baru</h3>
            <button onclick="document.getElementById('accModal').classList.remove('show')" style="background:none; border:none; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Akun</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Kas Kecil, BCA, OVO" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe</label>
                <select name="type" class="form-control">
                    <option value="cash">Tunai (Cash)</option>
                    <option value="bank">Bank Transfer</option>
                    <option value="ewallet">E-Wallet (QRIS)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Saldo Awal</label>
                <input type="number" name="balance" class="form-control" value="0">
            </div>
            <div class="form-group">
                <label class="form-label">Nomor Rekening (Optional)</label>
                <input type="text" name="account_number" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan</button>
        </form>
    </div>
</div>
@endsection