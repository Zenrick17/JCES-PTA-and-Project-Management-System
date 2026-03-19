<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - JCES Elementary School</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
                background: #22b573;
                min-height: 100vh;
                display: flex;
                align-items: stretch;
                justify-content: center;
                padding: 0;
                margin: 0;
            }
            
            .auth-container {
                display: flex;
                width: 100vw;
                max-width: none;
                min-height: 100vh;
                background: white;
                box-shadow: none;
            }
            
            .logo-section {
                flex: 1;
                width: 50%;
                background: #22b573;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 60px;
                position: relative;
                min-height: 100vh;
            }
            
            .jces-logo {
                width: 300px;
                height: 300px;
                background: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                border: 8px solid #1a8f5a;
                position: relative;
                overflow: hidden;
            }
            
            .logo-image {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                object-fit: cover;
                object-position: center;
                padding: 10px;
            }
            
            .logo-inner {
                width: 250px;
                height: 250px;
                border-radius: 50%;
                border: 4px solid #22b573;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 20px;
                position: relative;
                background: radial-gradient(circle, #fff 0%, #f8f9fa 100%);
            }
            
            /* Alternative logo styles for different image formats */
            .logo-image.transparent {
                padding: 20px;
            }
            
            .logo-image.with-background {
                padding: 0;
            }
            
            .school-name-top {
                font-size: 14px;
                font-weight: 700;
                color: #22b573;
                margin-bottom: 5px;
                letter-spacing: 1px;
            }
            
            .school-name-main {
                font-size: 18px;
                font-weight: 800;
                color: #1a8f5a;
                margin-bottom: 8px;
                line-height: 1;
            }
            
            .book-flame-icon {
                width: 60px;
                height: 60px;
                margin: 10px 0;
                background: linear-gradient(45deg, #ff4500 0%, #ffa500 50%, #ff6347 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
            }
            
            .book-flame-icon::before {
                content: "📚";
                font-size: 24px;
                position: absolute;
            }
            
            .book-flame-icon::after {
                content: "🔥";
                font-size: 16px;
                position: absolute;
                top: -5px;
                right: -5px;
            }
            
            .year-badge {
                background: #22b573;
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                margin: 8px 0;
            }
            
            .location-text {
                font-size: 12px;
                color: #1a8f5a;
                font-weight: 600;
                margin-top: 5px;
                letter-spacing: 0.5px;
            }
            
            .wheat-decoration {
                position: absolute;
                color: #22b573;
                font-size: 24px;
            }
            
            .wheat-left {
                left: 15px;
                top: 50%;
                transform: translateY(-50%) rotate(-15deg);
            }
            
            .wheat-right {
                right: 15px;
                top: 50%;
                transform: translateY(-50%) rotate(15deg);
            }
            
            .form-section {
                flex: 1;
                width: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 60px;
                background: white;
                min-height: 100vh;
            }
            
            .form-container {
                width: 100%;
                max-width: 400px;
            }
            
            .form-title {
                font-size: 48px;
                font-weight: 700;
                color: #333;
                margin-bottom: 8px;
                text-align: left;
            }
            
            .form-subtitle {
                color: #666;
                font-size: 16px;
                margin-bottom: 40px;
                text-align: left;
            }
            
            .form-group {
                margin-bottom: 24px;
            }
            
            .form-label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: #333;
                font-size: 14px;
            }
            
            .form-input {
                width: 100%;
                padding: 16px;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 16px;
                transition: all 0.3s ease;
                background: white;
                color: #333;
            }
            
            .form-input::placeholder {
                color: #999;
            }
            
            .form-input:focus {
                outline: none;
                border-color: #22b573;
                box-shadow: 0 0 0 3px rgba(34, 181, 115, 0.1);
            }
            
            .password-container {
                position: relative;
            }
            
            .password-toggle {
                position: absolute;
                right: 16px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                color: #666;
                font-size: 18px;
            }
            
            .form-error {
                color: #dc3545;
                font-size: 14px;
                margin-top: 6px;
                display: block;
            }
            
            .checkbox-group {
                display: flex;
                align-items: center;
                gap: 8px;
                margin: 20px 0;
            }
            
            .checkbox {
                width: 18px;
                height: 18px;
                border: 2px solid #ddd;
                border-radius: 4px;
                cursor: pointer;
                background: white;
            }
            
            .checkbox:checked {
                background: #22b573;
                border-color: #22b573;
            }
            
            .checkbox-label {
                color: #666;
                font-size: 14px;
                cursor: pointer;
            }
            
            .btn-primary {
                width: 100%;
                padding: 16px;
                background: #22b573;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                background: #1a8f5a;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(34, 181, 115, 0.3);
            }
            
            .btn-primary:active {
                transform: translateY(0);
            }
            
            .auth-links {
                text-align: center;
                margin-top: 24px;
            }
            
            .auth-link {
                color: #22b573;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }
            
            .auth-link:hover {
                color: #1a8f5a;
                text-decoration: underline;
            }
            
            .forgot-password {
                text-align: right;
                margin-top: 8px;
            }
            
            .forgot-password a {
                color: #666;
                font-size: 14px;
                text-decoration: none;
            }
            
            .forgot-password a:hover {
                color: #22b573;
            }
            
            .status-message {
                background: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
                padding: 12px 16px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
            }
            
            @media (max-width: 768px) {
                .auth-container {
                    flex-direction: column;
                    max-width: 100%;
                    width: 100vw;
                }
                
                .logo-section {
                    width: 100%;
                    min-height: 40vh;
                    padding: 40px 20px;
                }
                
                .form-section {
                    width: 100%;
                    padding: 40px 20px;
                    background: white;
                }
                
                .jces-logo {
                    width: 200px;
                    height: 200px;
                }
                
                .logo-inner {
                    width: 160px;
                    height: 160px;
                }
                
                .school-name-main {
                    font-size: 14px;
                }
                
                .form-title {
                    font-size: 36px;
                }
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <div class="logo-section">
                <div class="jces-logo">
                    @php
                        $logoPath = null;
                        $logoFormats = ['jces-logo.png', 'jces-logo.jpg', 'jces-logo.jpeg', 'school-logo.png', 'logo.png'];
                        
                        foreach($logoFormats as $format) {
                            if(file_exists(public_path('images/logos/' . $format))) {
                                $logoPath = 'images/logos/' . $format;
                                break;
                            }
                        }
                    @endphp
                    
                    @if($logoPath)
                        <img src="{{ asset($logoPath) }}" alt="JCES Elementary School Logo" class="logo-image" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="logo-inner" style="display: none;">
                    @else
                        <div class="logo-inner">
                    @endif
                            <div class="wheat-decoration wheat-left">🌾</div>
                            <div class="wheat-decoration wheat-right">🌾</div>
                            
                            <div class="school-name-top">R. GRINO</div>
                            <div class="school-name-main">ELEMENTARY SCHOOL</div>
                            
                            <div class="book-flame-icon"></div>
                            
                            <div class="year-badge">1975</div>
                            
                            <div class="location-text">BINANGONAN RELOCATION DAVAO</div>
                        </div>
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-container">
                    {{-- @include('components.flash-messages') --}}
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
