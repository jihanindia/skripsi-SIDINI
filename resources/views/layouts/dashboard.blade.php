<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - SIDINI (Sistem Deteksi Dini Preeklampsia)')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: var(--color-gray-100);">
    <!-- Sidebar -->
    <div id="sidebar" style="position: fixed; left: 0; top: 0; bottom: 0; width: 280px; background: linear-gradient(180deg, #fce4ec 0%, #f8bbd0 50%, #f48fb1 100%); box-shadow: 4px 0 20px rgba(236,64,122,0.2); z-index: 1000; transition: transform 0.3s ease;">
        <!-- Logo -->
        <div style="padding: 2rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.2);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="medical-icon icon-primary" style="width: 50px; height: 50px; font-size: 25px;">
                    🏥
                </div>
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #880e4f; margin: 0;">SIDINI</h2>
                    <p style="font-size: 0.75rem; color: #c2185b; margin: 0;">Sistem Deteksi Dini Preeklampsia</p>
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
                Tuning K
            </a>
            
            <div class="dropdown-group {{ request()->routeIs('predictions.*') ? 'open' : '' }}" style="margin-top: 0.25rem;">
                <button class="nav-link dropdown-trigger" onclick="toggleDropdown(this)" style="background: none; border: none; width: 100%; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center; outline: none; color: #ad1457;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <svg style="width: 20px; height: 20px; color: #ad1457; transition: color 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="8" y1="18" x2="8" y2="14"></line>
                            <line x1="12" y1="18" x2="12" y2="10"></line>
                            <line x1="16" y1="18" x2="16" y2="13"></line>
                        </svg>
                        <span style="font-weight: 500; color: #ad1457; transition: color 0.2s;">Prediksi Data</span>
                    </div>
                    <svg class="dropdown-caret" style="width: 16px; height: 16px; color: #c2185b; transition: all 0.3s ease; transform: {{ request()->routeIs('predictions.*') ? 'rotate(180deg)' : 'none' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="submenu-container" style="display: {{ request()->routeIs('predictions.*') ? 'block' : 'none' }};">
                    <a href="{{ route('predictions.training.index') }}" class="submenu-item {{ request()->routeIs('predictions.training.*') ? 'active' : '' }}">
                        <div class="submenu-icon-wrap">
                            <svg style="width: 18px; height: 18px; color: #c2185b;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <span>Prediksi Data Training</span>
                    </a>
                    <a href="{{ route('predictions.test.index') }}" class="submenu-item {{ request()->routeIs('predictions.test.*') ? 'active' : '' }}">
                        <div class="submenu-icon-wrap">
                            <svg style="width: 18px; height: 18px; color: #c2185b;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                        <span>Prediksi Data Testing</span>
                    </a>
                </div>
            </div>
            @endif
        </nav>

        <!-- User Info -->
        <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.2);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #EC407A, #880E4F); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div style="flex: 1;">
                    <p style="font-weight: 600; color: #880e4f; margin: 0; font-size: 0.9rem;">{{ auth()->user()->name }}</p>
                    <p style="font-size: 0.75rem; color: #c2185b; margin: 0;">{{ auth()->user()->role_label }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn w-full" style="padding: 0.5rem 1rem; font-size: 0.875rem; background: linear-gradient(135deg, #c2185b, #880e4f); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(136,14,79,0.3);" onmouseover="this.style.background='linear-gradient(135deg, #880e4f, #560027)'; this.style.boxShadow='0 4px 12px rgba(136,14,79,0.45)'" onmouseout="this.style.background='linear-gradient(135deg, #c2185b, #880e4f)'; this.style.boxShadow='0 2px 8px rgba(136,14,79,0.3)'">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
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
                    <p style="color: var(--color-gray-500); font-size: 0.875rem; margin: 0.25rem 0 0 0;">@yield('page-subtitle', 'Selamat datang di SIDINI (Sistem Deteksi Dini Preeklampsia)')</p>
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
            color: #ad1457;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.5);
            color: #880e4f;
            border-left-color: #c2185b;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.6);
            color: #880e4f;
            border-left-color: #c2185b;
            font-weight: 700;
        }

        .nav-link svg {
            flex-shrink: 0;
        }

        /* Dropdown Sidebar Styles */
        .submenu-container {
            position: relative;
            margin-left: 2.3rem;
            border-left: 2px solid rgba(255,255,255,0.5);
            padding-left: 0.75rem;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .submenu-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.85rem;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            color: rgba(136,14,79,0.8);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            border: 1px solid rgba(194,24,91,0.15);
            box-shadow: 0 1px 2px rgba(136,14,79,0.05);
        }
        .submenu-item:hover, .submenu-item.active {
            background: rgba(255,255,255,0.75);
            color: #880e4f;
            transform: translateX(2px);
            box-shadow: 0 4px 10px rgba(136,14,79,0.1);
        }
        .submenu-item::before {
            content: "";
            position: absolute;
            left: -0.75rem;
            top: 50%;
            width: 0.75rem;
            height: 2px;
            background: rgba(255,255,255,0.6);
        }
        .submenu-icon-wrap {
            background: rgba(255,255,255,0.25);
            border-radius: 6px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .dropdown-group.open .dropdown-trigger {
            color: #880e4f;
        }
        .dropdown-group.open .dropdown-trigger span {
            color: #880e4f !important;
            font-weight: 600;
        }
        .dropdown-group.open .dropdown-trigger svg:first-child {
            color: #880e4f !important;
        }
        .dropdown-group.open .dropdown-caret {
            transform: rotate(180deg) !important;
            color: #880e4f;
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
    <script>
        function toggleDropdown(btn) {
            const group = btn.closest('.dropdown-group');
            const submenu = group.querySelector('.submenu-container');
            const caret = group.querySelector('.dropdown-caret');
            
            if (submenu.style.display === 'none' || submenu.style.display === '') {
                submenu.style.display = 'block';
                group.classList.add('open');
                caret.style.transform = 'rotate(180deg)';
            } else {
                submenu.style.display = 'none';
                group.classList.remove('open');
                caret.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>
</html>
