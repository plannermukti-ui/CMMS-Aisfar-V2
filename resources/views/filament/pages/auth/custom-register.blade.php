<x-filament-panels::page.simple>
    <div class="auth-custom-bg"></div>
    {{ $this->content }}

    <style>
        /* Force light mode for auth pages to match the character image */
        :root {
            --fi-bg: 255 255 255; 
        }
        
        body, .fi-body {
            background: transparent !important;
            background-color: transparent !important;
        }

        /* Character Background */
        .auth-custom-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            background-image: url('{{ asset('images/auth-character.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Light frosted glass entrance animation for the main layout */
        main.fi-simple-main {
            position: relative;
            z-index: 10;
            animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.65) !important;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1), 0 0 30px rgba(255, 255, 255, 0.6);
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
        .fi-input-wrp, .fi-select-wrp {
            background: rgba(255, 255, 255, 0.8) !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease;
        }
        .fi-input-wrp:focus-within, .fi-select-wrp:focus-within {
            border-color: rgba(59, 130, 246, 0.5) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
            background: rgba(255, 255, 255, 1) !important;
        }
        
        .fi-input {
            color: #0f172a !important; /* dark text */
        }

        /* Text colors override for the light glass theme */
        .fi-simple-main h1, .fi-simple-main h2 {
            color: #0f172a !important;
            text-shadow: none;
        }
        .fi-simple-main p, .fi-simple-main span, .fi-simple-main label {
            color: #334155 !important;
        }
        
        /* Brand Logo animation */
        .fi-logo {
            animation: pulse-glow 3s infinite alternate;
        }
        
        @keyframes pulse-glow {
            from { filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.2)); }
            to { filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.5)); }
        }
        
        /* Primary button cool effect */
        button[type="submit"] {
            background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3) !important;
            transition: all 0.3s ease !important;
            color: white !important;
        }
        
        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4) !important;
            background: linear-gradient(135deg, #4f46e5, #3b82f6) !important;
        }
    </style>
</x-filament-panels::page.simple>
