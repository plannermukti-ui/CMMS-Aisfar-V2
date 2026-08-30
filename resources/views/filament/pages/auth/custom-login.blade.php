<x-filament-panels::page.simple>
    <!-- Background Animations -->
    <div class="cyber-grid"></div>
    <div class="glow-orb orb-1"></div>
    <div class="glow-orb orb-2"></div>
    <div class="mascot-layer"></div>

    {{ $this->content }}

    <style>
        /* Industrial Cyber Tech Theme */
        :root {
            --fi-bg: 0 0 0; 
            --industrial-orange: #ff6b00;
            --cyber-cyan: #00f0ff;
        }
        
        body, .fi-body, .fi-simple-layout {
            background-color: #000000 !important;
            overflow-x: hidden;
        }

        /* 1. Cyber Grid Animation */
        .cyber-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 200vw;
            height: 200vh;
            background-image: 
                linear-gradient(rgba(0, 240, 255, 0.15) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 240, 255, 0.15) 1px, transparent 1px);
            background-size: 50px 50px;
            transform: perspective(500px) rotateX(60deg) translateY(-100px) translateZ(-200px);
            animation: gridMove 15s linear infinite;
            z-index: 0;
            opacity: 0.5;
            -webkit-mask-image: linear-gradient(to right, transparent 20%, black 80%);
            mask-image: linear-gradient(to right, transparent 20%, black 80%);
        }

        @keyframes gridMove {
            0% { transform: perspective(500px) rotateX(60deg) translateY(0) translateZ(-200px); }
            100% { transform: perspective(500px) rotateX(60deg) translateY(50px) translateZ(-200px); }
        }

        /* 2. Floating Glowing Orbs (Sparks/Energy) */
        .glow-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            z-index: 0;
            animation: floatOrb 8s infinite alternate ease-in-out;
            opacity: 0.4;
            pointer-events: none;
        }
        .orb-1 {
            width: 400px;
            height: 400px;
            background: var(--industrial-orange);
            top: -10%;
            right: 5%;
        }
        .orb-2 {
            width: 500px;
            height: 500px;
            background: var(--cyber-cyan);
            bottom: -15%;
            right: 15%;
            animation-delay: -4s;
        }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-40px, 50px) scale(1.1); }
        }

        /* 3. Mascot Layer */
        .mascot-layer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            background-image: url('{{ asset('images/mascot.jpg') }}');
            background-size: 55%;
            background-position: 5% center;
            background-repeat: no-repeat;
            pointer-events: none;
            /* Fade right edge to transparent so black bg doesn't cover orbs */
            -webkit-mask-image: linear-gradient(to right, black 50%, transparent 85%);
            mask-image: linear-gradient(to right, black 50%, transparent 85%);
        }

        @media (max-width: 1023px) {
            .mascot-layer {
                background-size: cover;
                background-position: center top;
                opacity: 0.3;
                -webkit-mask-image: none;
                mask-image: none;
            }
            .cyber-grid {
                opacity: 0.2;
                -webkit-mask-image: none;
            }
            .fi-simple-main-ctn {
                background-color: rgba(0, 0, 0, 0.75) !important;
            }
        }

        /* 4. Layout positioning */
        @media (min-width: 1024px) {
            .fi-simple-main-ctn {
                display: flex !important;
                justify-content: flex-end !important;
                padding-right: 10vw !important;
                position: relative;
                z-index: 10;
            }
            main.fi-simple-main {
                width: 460px !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
        }

        /* 5. Stunning Card Form styling */
        main.fi-simple-main {
            position: relative;
            z-index: 10;
            animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            background: rgba(10, 15, 30, 0.7) !important;
            border: 1px solid rgba(0, 240, 255, 0.3);
            box-shadow: 0 0 40px rgba(0, 240, 255, 0.15), inset 0 0 20px rgba(255, 107, 0, 0.05);
            border-radius: 1.5rem;
            padding: 2.5rem !important;
            overflow: hidden;
        }

        /* Scanline effect for high-tech look */
        main.fi-simple-main::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
            background-size: 100% 4px, 3px 100%;
            z-index: -1;
            pointer-events: none;
            opacity: 0.5;
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* 6. Form Inputs & Elements */
        .fi-input-wrp, .fi-select-wrp {
            background: rgba(0, 0, 0, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            transition: all 0.3s ease;
        }
        .fi-input-wrp:focus-within, .fi-select-wrp:focus-within {
            border-color: var(--cyber-cyan) !important;
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.3), inset 0 0 5px rgba(0, 240, 255, 0.2) !important;
            background: rgba(10, 15, 30, 0.8) !important;
        }
        
        .fi-input {
            color: #f8fafc !important;
        }

        .fi-simple-main h1, .fi-simple-main h2 {
            color: #ffffff !important;
            text-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
        }
        .fi-simple-main p, .fi-simple-main span, .fi-simple-main label, .fi-simple-main a {
            color: #cbd5e1 !important;
        }
        .fi-simple-main a:hover {
            color: var(--cyber-cyan) !important;
            text-shadow: 0 0 5px var(--cyber-cyan);
        }
        
        /* Industrial Orange Button */
        button[type="submit"] {
            background: linear-gradient(135deg, var(--industrial-orange), #ff9500) !important;
            border: none !important;
            box-shadow: 0 0 20px rgba(255, 107, 0, 0.4) !important;
            color: #fff !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease !important;
        }
        
        button[type="submit"]:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 0 30px rgba(255, 107, 0, 0.7) !important;
            background: linear-gradient(135deg, #ff9500, var(--industrial-orange)) !important;
        }
    </style>
</x-filament-panels::page.simple>

