@extends('layouts.app')

@section('content')
<style>
    .pos-container { display: flex; gap: 20px; height: calc(100vh - 100px); }
    .pos-products { flex: 2; overflow-y: auto; padding-right: 10px; }
    .pos-cart { flex: 1; display: flex; flex-direction: column; background: white; border-radius: 12px; padding: 20px; box-shadow: var(--shadow-lg); height: 100%; }
    
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; }
    .product-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: var(--shadow-sm); cursor: pointer; transition: 0.2s; border: 1px solid transparent; }
    .product-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: var(--primary); }
    .product-img { height: 120px; width: 100%; object-fit: cover; }
    .product-info { padding: 10px; }
    .product-price { font-weight: bold; color: var(--primary); }
    
    .cart-items { flex: 1; overflow-y: auto; margin: 15px 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee; }
    .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f8fafc; }
    .qty-btn { width: 25px; height: 25px; border-radius: 50%; border: 1px solid #ddd; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .qty-btn:hover { background: #f1f5f9; }

    .category-tabs { display: flex; gap: 10px; margin-bottom: 15px; overflow-x: auto; padding-bottom: 5px; }
    .cat-tab { padding: 8px 16px; border-radius: 20px; background: white; border: 1px solid #e2e8f0; cursor: pointer; white-space: nowrap; transition: 0.2s; }
    .cat-tab.active { background: var(--primary); color: white; border-color: var(--primary); }
    
    /* Modal Styling */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; }
    .modal.show { display: flex; }
    .modal-content { background: white; padding: 25px; border-radius: 16px; width: 400px; max-width: 90%; animation: slideUp 0.3s ease; }
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<div class="pos-container">
    <!-- Left: Product Section -->
    <div class="pos-products">
        <!-- Search & Filter -->
        <div class="d-flex gap-2 mb-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari menu...">
        </div>

        <!-- Categories -->
        <div class="category-tabs" id="categoryTabs">
            <div class="cat-tab active" onclick="filterCategory('all', this)">Semua</div>
            @foreach($categories as $cat)
                <div class="cat-tab" onclick="filterCategory({{ $cat->id }}, this)">{{ $cat->name }}</div>
            @endforeach
        </div>

        <!-- Product Grid -->
        <div class="product-grid" id="productGrid">
            @foreach($products as $product)
                <div class="product-card" data-cat="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}" onclick="addToCart({{ json_encode($product) }})">
                    <img src="{{ $product->image_url }}" class="product-img" alt="{{ $product->name }}">
                    <div class="product-info">
                        <div style="font-weight:600; font-size:0.9rem; margin-bottom:4px; height: 40px; overflow: hidden;">{{ $product->name }}</div>
                        <div class="product-price">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</div>
                        <small class="text-muted">Stok: {{ $product->stock }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right: Cart Section -->
    <div class="pos-cart">
        <div class="d-flex justify-between align-center">
            <h3><i class="fas fa-shopping-cart"></i> Keranjang</h3>
            <button class="btn btn-outline btn-sm text-danger" onclick="clearCart()">Reset</button>
        </div>

        <div class="cart-items" id="cartItems">
            <!-- Cart items will be injected here -->
            <div class="text-center text-muted mt-4">Keranjang kosong</div>
        </div>

        <div class="cart-summary">
            <div class="d-flex justify-between mb-2">
                <span>Subtotal</span>
                <span id="subtotalDisplay">Rp 0</span>
            </div>
            <div class="d-flex justify-between mb-2 text-muted">
                <span>Pajak (11%)</span>
                <span id="taxDisplay">Rp 0</span>
            </div>
            <div class="d-flex justify-between mb-4" style="font-size: 1.2rem; font-weight: bold;">
                <span>Total</span>
                <span id="totalDisplay" class="text-primary">Rp 0</span>
            </div>
            
            <button class="btn btn-primary w-100" id="btnCheckout" onclick="openPaymentModal()" disabled>
                Proses Pembayaran
            </button>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal" id="paymentModal">
    <div class="modal-content">
        <div class="d-flex justify-between align-center mb-4">
            <h3>Pembayaran</h3>
            <button onclick="closePaymentModal()" style="background:none; border:none; cursor:pointer;"><i class="fas fa-times fa-lg"></i></button>
        </div>

        <div class="mb-4 text-center">
            <small class="text-muted">Total Tagihan</small>
            <h1 class="text-primary" id="modalTotalDisplay">Rp 0</h1>
        </div>

        <div class="form-group">
            <label class="form-label">Nama Pelanggan (Optional)</label>
            <input type="text" id="customerName" class="form-control" placeholder="Guest">
        </div>

        <div class="form-group">
            <label class="form-label">Metode Pembayaran</label>
            <div class="grid-3 gap-2">
                <label class="btn btn-outline" style="justify-content:center;">
                    <input type="radio" name="payment_method" value="Cash" checked onchange="toggleCashInput()"> Tunai
                </label>
                <label class="btn btn-outline" style="justify-content:center;">
                    <input type="radio" name="payment_method" value="QRIS" onchange="toggleCashInput()"> QRIS
                </label>
                <label class="btn btn-outline" style="justify-content:center;">
                    <input type="radio" name="payment_method" value="Transfer" onchange="toggleCashInput()"> Transfer
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Masuk ke Akun</label>
            <select id="accountId" class="form-control">
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" id="cashInputGroup">
            <label class="form-label">Uang Diterima</label>
            <input type="number" id="cashAmount" class="form-control" oninput="calculateChange()">
            <div class="d-flex justify-between mt-2">
                <small>Kembalian:</small>
                <strong id="changeDisplay" class="text-success">Rp 0</strong>
            </div>
        </div>

        <button class="btn btn-primary w-100 mt-4" onclick="processTransaction()">
            <i class="fas fa-print"></i> Bayar & Cetak
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // --- State Management ---
    let cart = [];
    const taxRate = 0.11;

    // --- Core POS Logic ---
    function addToCart(product) {
        const existingItem = cart.find(item => item.id === product.id);
        
        if (existingItem) {
            if(existingItem.qty < product.stock) {
                existingItem.qty++;
            } else {
                alert('Stok tidak mencukupi!');
                return;
            }
        } else {
            if(product.stock > 0) {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.sell_price),
                    qty: 1,
                    max_stock: product.stock
                });
            } else {
                alert('Stok habis!');
                return;
            }
        }
        updateCartUI();
    }

    function updateQty(id, change) {
        const item = cart.find(i => i.id === id);
        if(!item) return;

        const newQty = item.qty + change;
        if(newQty > 0 && newQty <= item.max_stock) {
            item.qty = newQty;
        } else if (newQty <= 0) {
            cart = cart.filter(i => i.id !== id);
        } else {
            alert('Stok mentok!');
        }
        updateCartUI();
    }

    function clearCart() {
        if(confirm('Kosongkan keranjang?')) {
            cart = [];
            updateCartUI();
        }
    }

    // --- UI Rendering ---
    function updateCartUI() {
        const cartContainer = document.getElementById('cartItems');
        cartContainer.innerHTML = '';

        let subtotal = 0;

        if (cart.length === 0) {
            cartContainer.innerHTML = '<div class="text-center text-muted mt-4">Keranjang kosong</div>';
            document.getElementById('btnCheckout').disabled = true;
        } else {
            document.getElementById('btnCheckout').disabled = false;
            cart.forEach(item => {
                subtotal += item.price * item.qty;
                cartContainer.innerHTML += `
                    <div class="cart-item">
                        <div>
                            <div style="font-weight:600;">${item.name}</div>
                            <small class="text-muted">Rp ${item.price.toLocaleString()}</small>
                        </div>
                        <div class="d-flex align-center gap-2">
                            <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                            <span style="width:20px; text-align:center;">${item.qty}</span>
                            <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                        </div>
                    </div>
                `;
            });
        }

        const tax = subtotal * taxRate;
        const total = subtotal + tax;

        document.getElementById('subtotalDisplay').innerText = formatRupiah(subtotal);
        document.getElementById('taxDisplay').innerText = formatRupiah(tax);
        document.getElementById('totalDisplay').innerText = formatRupiah(total);
        
        // Update Modal Values too
        document.getElementById('modalTotalDisplay').innerText = formatRupiah(total);
    }

    // --- Filtering ---
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        cards.forEach(card => {
            const name = card.dataset.name;
            if(name.includes(term)) card.style.display = 'block';
            else card.style.display = 'none';
        });
    });

    function filterCategory(catId, element) {
        // Active Tab UI
        document.querySelectorAll('.cat-tab').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        // Filter Logic
        const cards = document.querySelectorAll('.product-card');
        cards.forEach(card => {
            if(catId === 'all' || card.dataset.cat == catId) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // --- Payment Modal Logic ---
    function openPaymentModal() {
        document.getElementById('paymentModal').classList.add('show');
        document.getElementById('cashAmount').value = '';
        document.getElementById('changeDisplay').innerText = 'Rp 0';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.remove('show');
    }

    function toggleCashInput() {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const inputGroup = document.getElementById('cashInputGroup');
        const input = document.getElementById('cashAmount');
        const currentTotal = parseRupiah(document.getElementById('totalDisplay').innerText);

        if(method !== 'Cash') {
            input.value = currentTotal; // Auto fill exact amount
            inputGroup.style.display = 'none';
        } else {
            input.value = '';
            inputGroup.style.display = 'block';
        }
        calculateChange();
    }

    function calculateChange() {
        const total = parseRupiah(document.getElementById('totalDisplay').innerText);
        const cash = parseFloat(document.getElementById('cashAmount').value) || 0;
        const change = cash - total;
        
        const changeEl = document.getElementById('changeDisplay');
        changeEl.innerText = formatRupiah(change);
        
        if(change < 0) changeEl.classList.replace('text-success', 'text-danger');
        else changeEl.classList.replace('text-danger', 'text-success');
    }

    // --- Checkout Process (AJAX) ---
    async function processTransaction() {
        const total = parseRupiah(document.getElementById('totalDisplay').innerText);
        const cash = parseFloat(document.getElementById('cashAmount').value) || 0;
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (cash < total) {
            alert('Uang pembayaran kurang!');
            return;
        }

        const payload = {
            cart: cart,
            subtotal: total / 1.11, // Reverse tax calculation approximation
            tax: total - (total / 1.11),
            total_amount: total,
            cash_amount: cash,
            payment_method: method,
            account_id: document.getElementById('accountId').value,
            customer_name: document.getElementById('customerName').value,
            notes: 'POS Transaction'
        };

        try {
            const response = await fetch('{{ route("pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if(result.status === 'success') {
                // Open Receipt in new tab
                window.open(`transactions/${result.transaction_id}/print`, '_blank');
                // Reload to reset state
                window.location.reload();
            } else {
                alert('Gagal: ' + result.message);
            }

        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan sistem');
        }
    }

    // Helpers
    function formatRupiah(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID');
    }
    
    function parseRupiah(str) {
        return parseFloat(str.replace(/[^0-9,-]+/g,"").replace(',', '.'));
    }
</script>
@endpush