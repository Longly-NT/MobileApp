<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Angkor Khmer Cuisine')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --leaf: #355E3B;
            --leaf-dark: #274630;
            --leaf-darker: #1F3924;
            --leaf-tint: #EAF1EA;
            --leaf-tint-strong: #DCEADD;
            --ink: #22261F;
            --muted: #767C74;
            --border: #E7E9E4;
            --bg: #F8F9F6;
            --danger: #B3452E;
            --success: #3E7A4D;
            --radius: 14px;
            --radius-sm: 10px;
            --shadow-sm: 0 1px 2px rgba(31, 41, 24, .05), 0 1px 3px rgba(31, 41, 24, .06);
            --shadow-md: 0 8px 24px rgba(31, 41, 24, .08);
        }

        @view-transition {
            navigation: auto;
        }

        * {
            box-sizing: border-box;
        }

        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation-duration: .18s;
            animation-timing-function: ease-out;
        }

        body {
            background-color: var(--bg);
            color: var(--ink);
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            letter-spacing: -.01em;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 700;
            letter-spacing: -.02em;
            color: var(--ink);
        }

        a {
            color: inherit;
        }

        /* ---------- App shell ---------- */
        .app-shell {
            display: flex;
            align-items: stretch;
            min-height: 100vh;
        }

        /* ---------- Sidebar ---------- */
        .app-sidebar {
            width: 104px;
            flex-shrink: 0;
            background: linear-gradient(180deg, var(--leaf-dark), var(--leaf-darker));
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 22px 0 16px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .app-sidebar .brand {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .12);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 15px;
            margin-bottom: 26px;
            letter-spacing: 0;
        }

        .app-sidebar .nav-rail {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            width: 100%;
            align-items: center;
        }

        .app-sidebar .nav-rail a {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            width: 80px;
            padding: 9px 4px 8px;
            border-radius: var(--radius-sm);
            color: rgba(255, 255, 255, .62);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .01em;
            transition: background-color .18s ease, color .18s ease, transform .18s ease;
        }

        .app-sidebar .nav-rail a svg {
            width: 27px;
            height: 27px;
            transition: transform .18s ease;
        }

        .app-sidebar .nav-rail a:hover {
            color: #fff;
            background: rgba(255, 255, 255, .08);
            transform: translateY(-2px);
        }

        .app-sidebar .nav-rail a:hover svg {
            transform: scale(1.08);
        }

        .app-sidebar .nav-rail a.active {
            color: var(--leaf-darker);
            background: #fff;
        }

        .app-sidebar .rail-spacer {
            flex: 1;
        }

        .app-sidebar .rail-foot {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .app-sidebar .user-chip {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }

        .app-sidebar .logout-btn {
            border: 0;
            background: transparent;
            color: rgba(255, 255, 255, .5);
            width: 64px;
            padding: 8px 4px;
            border-radius: var(--radius-sm);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            font-weight: 600;
        }

        .app-sidebar .logout-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .08);
        }

        .app-sidebar .logout-btn svg {
            width: 20px;
            height: 20px;
        }

        /* ---------- Main area ---------- */
        .app-main {
            flex: 1;
            min-width: 0;
        }

        .app-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 28px;
            background: #fff;
            border-bottom: 1px solid var(--border);
        }

        .app-topbar .role-pill {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--leaf-dark);
            background: var(--leaf-tint);
            padding: 4px 10px;
            border-radius: 999px;
        }

        .app-content {
            padding: 26px 28px 48px;
        }

        /* ---------- Responsive: tablet & mobile ---------- */
        @media (max-width: 768px) {
            .app-shell {
                flex-direction: column;
            }

            .app-sidebar {
                width: 100%;
                height: auto;
                position: sticky;
                top: 0;
                z-index: 20;
                flex-direction: row;
                align-items: center;
                padding: 10px 14px;
                gap: 6px;
                overflow-x: auto;
            }

            .app-sidebar .brand {
                margin-bottom: 0;
                margin-right: 6px;
                flex-shrink: 0;
            }

            .app-sidebar .nav-rail {
                flex-direction: row;
                width: auto;
                gap: 4px;
                overflow-x: auto;
                flex-shrink: 0;
            }

            .app-sidebar .nav-rail a {
                flex-direction: row;
                width: auto;
                white-space: nowrap;
                padding: 7px 10px;
                font-size: 11px;
            }

            .app-sidebar .nav-rail a svg {
                width: 18px;
                height: 18px;
            }

            .app-sidebar .rail-spacer {
                display: none;
            }

            .app-sidebar .rail-foot {
                flex-direction: row;
                width: auto;
                margin-left: auto;
                flex-shrink: 0;
                gap: 8px;
            }

            .app-sidebar .logout-btn {
                flex-direction: row;
                width: auto;
                padding: 6px 8px;
            }

            .app-sidebar .user-chip {
                width: 32px;
                height: 32px;
                font-size: 11px;
            }

            .app-topbar {
                padding: 14px 16px;
                flex-wrap: wrap;
                gap: 8px;
            }

            .app-content {
                padding: 18px 16px 36px;
            }
        }

        @media (max-width: 480px) {
            .app-topbar .role-pill {
                display: none;
            }
        }

        /* ---------- Reusable components ---------- */
        .card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            background: #fff;
            font-weight: 700;
            border-bottom: 1px solid var(--border);
            border-top-left-radius: var(--radius) !important;
            border-top-right-radius: var(--radius) !important;
        }

        .btn {
            border-radius: var(--radius-sm);
            font-weight: 600;
            letter-spacing: -.005em;
        }

        .btn-dark,
        .btn-primary {
            background-color: var(--leaf);
            border-color: var(--leaf);
            color: #fff;
        }

        .btn-dark:hover,
        .btn-primary:hover {
            background-color: var(--leaf-dark);
            border-color: var(--leaf-dark);
            color: #fff;
        }

        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
        }

        .btn-outline-secondary {
            border-color: var(--border);
            color: var(--ink);
        }

        .btn-outline-danger {
            color: var(--danger);
            border-color: var(--danger);
        }

        .btn-outline-danger:hover {
            background-color: var(--danger);
            border-color: var(--danger);
        }

        .form-control,
        .form-select {
            border-radius: var(--radius-sm);
            border-color: var(--border);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--leaf);
            box-shadow: 0 0 0 .2rem var(--leaf-tint);
        }

        .form-label {
            font-weight: 600;
            font-size: 13.5px;
            color: var(--ink);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead.table-light th {
            background: var(--leaf-tint);
            color: var(--leaf-dark);
            font-size: 13.5px;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: none;
            padding: 14px 16px;
        }

        .table td {
            padding: 13px 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border);
            font-size: 13.5px;
            color: var(--ink);
        }

        .table td:first-child {
            font-weight: 600;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr {
            transition: background-color .15s ease;
        }

        .table tbody tr:hover {
            background-color: var(--bg);
        }

        .table .badge {
            border-radius: 999px;
            padding: 5px 12px;
            font-weight: 600;
            font-size: 11px;
        }

        .badge.bg-success {
            background-color: var(--success) !important;
        }

        .badge.bg-danger {
            background-color: var(--danger) !important;
        }

        .badge.bg-secondary {
            background-color: #8B9088 !important;
        }

        .badge.bg-info {
            background-color: var(--leaf) !important;
        }

        .text-success {
            color: var(--success) !important;
        }

        .text-danger {
            color: var(--danger) !important;
        }

        /* ---------- Guest (login) shell ---------- */
        .guest-shell {
            min-height: 100vh;
        }

        .guest-alerts {
            position: fixed;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1080;
            width: min(92vw, 480px);
        }


        /* ---------- Legacy classes kept for staff/tables/index.blade.php ---------- */
        .card-table {
            cursor: pointer;
            transition: transform .12s ease, box-shadow .12s ease;
        }

        .card-table:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .badge-status {
            font-size: .75rem;
        }

        .table-occupied {
            border: 1.5px solid var(--danger);
        }

        .table-available {
            border: 1.5px solid var(--success);
        }
    </style>
    @yield('styles')
</head>

<body>

    @auth
        <div class="app-shell">
            <nav class="app-sidebar">
                <div class="brand" style="background:transparent;">
                    <svg viewBox="0 0 100 100" width="40" height="40" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="47" fill="none" stroke="#C9A24B" stroke-width="3" />
                        <circle cx="50" cy="50" r="40" fill="#5A8567" />
                        <g fill="#C9A24B">
                            <rect x="46" y="30" width="8" height="22" />
                            <rect x="30" y="38" width="7" height="14" />
                            <rect x="63" y="38" width="7" height="14" />
                            <rect x="20" y="44" width="6" height="8" />
                            <rect x="74" y="44" width="6" height="8" />
                            <polygon points="50,16 54,26 46,26" />
                            <polygon points="33.5,26 37,34 30,34" />
                            <polygon points="66.5,26 70,34 63,34" />
                            <polygon points="23,32 26,39 20,39" />
                            <polygon points="77,32 80,39 74,39" />
                            <rect x="18" y="52" width="64" height="4" />
                        </g>
                    </svg>
                </div>

                <ul class="nav-rail">
                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                        <li><a href="{{ route('admin.dashboard') }}"
                                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3.5" y="3.5" width="7" height="7" rx="1.5" />
                                    <rect x="13.5" y="3.5" width="7" height="7" rx="1.5" />
                                    <rect x="3.5" y="13.5" width="7" height="7" rx="1.5" />
                                    <rect x="13.5" y="13.5" width="7" height="7" rx="1.5" />
                                </svg>
                                Dashboard
                            </a></li>
                        <li><a href="{{ route('admin.users.index') }}"
                                class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="8" r="3.25" />
                                    <path d="M2.75 20c.7-3.4 3.2-5.5 6.25-5.5s5.55 2.1 6.25 5.5" />
                                    <circle cx="17.25" cy="8.75" r="2.5" />
                                    <path d="M15.5 14.9c2.55.15 4.55 1.95 5.1 4.6" />
                                </svg>
                                Staff
                            </a></li>
                        <li><a href="{{ route('admin.categories.index') }}"
                                class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M12.6 3.5h5.9a2 2 0 012 2v5.9a2 2 0 01-.6 1.42l-8.6 8.6a2 2 0 01-2.83 0l-6.9-6.9a2 2 0 010-2.83l8.6-8.6a2 2 0 011.42-.6z" />
                                    <circle cx="16.5" cy="7.5" r="1.4" />
                                </svg>
                                Categories
                            </a></li>
                        <li><a href="{{ route('admin.menu-items.index') }}"
                                class="{{ request()->routeIs('admin.menu-items.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M4 4.75h11.25A2.75 2.75 0 0118 7.5v12l-3-2-3 2-3-2-3 2v-12A2.75 2.75 0 014 4.75z" />
                                    <path d="M7.25 8.5h7.5M7.25 11.5h7.5" />
                                </svg>
                                Menu
                            </a></li>
                        <li><a href="{{ route('admin.orders.index') }}"
                                class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 3.5h9.5L19 7v13.5H6z" />
                                    <path d="M15.5 3.5V7H19" />
                                    <path d="M9 12h6M9 15.5h6" />
                                </svg>
                                Orders
                            </a></li>
                        <li><a href="{{ route('admin.reports.index') }}"
                                class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 20V10M11 20V4M18 20v-7" />
                                </svg>
                                Reports
                            </a></li>
                        <li><a href="{{ route('staff.tables.index') }}"
                                class="{{ request()->routeIs('staff.tables.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3.25" y="5" width="17.5" height="14" rx="2" />
                                    <path d="M3.25 10.5h17.5M9.5 10.5V19" />
                                </svg>
                                Tables
                            </a></li>
                        <li><a href="{{ route('chef.orders.index') }}"
                                class="{{ request()->routeIs('chef.orders.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M12 3c1.6 2 1.9 3.4.9 4.9C14.9 8.6 16 10 16 11.8A4 4 0 018 11.8c0-1.4.6-2.3 1.5-3.2C8.3 7.4 8.7 5.4 12 3z" />
                                    <path
                                        d="M7.5 15.5h9a1.5 1.5 0 011.5 1.5v0a3.5 3.5 0 01-3.5 3.5h-5A3.5 3.5 0 016 17v0a1.5 1.5 0 011.5-1.5z" />
                                </svg>
                                Kitchen
                            </a></li>
                    @elseif(auth()->user()->isStaff())
                        <li><a href="{{ route('staff.tables.index') }}"
                                class="{{ request()->routeIs('staff.tables.*') || request()->routeIs('staff.orders.show') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3.25" y="5" width="17.5" height="14" rx="2" />
                                    <path d="M3.25 10.5h17.5M9.5 10.5V19" />
                                </svg>
                                Tables
                            </a></li>
                        <li><a href="{{ route('staff.orders.mine') }}"
                                class="{{ request()->routeIs('staff.orders.mine') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 3.5h9.5L19 7v13.5H6z" />
                                    <path d="M15.5 3.5V7H19" />
                                    <path d="M9 12h6M9 15.5h6" />
                                </svg>
                                My Orders
                            </a></li>
                    @elseif(auth()->user()->isChef())
                        <li><a href="{{ route('chef.orders.index') }}"
                                class="{{ request()->routeIs('chef.orders.*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M12 3c1.6 2 1.9 3.4.9 4.9C14.9 8.6 16 10 16 11.8A4 4 0 018 11.8c0-1.4.6-2.3 1.5-3.2C8.3 7.4 8.7 5.4 12 3z" />
                                    <path
                                        d="M7.5 15.5h9a1.5 1.5 0 011.5 1.5v0a3.5 3.5 0 01-3.5 3.5h-5A3.5 3.5 0 016 17v0a1.5 1.5 0 011.5-1.5z" />
                                </svg>
                                Kitchen
                            </a></li>
                    @endif
                </ul>

                <div class="rail-spacer"></div>

                <div class="rail-foot">
                    <div class="user-chip" title="{{ auth()->user()->name }}">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-btn" type="submit" title="Log out">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 4H6a2 2 0 00-2 2v12a2 2 0 002 2h3M16 16l4-4-4-4M20 12H9" />
                            </svg>
                            Log out
                        </button>
                    </form>
                </div>
            </nav>

            <div class="app-main">
                <div class="app-topbar">
                    <div>
                        <h5 class="mb-0">@yield('title', 'Angkor Khmer Cuisine')</h5>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="role-pill">{{ auth()->user()->role }}</span>
                        <span class="text-muted small">{{ auth()->user()->name }}</span>
                    </div>
                </div>

                <div class="app-content">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    @else
        <div class="guest-shell">
            @if (session('status') || $errors->any())
                <div class="guest-alerts">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')
        </div>
    @endauth

    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius: var(--radius); border: none; box-shadow: var(--shadow-md);">
                <div class="modal-body text-center pt-4 pb-2">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width:48px;height:48px;border-radius:50%;background:#FBEAE5;color:var(--danger);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                            stroke-linecap="round" stroke-linejoin="round" style="width:22px;height:22px;">
                            <path
                                d="M4 7h16M9 7V4.5A1.5 1.5 0 0110.5 3h3A1.5 1.5 0 0115 4.5V7m2 0v12a2 2 0 01-2 2H9a2 2 0 01-2-2V7" />
                            <path d="M10 11v6M14 11v6" />
                        </svg>
                    </div>
                    <p class="mb-0 fw-semibold" id="confirmModalMessage" style="font-size:14.5px;"></p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-outline-danger btn-sm px-3" id="confirmModalOk">Yes,
                        Remove</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Safety net: if anime.js fails to load (blocked, slow network, etc.),
        // define a harmless no-op version so nothing else on the page breaks.
        if (typeof anime === 'undefined') {
            window.anime = function () { return {}; };
            window.anime.timeline = function () {
                return {
                    add: function () { return this; }
                };
            };
            window.anime.stagger = function () { return 0; };
        }
    </script>
    <script>
        (function () {
            const modalEl = document.getElementById('confirmModal');
            if (!modalEl) return;
            const modal = new bootstrap.Modal(modalEl);
            const msgEl = document.getElementById('confirmModalMessage');
            const okBtn = document.getElementById('confirmModalOk');
            let pendingForm = null;

            document.querySelectorAll('form[data-confirm]').forEach(form => {
                form.addEventListener('submit', function (e) {
                    if (form.dataset.confirmed === 'true') return;
                    e.preventDefault();
                    pendingForm = form;
                    msgEl.textContent = form.dataset.confirm;
                    modal.show();
                });
            });

            okBtn.addEventListener('click', function () {
                if (pendingForm) { pendingForm.dataset.confirmed = 'true'; modal.hide(); pendingForm.submit(); }
            });
        })();
    </script>

    @yield('scripts')
</body>

</html>