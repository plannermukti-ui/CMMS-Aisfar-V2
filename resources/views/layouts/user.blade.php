@php
    $portalSettings = app(\App\Settings\UserPortalSettings::class);
    $generalSettings = rescue(fn () => app(\App\Settings\GeneralSettings::class), report: false);

    $themeMode = $portalSettings->theme_mode ?? 'light';
    if ($themeMode === 'system') {
        $themeMode = 'light';
    }

    $containerClass = ($portalSettings->container_width === 'fluid') ? 'container-fluid' : 'container-xxl';
    $primaryColor = $portalSettings->primary_color ?? '#009EF7';
    $fontFamily = $portalSettings->font_family ?? 'Inter';

    // 1. Dynamic Favicon & Logo from General Settings (/admin/settings/general-settings-page)
    $companyLogo = $generalSettings?->site_logo ? asset('storage/'.$generalSettings->site_logo) : null;
    $companyName = $generalSettings?->site_name ?: 'CMMS';
    $faviconUrl = $companyLogo ?: asset('assets/metronic/media/logos/favicon.ico');

    // 2. Dynamic Automatic Module Resolution
    $isScm = request()->is('scm*');
    if (request()->is('admin*')) {
        $moduleName = 'Admin';
    } elseif ($isScm) {
        $moduleName = 'SCM';
    } elseif (request()->is('chat*')) {
        $moduleName = 'Chat';
    } elseif (request()->is('profile*')) {
        $moduleName = 'Profile';
    } elseif (request()->is('plt*') || request()->is('user*')) {
        $moduleName = 'Plant';
    } else {
        $moduleName = 'CMMS';
    }

    // 3. Dynamic Automatic Page Title Resolution
    $rawPageTitle = $title ?? trim($__env->yieldContent('title'));

    if (empty($rawPageTitle)) {
        $routeName = request()->route()?->getName();
        if ($routeName) {
            $parts = explode('.', $routeName);
            $action = end($parts);
            $pageTitle = ucwords(str_replace(['-', '_'], ' ', $action));
        } else {
            $lastSegment = last(request()->segments()) ?: 'Dashboard';
            $pageTitle = ucwords(str_replace(['-', '_'], ' ', $lastSegment));
        }
    } else {
        $pageTitle = preg_replace('/^(Plant|SCM|Admin|Portal)\s*[-|:]\s*/i', '', $rawPageTitle);
        $pageTitle = preg_replace('/\s*[-|:]\s*'.preg_quote($companyName, '/').'$/i', '', $pageTitle);
    }

    $fullBrowserTabTitle = "{$moduleName} - {$pageTitle} - {$companyName}";
