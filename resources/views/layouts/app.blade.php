<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/img/choosechowlogo.png') }}">

    <title>@yield('title', 'ChooseChow - Delicious Homemade Meals')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --primary-color: #DC143C;
            --secondary-color: #F75270;
            --accent-color: #F7CAC9;
            --background-color: #FDEBD0;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --dark-color: #343a40;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }

        /* --- Auth Left Side --- */
        .auth-left {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.1'/%3E%3C/svg%3E");
            opacity: 0.4;
        }

        .auth-left > * { position: relative; z-index: 1; }

        .brand-logo { margin-bottom: 1rem; }
        .brand-name { font-size: 2.5rem; font-weight: bold; margin-bottom: 1rem; }
        .brand-tagline { font-size: 1.1rem; opacity: 0.9; margin-bottom: 2rem; }

        .auth-features { list-style: none; padding: 0; margin: 0; text-align: left; }
        .auth-features li { display: flex; align-items: center; margin-bottom: 1rem; font-size: 1rem; }
        .auth-features i { margin-right: 12px; width: 25px; text-align: center; font-size: 1.2rem; }

        /* --- Auth Right Side --- */
        .auth-right { padding: 60px 40px; }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-title { color: var(--dark-color); font-size: 2rem; font-weight: bold; margin-bottom: 0.5rem; }
        .auth-subtitle { color: #6c757d; font-size: 1rem; }

        /* Form Elements */
        .form-floating { margin-bottom: 1rem; position: relative; }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 20, 60, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }
        .divider::before {
            content: ''; position: absolute; top: 50%; left: 0; right: 0;
            height: 1px; background: #dee2e6;
        }
        .divider span {
            background: white; padding: 0 1rem; color: #6c757d; font-size: 0.9rem; position: relative;
        }

        .auth-link { color: var(--primary-color); text-decoration: none; font-weight: 600; }
        .auth-link:hover { color: var(--secondary-color); text-decoration: underline; }

        .alert { border-radius: 12px; border: none; margin-bottom: 1rem; }

        /* --- USER TYPE SELECTOR (Chef vs Customer) --- */
        .user-type-selector { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
        
        .user-type-option {
            flex: 1;
            padding: 1.5rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
        }
        
        .user-type-option:hover {
            border-color: var(--accent-color);
            background: #fff5f5;
        }

        /* Active State - This makes the selection VISIBLE */
        .user-type-option.active {
            border-color: var(--primary-color);
            background: #fff0f0;
            color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.15);
        }

        .user-type-option i { font-size: 2rem; margin-bottom: 0.5rem; display: block; }
        .user-type-option .title { font-weight: bold; font-size: 1.1rem; display: block; margin-bottom: 4px;}
        .user-type-option .description { font-size: 0.85rem; color: #6c757d; }

        @media (max-width: 768px) {
            .auth-card { flex-direction: column; }
            .auth-left, .auth-right { padding: 30px 20px; width: 100%; }
            .brand-name { font-size: 2rem; }
        }

        @yield('styles')
    </style>
</head>

<body>
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global Helpers
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    if (typeof bootstrap !== 'undefined') {
                        const alertInstance = bootstrap.Alert.getOrCreateInstance(alert);
                        alertInstance.close();
                    } else {
                        alert.style.display = 'none';
                    }
                });
            }, 5000);
        });
    </script>

    @yield('scripts')
</body>
</html>