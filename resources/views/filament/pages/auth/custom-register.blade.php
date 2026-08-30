<x-filament-panels::page.simple>
    {{ $this->content }}

    <style>
        /* Force pure black mode */
        :root {
            --fi-bg: 0 0 0; 
        }
        
        body, .fi-body, .fi-simple-layout {
            background-color: #000000 !important;
        }
        
        /* Mascot Background on Desktop */
        @media (min-width: 1024px) {
            body, .fi-body {
                background-image: url('{{ asset('images/mascot.jpg') }}') !important;
                background-size: 55% !important; /* adjust mascot size */
                background-position: left center !important;
                background-repeat: no-repeat !important;
                background-attachment: fixed !important;
            }
            
            /* Push the login card container to the right */
            .fi-simple-main-ctn {
                display: flex !important;
                justify-content: flex-end !important;
                padding-right: 12vw !important; /* spacing from right edge */
            }
            
            main.fi-simple-main {
                width: 450px !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
        }

        /* Mobile fallback */
        @media (max-width: 1023px) {
            body, .fi-body {
                background-image: url('{{ asset('images/mascot.jpg') }}') !important;
                background-position: center top !important;
                background-size: cover !important;
                background-attachment: fixed !important;
            }
            /* Darken the background on mobile so form is readable */
            .fi-simple-layout {
                background-color: rgba(0, 0, 0, 0.75) !important;
            }
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Dark Frosted Glass for the main form layout */
        main.fi-simple-main {
            position: relative;
            z-index: 10;
            animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(15, 23, 42, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8), 0 0 40px rgba(59, 130, 246, 0.2);
            border-radius: 1.5rem;
            padding: 2rem !important; /* Extra padding inside the card */
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
