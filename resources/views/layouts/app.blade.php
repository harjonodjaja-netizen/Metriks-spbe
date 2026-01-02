<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- ✅ ADD THIS -->
    <title>Task Management</title>

    <!-- Link ke Bootstrap CSS (Versi Terbaru) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- CSS Kustom -->
    <style>
        /* Palet warna */
        :root {
            --primary-color: #007bff;
            --secondary-color: #28a745;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --danger-color: #dc3545;
        }

        /* Navbar */
        .navbar {
            background-color: var(--primary-color);
            padding: 4px 15px;
        }

        .navbar a {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .navbar a:hover {
            color: #f8f9fa;
        }

        /* ✅ Logo Styling - BIGGER SIZE */
        .navbar-brand {
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 65px; /* ✅ Diperbesar dari 45px ke 65px */
            width: auto;
            margin-top: -4px; /* Nudge logo down to better center vertically */
        }

        /* ✅ Navbar Items - WIDER TABS */
        .navbar-nav .nav-link {
            position: relative;
            transition: all 0.3s ease;
            padding: 8px 20px; /* Increased padding */
            border-radius: 5px;
            min-width: 120px; /* Add minimum width */
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #ffffff;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }

        /* Active nav item */
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
            font-weight: 600;
        }

        /* Styling untuk container utama */
        .container {
            margin-top: 20px;
        }

        /* Per-page spacing helper (use as needed) */
        .content-top {
            margin-top: 20px;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Tombol "Add New Task" */
        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        /* Alert Success */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        /* Styling untuk input pencarian */
        .search-input {
            border-radius: 25px;
            padding: 10px;
            font-size: 1rem;
            border: 2px solid var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .search-input:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
        }

        /* Styling untuk tabel */
        .table-striped tbody tr:nth-child(odd) {
            background-color: var(--light-gray);
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            vertical-align: middle;
        }

        .table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .table-bordered {
            border: 1px solid #ddd;
        }

        /* ✅ TABLE 1600PX WIDE */
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive .table {
            min-width: 1600px !important;
        }

        /* Scrollbar styling */
        .table-responsive::-webkit-scrollbar {
            height: 10px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Pagination Styling */
        .pagination li a {
            color: var(--primary-color);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
        }

        .pagination li.active a {
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px;
        }

        /* Styling untuk tombol di tabel */
        .btn-warning,
        .btn-danger {
            font-size: 0.875rem;
            padding: 5px 10px;
        }

        /* Modal */
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Styling untuk modal header */
        .modal-header {
            background-color: #f7f7f7;
            border-bottom: 1px solid #ddd;
        }

        /* Styling untuk tombol close modal */
        .btn-close {
            font-size: 1.2rem;
            color: #000;
        }

        .btn-close:hover {
            color: var(--primary-color);
        }

        /* Styling untuk tombol edit dan delete */
        .btn-sm {
            font-size: 0.875rem;
            padding: 6px 12px;
        }

        .btn-sm:hover {
            opacity: 0.8;
        }

        /* Task Details Modal - Notes Fix */
        .modal-body {
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .notes-display {
            max-height: 200px;
            overflow-y: auto;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Mengoptimalkan responsivitas pada perangkat kecil */
        @media (max-width: 768px) {
            .search-input {
                width: 100%;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .pagination {
                font-size: 0.8rem;
            }

            /* ✅ Logo mobile size */
            .navbar-brand img {
                height: 50px; /* Mobile: 50px */
                margin-top: -3px; /* Slight nudge on mobile */
            }

            /* ✅ Navbar items mobile - reduce padding */
            .navbar-nav .nav-link {
                padding: 8px 16px;
                min-width: 100px;
            }

            /* ✅ Table responsive on mobile */
            .table-responsive .table {
                min-width: 100% !important;
            }
        }
    </style>
    
    @stack('styles')
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <!-- ✅ LOGO - Bigger Size -->
            <a class="navbar-brand" href="{{ route('tasks.index') }}">
                <img src="{{ asset('logo.png') }}" alt="Logo">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                            <i class="bi bi-list-task"></i> Task Tracker
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('calendar.index') ? 'active' : '' }}" href="{{ route('calendar.index') }}">
                            <i class="bi bi-calendar-event"></i> Calendar View
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Make layout full-bleed; pages control their own padding -->
    <div class="container-fluid p-0">
        <!-- Optional small wrapper for notifications / small spacing -->
        <div class="content-top">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>

</html>
