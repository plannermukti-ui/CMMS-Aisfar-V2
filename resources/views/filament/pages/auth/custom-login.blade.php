<x-filament-panels::page.simple>
    <div class="mascot-container">
        <img src="{{ asset('images/mascot.jpg') }}" alt="Mascot" class="mascot-img">
    </div>

    {{ $this->content }}

    <style>
        /* Force pure black mode for seamless mascot blending */
        :root {
            --fi-bg: 0 0 0; 
        }
        
        body, .fi-body {
            background-color: #000000 !important;
            min-height: 100vh;
        }

        /* Split layout on desktop */
        @media (min-width: 1024px) {
            .fi-simple-page {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                max-width: 100vw !important;
                margin: 0 !important;
                padding: 0 !important;
                min-height: 100vh !important;
            }
            .fi-simple-page-content {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 6rem;
                max-width: 1300px !important;
                width: 100% !important;
                padding: 2rem !important;
            }
            
            .mascot-container {
                flex: 1.2;
                display: flex;
                justify-content: flex-end;
                align-items: center;
                animation: float-in 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            
            .mascot-img {
                max-width: 100%;
                max-height: 85vh;
                object-fit: contain;
                filter: drop-shadow(0 0 40px rgba(59, 130, 246, 0.2));
            }
            
            main.fi-simple-main {
                flex: 1;
                max-width: 420px !important;
                width: 100%;
                margin: 0 !important;
            }
        }

        /* Mobile fallback */
        @media (max-width: 1023px) {
            .mascot-container {
                display: none; 
            }
            .fi-simple-page-content {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
        }

        @keyframes float-in {
            from { opacity: 0; transform: translateX(-40px) scale(0.95); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        /* Dark Frosted Glass for the main layout */
        main.fi-simple-main {
            position: relative;
            z-index: 10;
            animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(15, 23, 42, 0.4) !important;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 40px rgba(59, 130, 246, 0.15);
            border-radius: 1.5rem;
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Glassmorphism form inputs */
        .fi-input-wrp, .fi-select-wrp {
            background: rgba(15, 23, 42, 0.5) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            transition: all 0.3s ease;
        }
        .fi-input-wrp:focus-within, .fi-select-wrp:focus-within {
            border-color: rgba(59, 130, 246, 0.6) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
            background: rgba(15, 23, 42, 0.8) !important;
        }
        
        .fi-input {
            color: #f8fafc !important; /* light text */
        }

        /* Text colors */
        .fi-simple-main h1, .fi-simple-main h2 {
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .fi-simple-main p, .fi-simple-main span, .fi-simple-main label, .fi-simple-main a {
            color: #cbd5e1 !important;
        }
        .fi-simple-main a:hover {
            color: #60a5fa !important;
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
