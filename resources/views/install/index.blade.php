<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System Installation</title>
    <style>
        /* WordPress-style installation CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background: #f1f1f1;
            color: #444;
            line-height: 1.4;
        }

        .wp-core-ui {
            background: #f1f1f1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .language-chooser {
            max-width: 600px;
            width: 100%;
            background: #fff;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.13);
            padding: 0;
            overflow: hidden;
        }

        .language-chooser h1 {
            background: #0073aa;
            color: #fff;
            padding: 24px;
            margin: 0;
            font-size: 32px;
            font-weight: 400;
            text-align: center;
            position: relative;
        }

        .language-chooser h1::before {
            content: "⚡";
            position: absolute;
            left: 24px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 28px;
        }

        .setup-form {
            padding: 24px;
        }

        .setup-step {
            display: none;
        }

        .setup-step.active {
            display: block;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .form-table th,
        .form-table td {
            padding: 12px 0;
            vertical-align: middle;
            border: none;
        }

        .form-table th {
            width: 140px;
            text-align: left;
            font-weight: 600;
            color: #23282d;
        }

        .form-table input[type="text"],
        .form-table input[type="email"],
        .form-table input[type="password"],
        .form-table input[type="number"],
        .form-table select,
        .form-table textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            line-height: 1.4;
            color: #32373c;
            background: #fff;
        }

        .form-table input:focus,
        .form-table select:focus,
        .form-table textarea:focus {
            border-color: #0073aa;
            outline: none;
            box-shadow: 0 0 0 1px #0073aa;
        }

        .button {
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
            line-height: 2.15384615;
            min-height: 30px;
            margin: 0;
            padding: 0 10px;
            cursor: pointer;
            border: 1px solid #0073aa;
            border-radius: 3px;
            background: #0073aa;
            color: #fff;
            white-space: nowrap;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .button:hover,
        .button:focus {
            background: #005a87;
            border-color: #005a87;
            color: #fff;
        }

        .button:active {
            transform: translateY(1px);
        }

        .button-large {
            height: 40px;
            line-height: 2.30769231;
            padding: 0 20px;
            font-size: 14px;
        }

        .button-secondary {
            background: #fff;
            border-color: #ccc;
            color: #0073aa;
        }

        .button-secondary:hover,
        .button-secondary:focus {
            background: #f7f7f7;
            border-color: #999;
            color: #0073aa;
        }

        .notice {
            background: #fff;
            border-left: 4px solid #0073aa;
            padding: 12px;
            margin: 20px 0;
            border-radius: 0 3px 3px 0;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }

        .notice-success {
            border-left-color: #46b450;
        }

        .notice-error {
            border-left-color: #dc3232;
        }

        .notice-warning {
            border-left-color: #ffb900;
        }

        .notice-info {
            border-left-color: #0073aa;
        }

        .notice p {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .wp-version {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e5e5;
            color: #666;
            font-size: 12px;
        }

        .setup-progress {
            margin-bottom: 24px;
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: #e5e5e5;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #0073aa;
            transition: width 0.3s ease;
        }

        .progress-text {
            text-align: center;
            margin-top: 12px;
            font-size: 14px;
            color: #666;
        }

        .step-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e5e5;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.active {
            display: block;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #0073aa;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .checkbox-group {
            margin: 16px 0;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: normal;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 8px;
        }

        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
            line-height: 1.4;
        }

        .currency-select {
            display: grid;
            grid-template-columns: 1fr 120px;
            gap: 12px;
            align-items: center;
        }

        .branding-footer {
            background: #f8f9fa;
            border-top: 1px solid #e5e5e5;
            padding: 16px 24px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .branding-footer strong {
            color: #0073aa;
        }

        .branding-footer a {
            color: #0073aa;
            text-decoration: none;
        }

        .branding-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .language-chooser {
                margin: 0 20px;
            }
            
            .form-table th,
            .form-table td {
                display: block;
                width: 100%;
                padding: 8px 0;
            }
            
            .form-table th {
                margin-bottom: 4px;
            }
            
            .currency-select {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="wp-core-ui">
    <div class="language-chooser">
        <h1>POS System Installation</h1>
        
        <div class="setup-form">
            <div class="setup-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 25%;"></div>
                </div>
                <div class="progress-text" id="progressText">Step 1 of 4: Database Configuration</div>
            </div>

            <div id="messages"></div>

            <!-- Step 1: Database Configuration -->
            <div class="setup-step active" id="step1">
                <h2>Database Configuration</h2>
                <p>Below you should enter your database connection details. If you're not sure about these, contact your host.</p>
                
                <form id="databaseForm">
                    <table class="form-table">
                        <tr>
                            <th><label for="db_name">Database Name</label></th>
                            <td>
                                <input type="text" id="db_name" name="db_name" value="pos_system" required>
                                <div class="help-text">The name of the database you want to use with this POS system.</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="db_user">Username</label></th>
                            <td>
                                <input type="text" id="db_user" name="db_user" value="root" required>
                                <div class="help-text">Your database username.</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="db_password">Password</label></th>
                            <td>
                                <input type="password" id="db_password" name="db_password" placeholder="Leave blank if no password">
                                <div class="help-text">Your database password.</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="db_host">Database Host</label></th>
                            <td>
                                <input type="text" id="db_host" name="db_host" value="127.0.0.1" required>
                                <div class="help-text">Usually localhost or 127.0.0.1</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="db_port">Database Port</label></th>
                            <td>
                                <input type="number" id="db_port" name="db_port" value="3306" min="1" max="65535" required>
                                <div class="help-text">Usually 3306 for MySQL</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="table_prefix">Table Prefix</label></th>
                            <td>
                                <input type="text" id="table_prefix" name="table_prefix" value="" placeholder="pos_" maxlength="10">
                                <div class="help-text">If you want to run multiple POS systems in a single database, change this.</div>
                            </td>
                        </tr>
                    </table>
                </form>
                
                <div class="step-nav">
                    <div></div>
                    <button type="button" class="button button-large" onclick="testDatabase()">Test Database Connection</button>
                </div>
            </div>

            <!-- Step 2: Business Information -->
            <div class="setup-step" id="step2">
                <h2>Business Information</h2>
                <p>Tell us about your business. Don't worry, you can change these settings later.</p>
                
                <form id="businessForm">
                    <table class="form-table">
                        <tr>
                            <th><label for="business_name">Business Name</label></th>
                            <td>
                                <input type="text" id="business_name" name="business_name" required>
                                <div class="help-text">The name of your business or store.</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="business_email">Email Address</label></th>
                            <td>
                                <input type="email" id="business_email" name="business_email" required>
                                <div class="help-text">Your business email address.</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="business_phone">Phone Number</label></th>
                            <td>
                                <input type="text" id="business_phone" name="business_phone">
                                <div class="help-text">Your business phone number (optional).</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="business_address">Address</label></th>
                            <td>
                                <textarea id="business_address" name="business_address" rows="3"></textarea>
                                <div class="help-text">Your business address (optional).</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="currency">Currency</label></th>
                            <td>
                                <div class="currency-select">
                                    <select id="currency" name="currency" required>
                                        <option value="USD">US Dollar</option>
                                        <option value="EUR">Euro</option>
                                        <option value="GBP">British Pound</option>
                                        <option value="INR">Indian Rupee</option>
                                        <option value="PKR">Pakistani Rupee</option>
                                        <option value="CAD">Canadian Dollar</option>
                                        <option value="AUD">Australian Dollar</option>
                                        <option value="JPY">Japanese Yen</option>
                                        <option value="CNY">Chinese Yuan</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                    <input type="text" id="currency_symbol" name="currency_symbol" value="$" placeholder="$" maxlength="5" required>
                                </div>
                                <div class="help-text">Choose your currency and symbol.</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="tax_rate">Tax Rate (%)</label></th>
                            <td>
                                <input type="number" id="tax_rate" name="tax_rate" value="0" min="0" max="100" step="0.01" required>
                                <div class="help-text">Default tax rate for your products (can be changed later).</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="timezone">Timezone</label></th>
                            <td>
                                <select id="timezone" name="timezone" required>
                                    <option value="UTC">UTC</option>
                                    <option value="America/New_York">Eastern Time</option>
                                    <option value="America/Chicago">Central Time</option>
                                    <option value="America/Denver">Mountain Time</option>
                                    <option value="America/Los_Angeles">Pacific Time</option>
                                    <option value="Europe/London">London</option>
                                    <option value="Europe/Paris">Paris</option>
                                    <option value="Asia/Tokyo">Tokyo</option>
                                    <option value="Asia/Shanghai">Shanghai</option>
                                    <option value="Asia/Dubai">Dubai</option>
                                    <option value="Asia/Karachi">Karachi</option>
                                    <option value="Asia/Kolkata">Mumbai</option>
                                </select>
                                <div class="help-text">Your local timezone.</div>
                            </td>
                        </tr>
                    </table>
                </form>
                
                <div class="step-nav">
                    <button type="button" class="button button-secondary" onclick="previousStep()">← Back</button>
                    <button type="button" class="button button-large" onclick="saveBusiness()">Continue →</button>
                </div>
            </div>

            <!-- Step 3: Admin Account -->
            <div class="setup-step" id="step3">
                <h2>Admin Account</h2>
                <p>Create your administrator account. This will be the main account that can manage your POS system.</p>
                
                <form id="adminForm">
                    <table class="form-table">
                        <tr>
                            <th><label for="admin_username">Username</label></th>
                            <td>
                                <input type="text" id="admin_username" name="admin_username" required>
                                <div class="help-text">Your admin username for logging in.</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="admin_email">Email</label></th>
                            <td>
                                <input type="email" id="admin_email" name="admin_email" required>
                                <div class="help-text">Your admin email address.</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="admin_password">Password</label></th>
                            <td>
                                <input type="password" id="admin_password" name="admin_password" required>
                                <div class="help-text">Choose a strong password (at least 8 characters).</div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="admin_password_confirmation">Confirm Password</label></th>
                            <td>
                                <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" required>
                                <div class="help-text">Re-enter your password to confirm.</div>
                            </td>
                        </tr>
                    </table>
                </form>
                
                <div class="step-nav">
                    <button type="button" class="button button-secondary" onclick="previousStep()">← Back</button>
                    <button type="button" class="button button-large" onclick="saveAdmin()">Continue →</button>
                </div>
            </div>

            <!-- Step 4: Installation -->
            <div class="setup-step" id="step4">
                <h2>Ready to Install</h2>
                <p>We're ready to install your POS system! Click the button below to start the installation process.</p>
                
                <div class="notice notice-info">
                    <p><strong>What happens next:</strong></p>
                    <ul style="margin: 12px 0 0 20px;">
                        <li>Database tables will be created</li>
                        <li>Your business settings will be configured</li>
                        <li>Your admin account will be created</li>
                        <li>Default data will be installed</li>
                    </ul>
                </div>
                
                <div class="loading" id="installLoading">
                    <div class="spinner"></div>
                    <span>Installing POS system...</span>
                </div>
                
                <div class="step-nav">
                    <button type="button" class="button button-secondary" onclick="previousStep()">← Back</button>
                    <button type="button" class="button button-large" onclick="runInstallation()" id="installButton">Install POS System</button>
                </div>
            </div>
        </div>
        
        <div class="branding-footer">
            <strong>Made by Conzec Technologies</strong><br>
            <a href="https://wa.me/923325223746" target="_blank">Contact WhatsApp +923325223746</a>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 4;

        // Update currency symbol when currency changes
        document.getElementById('currency').addEventListener('change', function() {
            const symbols = {
                'USD': '$', 'EUR': '€', 'GBP': '£', 'INR': '₹', 'PKR': '₨',
                'CAD': 'C$', 'AUD': 'A$', 'JPY': '¥', 'CNY': '¥', 'OTHER': ''
            };
            document.getElementById('currency_symbol').value = symbols[this.value] || '';
        });

        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
            
            const stepTexts = [
                'Step 1 of 4: Database Configuration',
                'Step 2 of 4: Business Information',
                'Step 3 of 4: Admin Account',
                'Step 4 of 4: Installation'
            ];
            
            document.getElementById('progressText').textContent = stepTexts[currentStep - 1];
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

        function showMessage(message, type = 'info', suggestions = null) {
            const messageDiv = document.getElementById('messages');
            let html = `<div class="notice notice-${type}"><p>${message}</p>`;
            
            if (suggestions && suggestions.length > 0) {
                html += '<div style="margin-top: 12px;"><strong>Suggestions:</strong><ul style="margin: 8px 0 0 20px;">';
                suggestions.forEach(suggestion => {
                    html += `<li>${suggestion}</li>`;
                });
                html += '</ul></div>';
            }
            
            html += '</div>';
            messageDiv.innerHTML = html;
            messageDiv.scrollIntoView({ behavior: 'smooth' });
        }

        function testDatabase() {
            const form = document.getElementById('databaseForm');
            const formData = new FormData(form);
            
            showMessage('Testing database connection...', 'info');
            
            fetch('/install/database', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => nextStep(), 1500);
                } else {
                    showMessage(data.message, 'error', data.suggestions);
                }
            })
            .catch(error => {
                showMessage('Connection failed: ' + error.message, 'error');
            });
        }

        function saveBusiness() {
            const form = document.getElementById('businessForm');
            const formData = new FormData(form);
            
            showMessage('Saving business information...', 'info');
            
            fetch('/install/business', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => nextStep(), 1500);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Save failed: ' + error.message, 'error');
            });
        }

        function saveAdmin() {
            const form = document.getElementById('adminForm');
            const formData = new FormData(form);
            
            showMessage('Saving admin account...', 'info');
            
            fetch('/install/admin', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => nextStep(), 1500);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Save failed: ' + error.message, 'error');
            });
        }

        function runInstallation() {
            document.getElementById('installLoading').classList.add('active');
            document.getElementById('installButton').style.display = 'none';
            
            showMessage('Installing POS system...', 'info');
            
            fetch('/install/run', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    showMessage(data.message, 'error');
                    document.getElementById('installLoading').classList.remove('active');
                    document.getElementById('installButton').style.display = 'inline-block';
                }
            })
            .catch(error => {
                showMessage('Installation failed: ' + error.message, 'error');
                document.getElementById('installLoading').classList.remove('active');
                document.getElementById('installButton').style.display = 'inline-block';
            });
        }

        // Initialize
        updateProgress();
    </script>
</body>
</html> 