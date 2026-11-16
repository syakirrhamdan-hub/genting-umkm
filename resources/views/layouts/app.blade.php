<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Sistem Toko Genting') - Genting Jaya</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-terracotta: #c2410c;
            --primary-terracotta-dark: #9a3412;
            --primary-terracotta-light: #ffedd5;
            --primary-slate: #0f172a;
            --sidebar-bg: #1e293b;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            min-height: 100vh;
            width: 260px;
            background-color: var(--sidebar-bg);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, #9a3412 0%, #c2410c 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .sidebar-section {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            padding: 1.2rem 1.5rem 0.4rem;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0 0.75rem;
            margin: 0;
        }

        .sidebar-nav .nav-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.85rem;
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.88rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar-nav .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
            transform: translateX(3px);
        }

        .sidebar-nav .nav-link.active {
            color: #ffffff;
            background: linear-gradient(90deg, #ea580c 0%, #c2410c 100%);
            box-shadow: 0 4px 12px rgba(194, 65, 12, 0.35);
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
        }

        /* Page Content Wrapper */
        #page-content-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.85rem 1.75rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* Card Enhancements */
        .card {
            border: 1px solid var(--card-border);
            border-radius: 0.85rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 1.25rem;
            border-top-left-radius: 0.85rem !important;
            border-top-right-radius: 0.85rem !important;
            font-weight: 700;
        }

        .metric-card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
        }

        .metric-card .icon-bubble {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        /* Tables */
        .table {
            vertical-align: middle;
        }
        .table thead th {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1rem;
        }
        .table tbody td {
            padding: 0.85rem 1rem;
            font-size: 0.88rem;
        }

        /* Custom Badges */
        .badge-lunas {
            background-color: #dcfce7;
            color: #15803d;
            font-weight: 600;
        }
        .badge-tempo {
            background-color: #ffedd5;
            color: #c2410c;
            font-weight: 600;
        }
        .badge-belum {
            background-color: #fee2e2;
            color: #b91c1c;
            font-weight: 600;
        }

        /* Action Buttons */
        .btn-terracotta {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: #ffffff;
            border: none;
            font-weight: 600;
        }
        .btn-terracotta:hover {
            background: linear-gradient(135deg, #c2410c 0%, #9a3412 100%);
            color: #ffffff;
        }

        /* Print Media Style */
        @media print {
            #sidebar-wrapper, .top-navbar, .no-print, .btn, footer {
                display: none !important;
            }
            #page-content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
            body {
                background-color: #ffffff !important;
            }
        }

        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                margin-left: -260px;
            }
            #sidebar-wrapper.show {
                margin-left: 0;
            }
            #page-content-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <div id="sidebar-wrapper">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="bi bi-houses-fill fs-4 text-warning"></i>
            <div>
                <span class="d-block lh-1">GENTING JAYA</span>
                <small class="fw-normal text-white-50" style="font-size: 0.68rem; letter-spacing: 0.5px;">SISTEM PENDATAAN & KAS</small>
            </div>
        </a>

        <div class="sidebar-section">Navigasi Utama</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Transaksi & Kasir</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('transactions.create') }}" class="nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}" style="background-color: rgba(234, 88, 12, 0.15); color: #fb923c;">
                    <i class="bi bi-cart-plus-fill"></i>
                    <span>Kasir / Order Baru</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.show') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Penjualan (Faktur)</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>Pengeluaran Toko</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Katalog & Stok</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Produk Genteng & Stok</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i>
                    <span>Kategori Genteng</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('expense-categories.index') }}" class="nav-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}">
                    <i class="bi bi-collection-fill"></i>
                    <span>Kategori Pengeluaran</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Mitra Usaha</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Pelanggan (Customer)</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Pemasok / Pabrik</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Pembukuan & Laporan</div>
        <ul class="sidebar-nav mb-4">
            <li class="nav-item">
                <a href="{{ route('cashflows.index') }}" class="nav-link {{ request()->routeIs('cashflows.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Buku Kas (Arus Kas)</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.profit_loss') }}" class="nav-link {{ request()->routeIs('reports.profit_loss') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Laporan Laba Rugi</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper">
        <!-- Top Navbar -->
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebar-toggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Toko Genting Jaya')</h5>
                    <small class="text-muted">Pendataan Stok, Penjualan, & Keuangan Toko Genteng</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-success fw-semibold shadow-sm d-flex align-items-center gap-1">
                    <i class="bi bi-cart-plus"></i> <span class="d-none d-sm-inline">Kasir Baru</span>
                </a>
                <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-danger fw-semibold shadow-sm d-flex align-items-center gap-1">
                    <i class="bi bi-dash-circle"></i> <span class="d-none d-sm-inline">Catat Pengeluaran</span>
                </a>
                <div class="vr mx-2"></div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.8rem;">
                            AD
                        </div>
                        <span class="d-none d-sm-inline fw-semibold small">Admin Toko</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header">Genting Jaya Administrator</h6></li>
                        <li><a class="dropdown-item small" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item small" href="{{ route('cashflows.index') }}"><i class="bi bi-wallet me-2"></i>Buku Kas</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="p-3 p-lg-4 flex-grow-1">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-1"></i> Mohon periksa kesalahan input berikut:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-center text-muted small mt-auto">
            &copy; {{ date('Y') }} <strong>Toko Genting Jaya</strong> — Sistem Informasi Manajemen Stok, Penjualan & Pembukuan Kas.
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle mobile sidebar
        const toggleBtn = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar-wrapper');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
