<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Setup - POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .setup-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
        }
        .setup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .setup-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        .setup-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .setup-body {
            padding: 40px;
        }
        .setup-step {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        .setup-step.active {
            display: block;
        }
        .progress-bar {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
            transition: width 0.3s ease;
        }
        .status-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #e9ecef;
        }
        .status-item.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .status-item.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .status-item.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .status-icon {
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 500;
            border-radius: 8px;
            transition: transform 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e9ecef;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .footer-branding {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .footer-branding a {
            color: #667eea;
            text-decoration: none;
        }
        .footer-branding a:hover {
            color: #764ba2;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <h1><i class="fas fa-cogs"></i> Auto Setup</h1>
                <p>Automatic configuration for your POS system</p>
            </div>
            
            <div class="setup-body">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressBar" style="width: 0%"></div>
                </div>

                <!-- Step 1: System Check -->
                <div class="setup-step active" id="step1">
                    <h3><i class="fas fa-check-circle"></i> System Requirements Check</h3>
                    <p class="text-muted mb-4">Verifying your server meets all requirements...</p>
                    
                    <div class="status-item {{ $status['php_extensions']['pdo'] && $status['php_extensions']['pdo_mysql'] ? 'success' : 'error' }}">
                        <i class="fas fa-{{ $status['php_extensions']['pdo'] && $status['php_extensions']['pdo_mysql'] ? 'check' : 'times' }} status-icon"></i>
                        <div>
                            <strong>PHP Version:</strong> {{ $status['php_version'] }}
                            @if($status['php_extensions']['pdo'] && $status['php_extensions']['pdo_mysql'])
                                <span class="text-success">✓</span>
                            @else
                                <span class="text-danger">✗</span>
                            @endif
                        </div>
                    </div>

                    <div class="status-item {{ $status['env_file_exists'] ? 'success' : 'warning' }}">
                        <i class="fas fa-{{ $status['env_file_exists'] ? 'check' : 'exclamation-triangle' }} status-icon"></i>
                        <div>
                            <strong>Environment File:</strong> 
                            {{ $status['env_file_exists'] ? 'Found' : 'Will be created automatically' }}
                        </div>
                    </div>

                    <div class="status-item {{ $status['storage_linked'] ? 'success' : 'warning' }}">
                        <i class="fas fa-{{ $status['storage_linked'] ? 'check' : 'exclamation-triangle' }} status-icon"></i>
                        <div>
                            <strong>Storage Link:</strong> 
                            {{ $status['storage_linked'] ? 'Connected' : 'Will be created automatically' }}
                        </div>
                    </div>

                    @foreach($status['writable_directories'] as $dir => $writable)
                    <div class="status-item {{ $writable ? 'success' : 'error' }}">
                        <i class="fas fa-{{ $writable ? 'check' : 'times' }} status-icon"></i>
                        <div>
                            <strong>Directory {{ $dir }}:</strong> 
                            {{ $writable ? 'Writable' : 'Not writable' }}
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4">
                        <button class="btn btn-primary" onclick="nextStep()">
                            <i class="fas fa-arrow-right"></i> Continue to Database Setup
                        </button>
                    </div>
                </div>

                <!-- Step 2: Database Configuration -->
                <div class="setup-step" id="step2">
                    <h3><i class="fas fa-database"></i> Database Configuration</h3>
                    <p class="text-muted mb-4">Configure your database connection settings</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Default XAMPP Settings:</strong> The form is pre-filled with standard XAMPP configuration.
                        Just create a database named 'pos_system' in phpMyAdmin and click Test Connection.
                    </div>

                    <form id="databaseForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Database Host</label>
                                    <input type="text" class="form-control" name="db_host" value="127.0.0.1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Database Port</label>
                                    <input type="number" class="form-control" name="db_port" value="3306" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Database Name</label>
                                    <input type="text" class="form-control" name="db_database" value="pos_system" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Database Username</label>
                                    <input type="text" class="form-control" name="db_username" value="root" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Password</label>
                            <input type="password" class="form-control" name="db_password" placeholder="Leave empty for XAMPP default">
                        </div>
                    </form>

                    <div id="dbTestResult" class="mt-3"></div>

                    <div class="mt-4">
                        <button class="btn btn-outline-primary me-2" onclick="previousStep()">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button class="btn btn-primary me-2" onclick="testDatabase()">
                            <i class="fas fa-check"></i> Test Connection
                        </button>
                        <button class="btn btn-outline-secondary" onclick="useSQLite()">
                            <i class="fas fa-database"></i> Use SQLite Instead
                        </button>
                    </div>
                </div>

                <!-- Step 3: Database Initialization -->
                <div class="setup-step" id="step3">
                    <h3><i class="fas fa-server"></i> Database Initialization</h3>
                    <p class="text-muted mb-4">Setting up database tables and initial data...</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This process will create all necessary tables and install default settings.
                    </div>

                    <div id="initProgress" class="mt-3"></div>

                    <div class="mt-4">
                        <button class="btn btn-outline-primary me-2" onclick="previousStep()">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button class="btn btn-primary" onclick="initializeDatabase()">
                            <i class="fas fa-rocket"></i> Initialize Database
                        </button>
                    </div>
                </div>

                <!-- Step 4: Admin Account Setup -->
                <div class="setup-step" id="step4">
                    <h3><i class="fas fa-user-shield"></i> Admin Account Setup</h3>
                    <p class="text-muted mb-4">Create your administrator account and shop details</p>
                    
                    <form id="adminForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Admin Name</label>
                                    <input type="text" class="form-control" name="admin_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Admin Email</label>
                                    <input type="email" class="form-control" name="admin_email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="admin_password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="admin_password_confirmation" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Shop Name</label>
                            <input type="text" class="form-control" name="shop_name" required>
                        </div>
                    </form>

                    <div id="adminResult" class="mt-3"></div>

                    <div class="mt-4">
                        <button class="btn btn-outline-primary me-2" onclick="previousStep()">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button class="btn btn-primary" onclick="completeSetup()">
                            <i class="fas fa-check"></i> Complete Setup
                        </button>
                    </div>
                </div>

                <!-- Step 5: Setup Complete -->
                <div class="setup-step" id="step5">
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h3>Setup Complete!</h3>
                        <p class="text-muted mb-4">Your POS system is now ready to use.</p>
                        
                        <div class="alert alert-success">
                            <h5><i class="fas fa-rocket"></i> What's Next?</h5>
                            <ul class="text-start">
                                <li>Login with your admin credentials</li>
                                <li>Configure your shop settings</li>
                                <li>Add products and categories</li>
                                <li>Create staff accounts</li>
                                <li>Start making sales!</li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <a href="{{ url('/login') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Go to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-branding">
                <strong>Made by Conzec Technologies</strong><br>
                <a href="https://wa.me/923325223746" target="_blank">
                    <i class="fab fa-whatsapp"></i> Contact WhatsApp +923325223746
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 5;

        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }

        function showStep(step) {
            document.querySelectorAll('.setup-step').forEach(el => el.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
            currentStep = step;
            updateProgress();
        }

        function nextStep() {
            if (currentStep < totalSteps) {
                showStep(currentStep + 1);
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        }

        function testDatabase() {
            const form = document.getElementById('databaseForm');
            const formData = new FormData(form);
            const resultDiv = document.getElementById('dbTestResult');
            
            // Show loading
            resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Testing database connection...</div>';
            
            fetch('/auto-setup/configure-database', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                    setTimeout(() => nextStep(), 1500);
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Connection failed: ' + error.message + '</div>';
            });
        }

        function useSQLite() {
            const resultDiv = document.getElementById('dbTestResult');
            
            // Show loading
            resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Setting up SQLite database...</div>';
            
            fetch('/auto-setup/fallback-sqlite', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                    setTimeout(() => nextStep(), 1500);
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> SQLite setup failed: ' + error.message + '</div>';
            });
        }

        function initializeDatabase() {
            const resultDiv = document.getElementById('initProgress');
            
            // Show loading
            resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Initializing database...</div>';
            
            fetch('/auto-setup/initialize-database', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                    setTimeout(() => nextStep(), 1500);
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Initialization failed: ' + error.message + '</div>';
            });
        }

        function completeSetup() {
            const form = document.getElementById('adminForm');
            const formData = new FormData(form);
            const resultDiv = document.getElementById('adminResult');
            
            // Show loading
            resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Completing setup...</div>';
            
            fetch('/auto-setup/complete-setup', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                    setTimeout(() => showStep(5), 1500);
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</div>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Setup failed: ' + error.message + '</div>';
            });
        }

        // Initialize progress bar
        updateProgress();
    </script>
</body>
</html> 