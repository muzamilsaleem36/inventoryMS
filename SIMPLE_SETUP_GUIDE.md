# POS System - Simple Setup Guide

## Quick Setup (5 Minutes)

### 1. Download & Extract
- Download the POS system files
- Extract to your web directory:
  - **XAMPP**: `C:\xampp\htdocs\pos\`
  - **WAMP**: `C:\wamp64\www\pos\`
  - **Shared Hosting**: Upload to your domain folder

### 2. Database Setup
- Create a MySQL database named `pos_system`
- Create a database user with full permissions

### 3. Environment Configuration
- Copy `env-simple.txt` to `.env`
- Edit `.env` file:
  ```
  DB_DATABASE=pos_system
  DB_USERNAME=your_username
  DB_PASSWORD=your_password
  ```

### 4. Installation
- Visit your website:
  - **Local**: `http://localhost/pos`
  - **Hosting**: `http://yourdomain.com`
- Go to setup page: `http://localhost/pos/setup`
- Fill in business details and create admin account

### 5. Done!
Your POS system is ready to use!

## Folder Structure
```
pos/
├── .env (your configuration)
├── .htaccess (routing rules)
├── index.php (main entry point)
├── public/ (web files)
└── app/ (application files)
```

## URLs
- **Main App**: `http://localhost/pos/`
- **Setup**: `http://localhost/pos/setup`
- **Login**: `http://localhost/pos/`
- **Dashboard**: `http://localhost/pos/dashboard`

## Default Admin Account
After setup, you can login with the admin account you created during installation.

## Hosting Requirements
- PHP 8.1+
- MySQL 5.7+
- mod_rewrite enabled
- 512MB RAM minimum

## Support
If you need help, check that:
1. Database connection is working
2. `.env` file exists and is configured
3. Web server has mod_rewrite enabled
4. All files are uploaded correctly

That's it! Your POS system should work now. 