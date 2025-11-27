<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sakura Coffee') }}</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div style="padding: 2rem; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-mug-hot fa-2x"></i>
                <h2 style="margin:0;">Sakura Coffee</h2>
            </div>
            
            <div class="nav-menu">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i> Point of Sale
                </a>
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Produk
                </a>
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Kategori
                </a>
                <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Riwayat Transaksi
                </a>
                <a href="{{ route('finance.index') }}" class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i> Keuangan
                </a>
                <a href="{{ route('debts.index') }}" class="nav-link {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-dollar"></i> Hutang Piutang
                </a>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Laporan
                </a>
                
                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    @csrf
                    <button type="submit" class="nav-link" style="background:none; border:none; width:100%; cursor:pointer;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar Mobile Toggle -->
            <div class="d-flex justify-between align-center mb-4">
                <button id="sidebarToggle" class="btn btn-outline" style="display: none;">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="user-info d-flex align-center gap-2">
                    <span class="text-muted">{{ date('d M Y') }}</span>
                    <div class="badge badge-success">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->name ?? 'Guest' }}
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="card" style="background: #dcfce7; border-left: 4px solid var(--success); padding: 1rem; color: #166534; margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="card" style="background: #fee2e2; border-left: 4px solid var(--danger); padding: 1rem; color: #991b1b; margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="card" style="background: #fee2e2; padding: 1rem; color: #991b1b; margin-bottom: 1rem;">
                    <ul style="margin-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Mobile Sidebar Toggle
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        
        if(window.innerWidth <= 768) {
            toggleBtn.style.display = 'block';
        }

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if(window.innerWidth <= 768 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>