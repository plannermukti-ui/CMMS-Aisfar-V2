@props([
    'livewire' => null,
])

@php
    $renderHookScopes = $livewire?->getRenderHookScopes();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="cmms-auth-wrapper">
        <!-- 1. Background Cyber/Industrial Grid & Animated Energy Lighting -->
        <div class="cmms-grid-bg"></div>
        <div class="cmms-glow-orb orb-orange"></div>
        <div class="cmms-glow-orb orb-cyan"></div>
        <div class="cmms-hazard-stripe"></div>

        <!-- 2. Main Two-Column Split Container -->
        <div class="cmms-auth-container">
            
            <!-- LEFT COLUMN: Mascot & Plant Info -->
            <div class="cmms-mascot-col">
                <div class="cmms-mascot-wrap">
                    <img src="{{ asset('images/mascot.png') }}" alt="CMMS Aisfar Mascot" class="cmms-mascot-img">
                </div>
            </div>

            <!-- RIGHT COLUMN: Form Card (Distinct, Standout & Professional) -->
            <div class="cmms-form-col">
                <main class="cmms-card-box">
                    {{ $slot }}
                </main>
            </div>

        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}
    </div>

    <style>
        :root {
            --cmms-bg: #070a12;
            --cmms-orange: #ff6a00;
            --cmms-cyan: #00d2ff;
            --cmms-card-bg: rgba(13, 19, 33, 0.85);
            --cmms-border: rgba(0, 210, 255, 0.25);
        }

        body, .fi-body {
            background-color: var(--cmms-bg) !important;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: inherit;
        }

        .cmms-auth-wrapper {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: radial-gradient(circle at 70% 30%, #0d1829 0%, #05070c 100%);
            padding: 2rem 1rem;
            box-sizing: border-box;
        }

        /* 1. Animated Tech Grid */
        .cmms-grid-bg {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(0, 210, 255, 0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 210, 255, 0.07) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
            z-index: 1;
            pointer-events: none;
        }

        /* Ambient Glowing Energy Lights */
        .cmms-glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            z-index: 1;
            pointer-events: none;
            opacity: 0.45;
            animation: orbPulse 8s infinite alternate ease-in-out;
        }
        .orb-orange {
            width: 450px;
            height: 450px;
            background: var(--cmms-orange);
            top: 5%;
            left: 10%;
        }
        .orb-cyan {
            width: 500px;
            height: 500px;
            background: var(--cmms-cyan);
            bottom: 5%;
            right: 10%;
            animation-delay: -4s;
        }

        @keyframes orbPulse {
            0% { transform: scale(1) translateY(0); opacity: 0.35; }
            100% { transform: scale(1.15) translateY(-30px); opacity: 0.55; }
        }

        /* Subtle Mining Hazard Accent on Top Edge */
        .cmms-hazard-stripe {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: repeating-linear-gradient(
                -45deg,
                var(--cmms-orange),
                var(--cmms-orange) 15px,
                #111827 15px,
                #111827 30px
            );
            z-index: 20;
            box-shadow: 0 0 15px rgba(255, 106, 0, 0.5);
        }

        /* 2. Main Flex Layout (Split Screen) */
        .cmms-auth-container {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            max-width: 1300px;
            width: 100%;
            gap: 4rem;
            margin: auto;
        }

        /* Left Side: Mascot */
        .cmms-mascot-col {
            flex: 1.1;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: mascotEntrance 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .cmms-mascot-wrap {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cmms-mascot-img {
            max-width: 100%;
            max-height: 82vh;
            object-fit: contain;
            filter: drop-shadow(0 15px 35px rgba(0, 0, 0, 0.8)) drop-shadow(0 0 25px rgba(0, 210, 255, 0.2));
            border-radius: 1.5rem;
            transition: transform 0.5s ease;
        }

        .cmms-mascot-img:hover {
            transform: scale(1.02);
        }

        @keyframes mascotEntrance {
            from { opacity: 0; transform: translateX(-40px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Right Side: Form Card */
        .cmms-form-col {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 560px;
            width: 100%;
        }

        /* The Standout Card Box */
        .cmms-card-box {
            width: 100%;
            background: var(--cmms-card-bg) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--cmms-border) !important;
            border-radius: 1.5rem;
            padding: 2.5rem 2.25rem;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.7),
                0 0 35px rgba(0, 210, 255, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: cardEntrance 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Header styling inside Card */
        .fi-simple-header {
            margin-bottom: 1.75rem;
            text-align: center;
        }

        .fi-simple-header-heading {
            color: #ffffff !important;
            font-size: 1.75rem !important;
            font-weight: 800 !important;
            letter-spacing: -0.025em;
            margin-top: 0.75rem !important;
            text-shadow: 0 0 20px rgba(0, 210, 255, 0.4);
        }

        .fi-simple-header-subheading {
            color: #94a3b8 !important;
            font-size: 0.875rem !important;
            margin-top: 0.25rem !important;
        }

        .fi-simple-header-subheading a {
            color: var(--cmms-cyan) !important;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .fi-simple-header-subheading a:hover {
            color: #7dd3fc !important;
            text-decoration: underline;
            text-shadow: 0 0 8px rgba(0, 210, 255, 0.6);
        }

        /* Form Inputs High Contrast & Glow */
        .fi-input-wrp, .fi-select-wrp {
            background: rgba(8, 12, 22, 0.8) !important;
            border: 1px solid rgba(148, 163, 184, 0.2) !important;
            border-radius: 0.75rem !important;
            transition: all 0.25s ease !important;
        }

        .fi-input-wrp:focus-within, .fi-select-wrp:focus-within {
            border-color: var(--cmms-cyan) !important;
            box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.2), 0 0 15px rgba(0, 210, 255, 0.3) !important;
            background: rgba(13, 20, 38, 0.95) !important;
        }

        .fi-input {
            color: #f8fafc !important;
            font-size: 0.95rem !important;
        }

        .fi-input::placeholder {
            color: #64748b !important;
        }

        /* Labels */
        .fi-fo-field-wrp-label span, label.fi-fo-field-wrp-label {
            color: #cbd5e1 !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
        }

        /* Checkbox & Links */
        .fi-checkbox-input {
            background-color: rgba(8, 12, 22, 0.8) !important;
            border-color: rgba(148, 163, 184, 0.3) !important;
        }

        .fi-checkbox-input:checked {
            background-color: var(--cmms-orange) !important;
            border-color: var(--cmms-orange) !important;
        }

        .fi-simple-main a {
            color: var(--cmms-cyan) !important;
        }

        /* Primary Action Button (Heavy Machinery Industrial Orange) */
        button[type="submit"] {
            background: linear-gradient(135deg, #ff7b00 0%, #e65100 100%) !important;
            border: none !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 700 !important;
            font-size: 0.95rem !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            color: #ffffff !important;
            box-shadow: 0 4px 20px rgba(255, 106, 0, 0.45) !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            margin-top: 0.5rem !important;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 30px rgba(255, 106, 0, 0.7) !important;
            background: linear-gradient(135deg, #ff9100 0%, #f57c00 100%) !important;
        }

        button[type="submit"]:active {
            transform: translateY(0) !important;
        }

        /* Responsive Mobile Breakpoint */
        @media (max-width: 1023px) {
            .cmms-auth-container {
                flex-direction: column;
                gap: 2rem;
                padding-top: 1rem;
            }

            .cmms-mascot-col {
                display: none; /* Hide on mobile so login form is instantly accessible */
            }

            .cmms-form-col {
                max-width: 440px;
            }

            .cmms-card-box {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</x-filament-panels::layout.base>