@endphp
<!DOCTYPE html>
<html lang="id" data-bs-theme="{{ $themeMode }}">
<head>
    <title>{{ $fullBrowserTabTitle }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @if($companyLogo)
        <link rel="icon" type="image/png" href="{{ $faviconUrl }}?v=1" />
        <link rel="shortcut icon" type="image/png" href="{{ $faviconUrl }}?v=1" />
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}?v=1" />
    @else
        <link rel="shortcut icon" href="{{ asset('assets/metronic/media/logos/favicon.ico') }}" />
    @endif
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family={{ $fontFamily }}:300,400,500,600,700" />
    <link href="{{ asset('assets/metronic/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/metronic/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        :root {
            --bs-primary: {{ $primaryColor }};
            --bs-primary-active: {{ $primaryColor }};
            --kt-primary: {{ $primaryColor }};
            --kt-primary-active: {{ $primaryColor }};
        }
        body { font-family: "{{ $fontFamily }}", sans-serif; background-color: #f8fafc; }

        /* ── Page transition ─────────────────────────── */
        #kt_app_content { will-change: opacity, transform; }

        /* ── Livewire wire:loading button state ─────── */
        [wire\:loading][wire\:target],
        button[wire\:loading] { opacity: 0.75; pointer-events: none; cursor: not-allowed; }

        /* ── Toast slide-in animation ────────────────── */
        @keyframes slideInRight {
            from { transform: translateX(120%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }

        /* ── Button wire:loading spinner ─────────────── */
        .btn-loading-state { position: relative; pointer-events: none; }
        .btn-loading-state::after {
            content: '';
            position: absolute; inset: 0;
            background: inherit;
            border-radius: inherit;
            opacity: 0.6;
        }


        /* Base Slim Sidebar Layout */
        .app-sidebar.slim-sidebar {
            width: 90px !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0 16px 0;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }
        
        .slim-sidebar .logo-container {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .slim-sidebar .logo-box {
            width: 48px;
            height: 48px;
            background: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            padding: 4px;
            overflow: hidden;
        }

        .slim-sidebar .logo-box.default-gradient {
            background: linear-gradient(135deg, #009EF7 0%, #0077c5 100%);
            border: none;
        }

        .slim-sidebar .logo-box:hover {
            transform: scale(1.08);
        }

        .slim-sidebar .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 68px;
            border-radius: 12px;
            margin-bottom: 4px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            position: relative;
        }

        .slim-sidebar .menu-section-label {
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 14px;
            margin-bottom: 6px;
            text-align: center;
            width: 100%;
        }
        .theme-scm .slim-sidebar .menu-section-label { color: rgba(255,255,255,0.4); }
        .theme-plant .slim-sidebar .menu-section-label { color: rgba(0,0,0,0.3); }
        
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        .slim-sidebar .menu-item i {
            font-size: 24px !important;
            line-height: 1;
            margin-bottom: 4px;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .slim-sidebar .menu-item:hover i {
            transform: translateY(-2px);
        }
        
        .slim-sidebar .menu-item span {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .slim-sidebar .sidebar-footer {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        /* -------------------------------------------------------------
           1. PLANT MAINTENANCE THEME (Deep Royal Navy / Electric Blue)
        ------------------------------------------------------------- */
        .theme-plant .slim-sidebar {
            background: linear-gradient(180deg, #0f172a 0%, #090e1c 100%);
            border-right: 1px solid #1e293b;
        }
        .theme-plant .slim-sidebar .logo-box {
            box-shadow: 0 4px 14px rgba(27, 132, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .theme-plant .slim-sidebar .menu-item {
            color: #94a3b8;
        }
        .theme-plant .slim-sidebar .menu-item i {
            color: #93c5fd;
        }
        .theme-plant .slim-sidebar .menu-item:hover {
            color: #ffffff;
            background-color: rgba(27, 132, 255, 0.18);
        }
        .theme-plant .slim-sidebar .menu-item:hover i {
            color: #60a5fa;
        }
        .theme-plant .slim-sidebar .menu-item.active {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(27, 132, 255, 0.3) 0%, rgba(13, 110, 253, 0.45) 100%);
            border: 1px solid rgba(96, 165, 250, 0.4);
            box-shadow: 0 4px 14px rgba(27, 132, 255, 0.3);
        }
        .theme-plant .slim-sidebar .menu-item.active i {
            color: #38bdf8 !important;
        }
        .theme-plant .app-header.custom-header {
            border-top: 3px solid #1B84FF !important;
        }
        .theme-plant .module-switch-btn {
            background: rgba(27, 132, 255, 0.1);
            border: 1px solid rgba(27, 132, 255, 0.25);
            color: #93c5fd;
        }
        .theme-plant .module-switch-btn:hover {
            background: rgba(27, 132, 255, 0.25);
            color: #ffffff;
        }

        /* -------------------------------------------------------------
           2. SCM LOGISTICS THEME (Deep Forest Jade / Emerald Green)
        ------------------------------------------------------------- */
        .theme-scm .slim-sidebar {
            background: linear-gradient(180deg, #062a22 0%, #021612 100%);
            border-right: 1px solid #0f4d3e;
        }
        .theme-scm .slim-sidebar .logo-box {
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .theme-scm .slim-sidebar .menu-item {
            color: #94a3b8;
        }
        .theme-scm .slim-sidebar .menu-item i {
            color: #6ee7b7;
        }
        .theme-scm .slim-sidebar .menu-item:hover {
            color: #ffffff;
            background-color: rgba(16, 185, 129, 0.18);
        }
        .theme-scm .slim-sidebar .menu-item:hover i {
            color: #34d399;
        }
        .theme-scm .slim-sidebar .menu-item.active {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.3) 0%, rgba(5, 150, 105, 0.45) 100%);
            border: 1px solid rgba(52, 211, 153, 0.4);
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        }
        .theme-scm .slim-sidebar .menu-item.active i {
            color: #4ade80 !important;
        }
        .theme-scm .app-header.custom-header {
            border-top: 3px solid #10B981 !important;
        }
        .theme-scm .module-switch-btn {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #6ee7b7;
        }
        .theme-scm .module-switch-btn:hover {
            background: rgba(16, 185, 129, 0.25);
            color: #ffffff;
        }

        .module-switch-btn {
            width: 76px;
            padding: 8px 4px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .module-switch-btn span {
            font-size: 10px;
            font-weight: 700;
            margin-top: 3px;
        }
        
        @media (min-width: 992px) {
            .app-wrapper.with-slim-sidebar {
                margin-left: 90px;
            }
        }
        @media (max-width: 991.98px) {
            .app-wrapper.with-slim-sidebar {
                margin-left: 0;
            }
        }

        .app-header.custom-header {
            background-color: #ffffff !important;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.03) !important;
            border-bottom: 1px solid #eef2f5 !important;
            height: 70px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 99;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('css')
</head>
<body id="kt_app_body" class="app-default {{ $isScm ? 'theme-scm' : 'theme-plant' }}">
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            
            <!--begin::Slim Sidebar-->
            <div id="kt_app_sidebar" class="app-sidebar slim-sidebar d-none d-lg-flex">
                <!-- App Logo -->
                <div class="logo-container">
                    <a href="{{ url('/') }}" class="logo-box {{ $companyLogo ? '' : 'default-gradient' }}" title="{{ $companyName }}">
                        @if($companyLogo)
                            <img src="{{ $companyLogo }}" alt="{{ $companyName }}" class="w-100 h-100 object-fit-contain rounded-2" style="image-rendering: -webkit-optimize-contrast; max-height: 40px;" />
                        @else
                            <i class="ki-outline ki-element-11 text-white fs-2"></i>
                        @endif
                    </a>
                </div>
                
                <!-- Menu Items -->
                <div class="w-100 flex-grow-1 overflow-y-auto overflow-x-hidden scrollbar-hide px-2">
                    @if($isScm)
                        <!-- SCM Navigation -->
                        <div class="menu-section-label">Main</div>
                        <a href="{{ route('scm.dashboard') }}" class="menu-item {{ request()->routeIs('scm.dashboard') ? 'active' : '' }}" title="SCM Dashboard">
                            <i class="ki-outline ki-chart-simple-3"></i>
                            <span>Dashboard</span>
                        </a>

                        <div class="menu-section-label">Inventory</div>
                        <a href="{{ route('scm.parts') }}" class="menu-item {{ request()->routeIs('scm.parts') ? 'active' : '' }}" title="Master Spareparts Catalog">
                            <i class="ki-outline ki-cube-2"></i>
                            <span>Spareparts</span>
                        </a>

                        <a href="{{ route('scm.opname') }}" class="menu-item {{ request()->routeIs('scm.opname') ? 'active' : '' }}" title="Stock Opname & Berita Acara">
                            <i class="ki-outline ki-clipboard"></i>
                            <span>Opname</span>
                        </a>
                        
                        <div class="menu-section-label">Purchasing</div>
                        <a href="{{ route('scm.mol') }}" class="menu-item {{ request()->routeIs('scm.mol') ? 'active' : '' }}" title="MOL (Mechanic Order Part)">
                            <i class="ki-outline ki-handcart"></i>
                            <span>MOL</span>
                        </a>

                        <a href="{{ route('scm.pr') }}" class="menu-item {{ request()->routeIs('scm.pr') ? 'active' : '' }}" title="PR (Purchase Request)">
                            <i class="ki-outline ki-document"></i>
                            <span>PR</span>
                        </a>

                        <a href="{{ route('scm.rfq') }}" class="menu-item {{ request()->routeIs('scm.rfq') ? 'active' : '' }}" title="RFQ & Evaluasi Vendor">
                            <i class="ki-outline ki-calculator"></i>
                            <span>RFQ</span>
                        </a>

                        <a href="{{ route('scm.po') }}" class="menu-item {{ request()->routeIs('scm.po') ? 'active' : '' }}" title="PO (Purchase Order)">
                            <i class="ki-outline ki-notepad-edit"></i>
                            <span>PO</span>
                        </a>

                        <div class="menu-section-label">Logistics</div>
                        <a href="{{ route('scm.do') }}" class="menu-item {{ request()->routeIs('scm.do') ? 'active' : '' }}" title="DO (Delivery Order to Site)">
                            <i class="ki-outline ki-delivery-3"></i>
                            <span>Delivery</span>
                        </a>

                        <a href="{{ route('scm.gr') }}" class="menu-item {{ request()->routeIs('scm.gr') ? 'active' : '' }}" title="GR (Goods Receipt Gudang)">
                            <i class="ki-outline ki-package"></i>
                            <span>Receipt</span>
                        </a>

                        <div class="menu-section-label">Team</div>
                        <a href="{{ route('chat') }}" class="menu-item {{ request()->routeIs('chat') ? 'active' : '' }}" title="Team Chat Messenger">
                            <i class="ki-outline ki-messages"></i>
                            <span>Chat</span>
                        </a>
                    @else
                        <!-- PLANT Navigation -->
                        <div class="menu-section-label">Main</div>
                        <a href="{{ route('plt.dashboard') }}" class="menu-item {{ request()->routeIs('plt.dashboard') ? 'active' : '' }}" title="PLANT Dashboard">
                            <i class="ki-outline ki-chart-simple-3"></i>
                            <span>Dashboard</span>
                        </a>

                        <div class="menu-section-label">Maintenance</div>
                        <a href="{{ route('plt.workorder') }}" class="menu-item {{ request()->routeIs('plt.workorder') ? 'active' : '' }}" title="Work Order Operasional">
                            <i class="ki-outline ki-wrench"></i>
                            <span>Work Order</span>
                        </a>

                        <a href="{{ route('plt.components') }}" class="menu-item {{ request()->routeIs('plt.components') ? 'active' : '' }}" title="Component Tracker & Lifecycle">
                            <i class="ki-outline ki-cube-2"></i>
                            <span>Components</span>
                        </a>

                        <div class="menu-section-label">Reports</div>
                        <a href="{{ route('plt.ccr') }}" class="menu-item {{ request()->routeIs('plt.ccr') ? 'active' : '' }}" title="Component Condition Report (CCR)">
                            <i class="ki-outline ki-clipboard"></i>
                            <span>CCR Report</span>
                        </a>

                        <a href="{{ route('plt.far') }}" class="menu-item {{ request()->routeIs('plt.far') ? 'active' : '' }}" title="Failure Analysis Report (FAR / Root Cause)">
                            <i class="ki-outline ki-shield-cross"></i>
                            <span>FAR Failure</span>
                        </a>

                        <a href="{{ route('plt.osr') }}" class="menu-item {{ request()->routeIs('plt.osr') ? 'active' : '' }}" title="Outside Repair Order (OSR / Machine Shop)">
                            <i class="ki-outline ki-delivery-3"></i>
                            <span>OSR</span>
                        </a>

                        <div class="menu-section-label">Team</div>
                        <a href="{{ route('profile') }}" class="menu-item {{ request()->routeIs('profile') ? 'active' : '' }}" title="Profiles Mekanik">
                            <i class="ki-outline ki-profile-circle"></i>
                            <span>Profiles</span>
                        </a>

                        <a href="{{ route('chat') }}" class="menu-item {{ request()->routeIs('chat') ? 'active' : '' }}" title="Team Chat Messenger">
                            <i class="ki-outline ki-messages"></i>
                            <span>Chat</span>
                        </a>
                    @endif
                </div>
                
                <!-- Sidebar Footer (Quick Switcher) -->
                <div class="sidebar-footer pb-4">
                    <a href="{{ $isScm ? route('plt.dashboard') : route('scm.dashboard') }}" class="module-switch-btn" title="{{ $isScm ? 'Beralih ke Modul PLANT Maintenance' : 'Beralih ke Modul SCM Logistik' }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <i class="ki-outline {{ $isScm ? 'ki-wrench' : 'ki-shop' }} fs-2"></i>
                        <span>{{ $isScm ? 'To Plant' : 'To SCM' }}</span>
                    </a>
                </div>
            </div>
            <!--end::Slim Sidebar-->
            
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid with-slim-sidebar" id="kt_app_wrapper">
                
                <!--begin::Header-->
                <div id="kt_app_header" class="app-header custom-header">
                    <div class="app-container {{ $containerClass }} d-flex align-items-stretch justify-content-between">
                        <!-- Header Left: Brand info / Module Switcher -->
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center me-4">
                                <span class="fs-3 fw-bolder text-gray-900 tracking-tight me-2">CMMS</span>
                                @if($isScm)
                                    <span class="badge bg-light-success text-success border border-success border-opacity-25 fw-bolder fs-8 px-2.5 py-1 rounded-pill">
                                        <i class="ki-outline ki-shop text-success fs-7 me-1"></i> SCM LOGISTICS
                                    </span>
                                @elseif(request()->is('chat*'))
                                    <span class="badge bg-light-info text-info border border-info border-opacity-25 fw-bolder fs-8 px-2.5 py-1 rounded-pill">
                                        <i class="ki-outline ki-messages text-info fs-7 me-1"></i> MESSENGER
                                    </span>
                                @elseif(request()->is('profile*'))
                                    <span class="badge bg-light-warning text-warning border border-warning border-opacity-25 fw-bolder fs-8 px-2.5 py-1 rounded-pill">
                                        <i class="ki-outline ki-profile-circle text-warning fs-7 me-1"></i> USER PROFILE
                                    </span>
                                @else
                                    <span class="badge bg-light-primary text-primary border border-primary border-opacity-25 fw-bolder fs-8 px-2.5 py-1 rounded-pill">
                                        <i class="ki-outline ki-wrench text-primary fs-7 me-1"></i> PLANT MAINTENANCE
                                    </span>
                                @endif
                            </div>

                            <!-- Module Switcher Quick Dropdown -->
                            <div class="d-flex align-items-center me-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle py-1.5 px-3 fs-8 fw-bold rounded-pill" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ki-outline {{ $isScm ? 'ki-shop text-success' : 'ki-wrench text-primary' }} fs-6 me-1"></i>
                                        {{ $isScm ? 'Modul: SCM Logistik' : 'Modul: PLANT Maintenance' }}
                                    </button>
                                    <ul class="dropdown-menu shadow-lg border-0 rounded-3 fs-7 py-2">
                                        <li>
                                            <a class="dropdown-item py-2 px-4 {{ !$isScm ? 'active bg-light-primary text-primary fw-bold' : '' }}" href="{{ route('plt.dashboard') }}">
                                                <i class="ki-outline ki-wrench fs-5 me-2 text-primary"></i> <strong>PLANT</strong> (Maintenance & Assets)
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 px-4 {{ $isScm ? 'active bg-light-success text-success fw-bold' : '' }}" href="{{ route('scm.dashboard') }}">
                                                <i class="ki-outline ki-shop fs-5 me-2 text-success"></i> <strong>SCM</strong> (Supply Chain & Logistik)
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a class="dropdown-item py-2 px-4 text-gray-700" href="{{ route('chat') }}">
                                                <i class="ki-outline ki-messages fs-5 me-2 text-info"></i> <strong>Chat Messenger</strong>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Search Bar -->
                            <div class="d-none d-md-flex align-items-center position-relative my-1">
                                <i class="ki-outline ki-magnifier fs-4 position-absolute ms-4 text-gray-500"></i>
                                <input type="text" class="form-control form-control-solid form-control-sm w-175px w-lg-225px ps-11 fs-7 rounded-pill" placeholder="Cari data..." />
                            </div>
                        </div>
                        
                        <!-- Header Right -->
                        <div class="d-flex align-items-center gap-2 gap-lg-3">
                            <a href="{{ route('scm.mol') }}" class="btn btn-sm btn-light-success fw-bold px-3 fs-8 rounded-pill d-none d-sm-inline-flex" title="Buat Request Part">
                                <i class="ki-outline ki-handcart fs-5 me-1"></i> Request Part
                            </a>
                            
                            <a href="{{ route('plt.workorder') }}" class="btn btn-sm btn-light-primary fw-bold px-3 fs-8 rounded-pill d-none d-sm-inline-flex" title="Work Orders">
                                <i class="ki-outline ki-wrench fs-5 me-1"></i> Work Orders
                            </a>

                            <a href="{{ route('chat') }}" class="btn btn-icon btn-sm btn-light-info rounded-circle position-relative" title="Buka Chat Messenger">
                                <i class="ki-outline ki-messages fs-4"></i>
                            </a>
                            
                            <!-- User Profile Dropdown -->
                            <div class="dropdown ms-2">
                                <div class="cursor-pointer symbol symbol-35px symbol-md-40px symbol-circle" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                                    @if(auth()->user()?->photo)
                                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="{{ auth()->user()->full_name }}" class="w-100 h-100 object-fit-cover rounded-circle" />
                                    @else
                                        <div class="symbol-label fs-4 {{ $isScm ? 'bg-light-success text-success' : 'bg-light-primary text-primary' }} fw-bolder">
                                            {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 rounded-4 w-300px mt-3 overflow-hidden">
                                    <!-- User Header Card -->
                                    <div class="p-5 {{ $isScm ? 'bg-light-success' : 'bg-light-primary' }} border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px symbol-circle me-3">
                                                @if(auth()->user()?->photo)
                                                    <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="{{ auth()->user()->full_name }}" class="w-100 h-100 object-fit-cover rounded-circle" />
                                                @else
                                                    <div class="symbol-label fs-3 {{ $isScm ? 'bg-success text-white' : 'bg-primary text-white' }} fw-bold">
                                                        {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->username ?? 'U', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column text-truncate">
                                                <div class="fw-bold fs-6 text-gray-900 text-truncate">{{ auth()->user()->full_name ?? auth()->user()->name ?? 'User' }}</div>
                                                <span class="fs-8 text-muted text-truncate">{{ auth()->user()->email ?? '' }}</span>
                                                <div class="mt-1">
                                                    <span class="badge {{ $isScm ? 'bg-success' : 'bg-primary' }} text-white fs-9 px-2 py-0.5 rounded-pill">
                                                        {{ auth()->user()->roles->first()?->name ?? 'Pengguna' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Menu Links -->
                                    <div class="p-3">
                                        <a href="{{ route('profile') }}" class="dropdown-item py-2 px-3 rounded-2 fs-7 d-flex align-items-center text-gray-800 text-hover-primary">
                                            <i class="ki-outline ki-user fs-4 me-3 text-gray-500"></i>
                                            <span class="fw-semibold">Profil Saya</span>
                                        </a>

                                        <a href="{{ route('chat') }}" class="dropdown-item py-2 px-3 rounded-2 fs-7 d-flex align-items-center text-gray-800 text-hover-primary">
                                            <i class="ki-outline ki-messages fs-4 me-3 text-gray-500"></i>
                                            <span class="fw-semibold">Chat Messenger</span>
                                        </a>

                                        @if(auth()->user()?->hasRole('Super Admin') || auth()->user()?->hasRole('Admin') || auth()->user()?->hasRole('admin'))
                                            <a href="{{ url('/admin') }}" class="dropdown-item py-2 px-3 rounded-2 fs-7 d-flex align-items-center text-primary fw-bold">
                                                <i class="ki-outline ki-category fs-4 me-3 text-primary"></i>
                                                <span>Panel Admin</span>
                                            </a>
                                        @endif

                                        <div class="dropdown-divider my-2"></div>

                                        <!-- Logout Form -->
                                        <form method="POST" action="{{ route('filament.admin.auth.logout') }}" id="header-logout-form">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-2 px-3 rounded-2 fs-7 d-flex align-items-center text-danger w-100 bg-transparent border-0">
                                                <i class="ki-outline ki-entrance-left fs-4 me-3 text-danger"></i>
                                                <span class="fw-bold">Keluar (Sign Out)</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End User Profile Dropdown -->
                            
                        </div>
                    </div>
                </div>
                <!--end::Header-->
                
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container {{ $containerClass }}">
                                @if(isset($slot))
                                    {{ $slot }}
                                @else
                                    @yield('content')
                                @endif
                            </div>
                        </div>
                    </div>
                    <div id="kt_app_footer" class="app-footer custom-footer mt-5 pb-5">
                        <div class="app-container {{ $containerClass }} d-flex flex-column flex-md-row flex-center flex-md-stack">
                            <div class="text-dark order-2 order-md-1">
                                <span class="text-muted fw-semibold me-1">{{ date('Y') }}&copy;</span>
                                <span class="text-gray-800 fw-bold">{{ $companyName }}</span>
                            </div>
                            <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
                                <li class="menu-item"><a href="{{ route('plt.dashboard') }}" class="menu-link px-2">Plant</a></li>
                                <li class="menu-item"><a href="{{ route('scm.dashboard') }}" class="menu-link px-2">SCM</a></li>
                                <li class="menu-item"><a href="{{ route('chat') }}" class="menu-link px-2">Chat</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Wrapper-->
        </div>
    </div>
    
    <script src="{{ asset('assets/metronic/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/metronic/js/scripts.bundle.js') }}"></script>
    @livewireScripts
    @stack('js')
    @if(!request()->routeIs('chat') && !request()->routeIs('plt.chat') && !request()->routeIs('user.chat'))
        @livewire('floating-chat')
    @endif

    {{-- ══════════════════════════════════════════════════════
         GLOBAL UX ENHANCEMENTS:
         1. Page Transition (fade+slide on navigation)
         2. Livewire global loading overlay
         3. Form submit spinner
         4. Flash toast notifications
    ══════════════════════════════════════════════════════ --}}
    <script>
    // ── 1. PAGE TRANSITION ────────────────────────────────────
    (function () {
        const content = document.getElementById('kt_app_content');
        if (!content) return;

        // Fade-in on initial load
        content.style.opacity = '0';
        content.style.transform = 'translateY(10px)';
        content.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                content.style.opacity = '1';
                content.style.transform = 'translateY(0)';
            });
        });

        // Fade-out on link click (non-Livewire)
        document.addEventListener('click', function (e) {
            const anchor = e.target.closest('a[href]');
            if (!anchor) return;
            const href = anchor.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') || anchor.target === '_blank') return;
            if (anchor.closest('[wire\\:click]') || anchor.hasAttribute('wire:navigate')) return;
            content.style.opacity = '0';
            content.style.transform = 'translateY(-6px)';
        });
    })();

    // ── 2. LIVEWIRE GLOBAL LOADING BAR ───────────────────────
    const loadingBar = document.createElement('div');
    loadingBar.id = 'cmms-loading-bar';
    loadingBar.style.cssText = [
        'position:fixed;top:0;left:0;height:3px;width:0%;z-index:99999',
        'background:linear-gradient(90deg,#009EF7,#00c6ff,#009EF7)',
        'background-size:200% 100%;transition:width 0.4s ease',
        'animation:shimmer 1.5s linear infinite',
        'display:none;border-radius:0 2px 2px 0',
    ].join(';');
    document.body.appendChild(loadingBar);

    const style = document.createElement('style');
    style.textContent = '@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}';
    document.head.appendChild(style);

    let loadingTimer;
    document.addEventListener('livewire:request', () => {
        clearTimeout(loadingTimer);
        loadingBar.style.display = 'block';
        loadingBar.style.width = '30%';
        loadingTimer = setTimeout(() => { loadingBar.style.width = '70%'; }, 300);
    });
    document.addEventListener('livewire:response', () => {
        clearTimeout(loadingTimer);
        loadingBar.style.width = '100%';
        setTimeout(() => {
            loadingBar.style.opacity = '0';
            setTimeout(() => { loadingBar.style.display = 'none'; loadingBar.style.width = '0'; loadingBar.style.opacity = '1'; }, 400);
        }, 300);
    });

    // ── 3. FORM SUBMIT SPINNER ───────────────────────────────
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const btn = form.querySelector('[type="submit"]');
        if (!btn) return;
        btn.setAttribute('disabled', 'disabled');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';
        // Re-enable after 8s fallback
        setTimeout(() => {
            btn.removeAttribute('disabled');
            btn.innerHTML = originalHtml;
        }, 8000);
    });

    // ── 4. LIVEWIRE TOAST FLASH ──────────────────────────────
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', (data) => {
            if (typeof KTAlertDialog === 'undefined') return;
            const { message, type } = data[0] || {};
            const toastEl = document.createElement('div');
            const bgMap = { success:'#50cd89', danger:'#f1416c', warning:'#ffc700', info:'#009EF7' };
            toastEl.style.cssText = [
                'position:fixed;bottom:24px;right:24px;z-index:99999',
                'background:'+(bgMap[type]||bgMap.info),
                'color:#fff;padding:14px 20px;border-radius:10px',
                'font-size:14px;font-weight:600;box-shadow:0 8px 32px rgba(0,0,0,.18)',
                'animation:slideInRight .35s ease',
                'max-width:360px;display:flex;align-items:center;gap:10px',
            ].join(';');
            toastEl.innerHTML = `<i class="ki-outline ki-${type==='success'?'check-circle':type==='danger'?'cross-circle':type==='warning'?'warning-2':'information-5'} fs-3 text-white"></i><span>${message||''}</span>`;
            document.body.appendChild(toastEl);
            setTimeout(() => { toastEl.style.opacity='0'; toastEl.style.transition='opacity .3s'; setTimeout(() => toastEl.remove(), 300); }, 4000);
        });
    });
    </script>
</body>
</html>

