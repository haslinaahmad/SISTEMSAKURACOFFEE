@extends('layouts.app')

@section('content')
{{-- --- HEADER HALAMAN --- --}}
<div class="d-flex justify-between align-center mb-4">
    <div>
        <h2>Manajemen Produk</h2>
        <p class="text-muted">Kelola menu kopi, makanan, dan stok.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('create')">
        <i class="fas fa-plus"></i> Tambah Produk
    </button>
</div>

{{-- --- TABEL PRODUK --- --}}
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Info Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
                    </td>
                    <td>
                        <strong>{{ $product->name }}</strong><br>
                        <small class="text-muted">{{ $product->code }}</small>
                    </td>
                    <td>
                        <span class="badge badge-warning">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    </td>
                    <td>
                        <div class="text-success">Jual: Rp {{ number_format($product->sell_price, 0, ',', '.') }}</div>
                        <small class="text-muted">Beli: Rp {{ number_format($product->buy_price, 0, ',', '.') }}</small>
                    </td>
                    <td>
                        <span class="{{ $product->stock <= $product->min_stock_alert ? 'text-danger fw-bold' : '' }}">
                            {{ $product->stock }} {{ $product->unit }}
                        </span>
                    </td>
                    <td>
                        {{-- Tombol Edit --}}
                        <button class="btn btn-outline btn-sm" onclick="editProduct({{ json_encode($product) }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        {{-- Tombol Hapus --}}
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm text-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted p-4">
                        Belum ada data produk. Silakan tambah data baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- --- MODAL FORM (POP-UP) --- --}}
<div class="modal" id="productModal">
    <div class="modal-content">
        {{-- Header Modal --}}
        <div class="d-flex justify-between align-center mb-4">
            <h3 id="modalTitle" style="margin:0;">Tambah Produk Baru</h3>
            <button type="button" onclick="closeModal()" style="background:none; border:none; cursor:pointer; font-size:1.2rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="methodField"></div>
            
            {{-- GRID 1: Nama & Kode --}}
            <div class="grid-2 mb-4">
                <div class="form-group">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" id="p_name" class="form-control" placeholder="Contoh: Kopi Susu Aren" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kode / SKU</label>
                    <input type="text" name="code" id="p_code" class="form-control" placeholder="Contoh: K-001" required>
                </div>
            </div>

            {{-- GRID 2: Kategori & Satuan --}}
            <div class="grid-2 mb-4">
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" id="p_category" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="unit" id="p_unit" class="form-control" placeholder="Pcs/Cup/Kg" required>
                </div>
            </div>

            {{-- GRID 3: Harga Beli & Jual --}}
            <div class="grid-2 mb-4">
                <div class="form-group">
                    <label class="form-label">Harga Beli (Modal)</label>
                    <input type="number" name="buy_price" id="p_buy" class="form-control" placeholder="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual</label>
                    <input type="number" name="sell_price" id="p_sell" class="form-control" placeholder="0" required>
                </div>
            </div>

            {{-- GRID 4: Stok & Alert --}}
            <div class="grid-2 mb-4">
                <div class="form-group">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" name="stock" id="p_stock" class="form-control" placeholder="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Alert Stok Min.</label>
                    <input type="number" name="min_stock_alert" id="p_min" class="form-control" value="10" required>
                    <small class="text-muted" style="font-size:0.8rem;">Peringatan jika stok di bawah ini</small>
                </div>
            </div>

            {{-- Upload Gambar --}}
            <div class="form-group mb-4">
                <label class="form-label">Gambar Produk</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <small class="text-muted">Format: JPG, PNG. Max 2MB.</small>
            </div>

            {{-- Footer Modal (Tombol) --}}
            <div class="d-flex justify-between mt-4 pt-4" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- --- JAVASCRIPT --- --}}
@push('scripts')
<script>
    function openModal(mode) {
        const modal = document.getElementById('productModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('productForm');
        const methodField = document.getElementById('methodField');

        if (!modal) { console.error('Modal not found'); return; }

        modal.classList.add('show');

        if(mode === 'create') {
            modalTitle.innerText = 'Tambah Produk Baru';
            form.action = "{{ route('products.store') }}";
            form.reset();
            methodField.innerHTML = '';
        }
    }

    function closeModal() {
        const modal = document.getElementById('productModal');
        if (modal) modal.classList.remove('show');
    }

    function editProduct(product) {
        openModal('edit');
        document.getElementById('modalTitle').innerText = 'Edit Produk: ' + product.name;
        document.getElementById('productForm').action = "/products/" + product.id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        // Isi Data ke Form
        document.getElementById('p_name').value = product.name;
        document.getElementById('p_code').value = product.code;
        document.getElementById('p_category').value = product.category_id;
        document.getElementById('p_unit').value = product.unit;
        document.getElementById('p_buy').value = product.buy_price;
        document.getElementById('p_sell').value = product.sell_price;
        document.getElementById('p_stock').value = product.stock; 
        document.getElementById('p_min').value = product.min_stock_alert;
    }

    // Tutup jika klik di luar form
    window.onclick = function(event) {
        const modal = document.getElementById('productModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endpush