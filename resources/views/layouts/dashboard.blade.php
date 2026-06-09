<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Preeklampsia CDSS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: var(--color-gray-100);">
    <!-- Sidebar -->
    <div id="sidebar" style="position: fixed; left: 0; top: 0; bottom: 0; width: 280px; background: white; box-shadow: 2px 0 10px rgba(0,0,0,0.05); z-index: 1000; transition: transform 0.3s ease;">
        <!-- Logo -->
        <div style="padding: 2rem 1.5rem; border-bottom: 1px solid var(--color-gray-200);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="medical-icon icon-primary" style="width: 50px; height: 50px; font-size: 25px;">
                    🏥
                </div>
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--color-gray-800); margin: 0;">CDSS</h2>
                    <p style="font-size: 0.75rem; color: var(--color-gray-500); margin: 0;">Preeklampsia</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav style="padding: 1.5rem 0;">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>
            @if(auth()->user()->isPuskesmas())
            <a href="{{ route('assessments.create') }}" class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Skrining
            </a>
            @endif
            <a href="{{ route('patients.index') }}" class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Data Pasien
            </a>
            @if(auth()->user()->isDinas())
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Laporan
            </a>
            @endif
            @if(auth()->user()->isPuskesmas())
            <a href="{{ route('training-data.index') }}" class="nav-link {{ request()->routeIs('training-data.*') ? 'active' : '' }}">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Training
            </a>
            @endif
        </nav>

        <!-- User Info -->
        <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 1.5rem; border-top: 1px solid var(--color-gray-200);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #EC407A, #880E4F); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div style="flex: 1;">
                    <p style="font-weight: 600; color: var(--color-gray-800); margin: 0; font-size: 0.9rem;">{{ auth()->user()->name }}</p>
                    <p style="font-size: 0.75rem; color: var(--color-gray-500); margin: 0;">{{ auth()->user()->role_label }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline w-full" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div style="margin-left: 280px; min-height: 100vh;">
        <!-- Top Bar -->
        <div style="background: white; padding: 1.5rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--color-gray-800); margin: 0;">@yield('page-title', 'Dashboard')</h1>
                    <p style="color: var(--color-gray-500); font-size: 0.875rem; margin: 0.25rem 0 0 0;">@yield('page-subtitle', 'Selamat datang di Preeklampsia CDSS')</p>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="position: relative;">
                        <svg style="width: 24px; height: 24px; color: var(--color-gray-400); cursor: pointer;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span style="position: absolute; top: -4px; right: -4px; width: 8px; height: 8px; background: var(--color-medical-danger); border-radius: 50%;"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div style="padding: 2rem;">
            @yield('content')
        </div>
    </div>

    <style>
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: var(--color-gray-600);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: var(--color-gray-50);
            color: var(--color-medical-primary);
            border-left-color: var(--color-medical-primary);
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(216, 27, 96, 0.1), transparent);
            color: var(--color-medical-primary);
            border-left-color: var(--color-medical-primary);
        }

        .nav-link svg {
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.open {
                transform: translateX(0);
            }

            div[style*="margin-left: 280px"] {
                margin-left: 0 !important;
            }
        }
    </style>
</body>
</html>
