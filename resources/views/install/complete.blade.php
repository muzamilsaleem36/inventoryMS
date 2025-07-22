<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Complete - POS System</title>
    <style>
        /* WordPress-style success page CSS */
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
            background: #46b450;
            color: #fff;
            padding: 24px;
            margin: 0;
            font-size: 32px;
            font-weight: 400;
            text-align: center;
            position: relative;
        }

        .language-chooser h1::before {
            content: "✓";
            position: absolute;
            left: 24px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 28px;
            font-weight: bold;
        }

        .setup-form {
            padding: 40px;
            text-align: center;
        }

        .success-icon {
            font-size: 64px;
            color: #46b450;
            margin-bottom: 20px;
        }

        .success-message {
            margin-bottom: 30px;
        }

        .success-message h2 {
            color: #23282d;
            margin-bottom: 16px;
            font-size: 24px;
            font-weight: 400;
        }

        .success-message p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .next-steps {
            background: #f8f9fa;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            padding: 24px;
            margin: 24px 0;
            text-align: left;
        }

        .next-steps h3 {
            color: #0073aa;
            margin-bottom: 16px;
            font-size: 18px;
            font-weight: 600;
        }

        .next-steps ul {
            margin: 0;
            padding-left: 20px;
        }

        .next-steps li {
            margin-bottom: 8px;
            color: #555;
        }

        .button {
            display: inline-block;
            text-decoration: none;
            font-size: 14px;
            line-height: 2.15384615;
            min-height: 30px;
            margin: 0;
            padding: 0 20px;
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

        .button-large {
            height: 40px;
            line-height: 2.30769231;
            padding: 0 30px;
            font-size: 16px;
            margin: 0 10px;
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

        .system-info {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            padding: 20px;
            margin: 24px 0;
            text-align: left;
        }

        .system-info h4 {
            color: #23282d;
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: 600;
        }

        .system-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .system-info th,
        .system-info td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .system-info th {
            background: #f8f9fa;
            color: #555;
            font-weight: 600;
            width: 40%;
        }

        .system-info td {
            color: #333;
        }

        .branding-footer {
            background: #f8f9fa;
            border-top: 1px solid #e5e5e5;
            padding: 20px;
            text-align: center;
            font-size: 14px;
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

        .action-buttons {
            margin-top: 30px;
        }

        @media (max-width: 600px) {
            .language-chooser {
                margin: 0 20px;
            }
            
            .setup-form {
                padding: 20px;
            }
            
            .button-large {
                display: block;
                margin: 10px 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="wp-core-ui">
    <div class="language-chooser">
        <h1>Installation Complete!</h1>
        
        <div class="setup-form">
            <div class="success-icon">🎉</div>
            
            <div class="success-message">
                <h2>Success!</h2>
                <p>Your POS system has been successfully installed and is ready to use.</p>
                <p>You can now start configuring your products, customers, and begin making sales.</p>
            </div>

            <div class="next-steps">
                <h3>🚀 What's Next?</h3>
                <ul>
                    <li><strong>Login</strong> to your admin dashboard</li>
                    <li><strong>Configure</strong> your business settings and preferences</li>
                    <li><strong>Add products</strong> to your inventory</li>
                    <li><strong>Create categories</strong> to organize your products</li>
                    <li><strong>Set up users</strong> for your staff members</li>
                    <li><strong>Start making sales</strong> with the POS interface</li>
                </ul>
            </div>

            <div class="system-info">
                <h4>📋 System Information</h4>
                <table>
                    <tr>
                        <th>Installation Date</th>
                        <td>{{ now()->format('M j, Y') }}</td>
                    </tr>
                    <tr>
                        <th>System Version</th>
                        <td>POS System v1.0.0</td>
                    </tr>
                    <tr>
                        <th>PHP Version</th>
                        <td>{{ phpversion() }}</td>
                    </tr>
                    <tr>
                        <th>Database</th>
                        <td>MySQL {{ DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown' }}</td>
                    </tr>
                    <tr>
                        <th>Installation URL</th>
                        <td>{{ url('/') }}</td>
                    </tr>
                </table>
            </div>

            <div class="action-buttons">
                <a href="{{ url('/login') }}" class="button button-large">
                    Login to Dashboard
                </a>
                <a href="{{ url('/') }}" class="button button-secondary button-large">
                    Visit Site
                </a>
            </div>
        </div>
        
        <div class="branding-footer">
            <strong>🏆 Professional POS System</strong><br>
            <strong>Made by Conzec Technologies</strong><br>
            <a href="https://wa.me/923325223746" target="_blank">
                📱 Contact WhatsApp +923325223746
            </a>
            <br><br>
            <em>Thank you for choosing our POS system for your business!</em>
        </div>
    </div>
</body>
</html> 