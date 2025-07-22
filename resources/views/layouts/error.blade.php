<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') - POS System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
        }
        
        .error-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
            margin: 2rem;
        }
        
        .error-icon {
            margin-bottom: 2rem;
        }
        
        .error-code {
            font-size: 5rem;
            font-weight: 900;
            color: #e74c3c;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            line-height: 1;
        }
        
        .error-message h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .error-message .lead {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }
        
        .error-actions {
            margin: 2rem 0;
        }
        
        .error-actions .btn {
            margin: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .error-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .error-help {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
        }
        
        .error-help h5 {
            color: #2c3e50;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .error-help ul li {
            margin-bottom: 0.5rem;
            color: #5a6c7d;
        }
        
        .error-shortcuts {
            margin-top: 2rem;
            text-align: center;
        }
        
        .error-shortcuts h5 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .shortcut-link {
            display: block;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            text-decoration: none;
            color: #5a6c7d;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .shortcut-link:hover {
            background: #e9ecef;
            color: #2c3e50;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .shortcut-link i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .shortcut-link span {
            display: block;
            font-weight: 500;
        }
        
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #2c3e50 !important;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .error-container {
                padding: 2rem 1rem;
                margin: 1rem;
            }
            
            .error-code {
                font-size: 4rem;
            }
            
            .error-actions .btn {
                display: block;
                width: 100%;
                margin: 0.5rem 0;
            }
            
            .shortcut-link {
                padding: 0.75rem;
            }
        }
        
        /* Dark mode styles */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            }
            
            .error-container {
                background: #2c3e50;
                color: white;
            }
            
            .error-message h2 {
                color: #ecf0f1;
            }
            
            .error-help {
                background: #34495e;
                color: #ecf0f1;
            }
            
            .error-help h5 {
                color: #ecf0f1;
            }
            
            .error-shortcuts h5 {
                color: #ecf0f1;
            }
            
            .shortcut-link {
                background: #34495e;
                color: #ecf0f1;
            }
            
            .shortcut-link:hover {
                background: #3a5169;
                color: #ecf0f1;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    @auth
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-cash-register"></i> POS System
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text">
                    {{ auth()->user()->name }} ({{ auth()->user()->getRoleNames()->first() }})
                </span>
                <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-sm ms-2"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </nav>
    @endauth
    
    <div class="container-fluid">
        @yield('content')
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Auto-refresh functionality
        function startAutoRefresh() {
            setInterval(function() {
                if (navigator.onLine) {
                    const refreshBtn = document.querySelector('.btn-refresh');
                    if (refreshBtn) {
                        refreshBtn.classList.add('text-success');
                        refreshBtn.innerHTML = '<i class="fas fa-check"></i> Connection Restored';
                    }
                }
            }, 30000);
        }
        
        // Check connection status
        window.addEventListener('online', function() {
            const errorContainer = document.querySelector('.error-container');
            if (errorContainer) {
                errorContainer.insertAdjacentHTML('afterbegin', 
                    '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-wifi"></i> Connection restored! You can now continue using the system.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>'
                );
            }
        });
        
        window.addEventListener('offline', function() {
            const errorContainer = document.querySelector('.error-container');
            if (errorContainer) {
                errorContainer.insertAdjacentHTML('afterbegin', 
                    '<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-wifi-slash"></i> Connection lost! Some features may not work properly.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>'
                );
            }
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();
        });
    </script>
    
    @yield('scripts')
</body>
</html> 