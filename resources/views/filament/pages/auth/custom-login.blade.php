<x-filament-panels::page.simple>
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}
            {{ $this->registerAction }}
        </x-slot>
    @endif

    <div class="auth-custom-bg"></div>

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <style>
        /* Force dark mode for auth pages to match the cool vibe */
        :root {
            --fi-bg: 15 23 42; /* Tailwind slate-900 */
        }
        
        /* Animated Background */
        .auth-custom-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -10;
            background-image: url('{{ asset('images/auth-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            animation: kenburns 20s ease-out infinite alternate;
        }

        /* Dark overlay */
        .auth-custom-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15,23,42,0.92) 0%, rgba(15,23,42,0.6) 100%);
            z-index: -9;
        }

        @keyframes kenburns {
            0% { transform: scale(1); }
            100% { transform: scale(1.05); }
        }

        /* Cool entrance animation for the main layout */
        main.fi-simple-main {
            animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background: rgba(30, 41, 59, 0.7) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 40px rgba(59, 130, 246, 0.2);
            border-radius: 1.5rem;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Glassmorphism form inputs */
        .fi-input-wrp {
            background: rgba(15, 23, 42, 0.5) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            transition: all 0.3s ease;
        }
        .fi-input-wrp:focus-within {
            border-color: rgba(59, 130, 246, 0.5) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
            background: rgba(15, 23, 42, 0.8) !important;
        }
        
        .fi-input {
            color: #f8fafc !important; /* white text */
        }

        /* Text colors override for the dark glass theme */
        .fi-simple-main h1, .fi-simple-main h2 {
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .fi-simple-main p, .fi-simple-main span, .fi-simple-main label {
            color: #cbd5e1 !important; /* slate-300 */
        }
        
        /* Brand Logo animation */
        .fi-logo {
            animation: pulse-glow 3s infinite alternate;
        }
        
        @keyframes pulse-glow {
            from { filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.4)); }
            to { filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.8)); }
        }
        
        /* Primary button cool effect */
        button[type="submit"] {
            background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4) !important;
            transition: all 0.3s ease !important;
        }
        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.6) !important;
        }
    </style>
</x-filament-panels::page.simple>
