@extends('layouts.app')

@section('content')
<div class="d-flex justify-between align-center mb-4">
    <h2>Kategori Produk</h2>
    <button class="btn btn-primary" onclick="openModal('create')"><i class="fas fa-plus"></i> Tambah Kategori</button>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th>Slug</th>
                <th>Deskripsi</th>
                <th>Jumlah Produk</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
            <tr>
                <td><strong>{{ $cat->name }}</strong></td>
                <td>{{ $cat->slug }}</td>
                <td>{{ $cat->description ?? '-' }}</td>
                <td><span class="badge badge-warning">{{ $cat->products_count }} Item</span></td>
                <td>
                    <button class="btn btn-sm btn-outline" onclick="editCat({{ json_encode($cat) }})"><i class="fas fa-edit"></i></button>
                    <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline text-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal" id="catModal">
    <div class="modal-content">
        <div class="d-flex justify-between align-center mb-4">
            <h3 id="modalTitle">Tambah Kategori</h3>
            <button onclick="closeModal()" style="background:none; border:none; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form id="catForm" action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div id="methodField"></div>
            <div class="form-group">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="name" id="c_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" id="c_desc" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan</button>
        </form>
    </div>
</div>

<script>
    function openModal(mode) {
        document.getElementById('catModal').classList.add('show');
        if(mode === 'create') {
            document.getElementById('catForm').action = "{{ route('categories.store') }}";
            document.getElementById('catForm').reset();
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('modalTitle').innerText = 'Tambah Kategori';
        }
    }
    function closeModal() { document.getElementById('catModal').classList.remove('show'); }
    function editCat(cat) {
        openModal('edit');
        document.getElementById('modalTitle').innerText = 'Edit Kategori';
        document.getElementById('catForm').action = "/categories/" + cat.id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('c_name').value = cat.name;
        document.getElementById('c_desc').value = cat.description;
    }
</script>
@endsection