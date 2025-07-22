# Complete White-Label POS System Deployment Guide

## 🚀 Professional POS System by Conzec Technologies

This guide will help you deploy your complete white-label Point of Sale system on shared hosting (like Hostinger).

---

## 📋 Pre-Deployment Checklist

### System Requirements
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Web server with URL rewriting support
- Minimum 256MB RAM
- 500MB disk space

### Features Included
✅ **Complete Setup Wizard** - First-time business configuration  
✅ **User Authentication & Roles** - Admin, Manager, Cashier roles  
✅ **Inventory Management** - Products, categories, stock tracking  
✅ **Customer Management** - Customer database with purchase history  
✅ **Supplier Management** - Supplier tracking and purchase orders  
✅ **Point of Sale** - Modern POS interface with barcode support  
✅ **Sales Management** - Complete sales tracking and receipts  
✅ **Purchase Management** - Inventory restocking system  
✅ **Reports** - Sales, stock, customer, expense reports  
✅ **Expense Tracking** - Business expense management  
✅ **Multi-Store Support** - Manage multiple store locations  
✅ **Barcode Generator** - Product barcode generation and printing  
✅ **White-Label Branding** - "Made by Conzec Technologies. Contact WhatsApp +923325223746"  
✅ **Professional UI** - Responsive design with modern interface  

---

## 🛠 Step 1: File Preparation

### 1.1 Create Environment File
Create a file named `.env` in your project root with the following content:

```env
APP_NAME="Your Shop Name"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@domain.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

---

## 🗄 Step 2: Database Setup

### 2.1 Create Database
1. Login to your hosting control panel (cPanel/hPanel)
2. Go to "MySQL Databases"
3. Create a new database (e.g., `your_username_pos`)
4. Create a database user with full privileges
5. Note down the database details for the `.env` file

### 2.2 Database Configuration
Update your `.env` file with the correct database credentials:
```env
DB_DATABASE=your_username_pos
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

---

## 📁 Step 3: File Upload

### 3.1 Upload Project Files
1. Compress your entire project into a ZIP file
2. Upload to your hosting account
3. Extract files to your domain's public folder (usually `public_html`)

### 3.2 Directory Structure
Your hosting directory should look like this:
```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── composer.json
└── other files...
```

### 3.3 Move Public Files
**IMPORTANT**: Move contents of the `public` folder to your domain root:
1. Move all files from `public/` to `public_html/`
2. Update `index.php` to point to the correct bootstrap file:

```php
// In public_html/index.php, change:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// To (adjust path as needed):
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

---

## 🔧 Step 4: Configuration

### 4.1 Set File Permissions
Set the following permissions via File Manager or SSH:
```bash
chmod 755 bootstrap/cache/
chmod 755 storage/
chmod -R 755 storage/
chmod 755 public/
```

### 4.2 Generate Application Key
Run via SSH or create a temporary PHP file:
```php
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$key = 'base64:'.base64_encode(random_bytes(32));
echo "APP_KEY=".$key;
// Copy this key to your .env file
```

### 4.3 Create Storage Link
Create a symlink for storage (if supported):
```bash
ln -s ../storage/app/public public/storage
```

Or via PHP file:
```php
<?php
if (!file_exists(public_path('storage'))) {
    symlink(storage_path('app/public'), public_path('storage'));
}
echo "Storage link created";
?>
```

---

## 🗃 Step 5: Database Migration

### 5.1 Run Migrations
Create a setup file `setup.php` in your domain root:

```php
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

try {
    // Test database connection
    $pdo = new PDO("mysql:host=".env('DB_HOST').";dbname=".env('DB_DATABASE'), env('DB_USERNAME'), env('DB_PASSWORD'));
    echo "Database connected successfully!\n";
    
    // Run migrations (you may need to create tables manually)
    echo "Please run the following SQL commands in your database:\n\n";
    
    // Output the SQL schema here or provide migration files
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
```

### 5.2 Database Schema
Execute the following SQL in your database:

```sql
-- Create users table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'manager', 'cashier') DEFAULT 'cashier',
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    store_id BIGINT UNSIGNED,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create stores table
CREATE TABLE stores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255),
    website VARCHAR(255),
    logo VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    store_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    sku VARCHAR(100) UNIQUE,
    barcode VARCHAR(100) UNIQUE,
    category_id BIGINT UNSIGNED,
    cost_price DECIMAL(10,2) DEFAULT 0,
    selling_price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    min_stock_level INT DEFAULT 0,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    store_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create customers table
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    store_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create suppliers table
CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    contact_person VARCHAR(255),
    store_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create sales table
CREATE TABLE sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(100) UNIQUE NOT NULL,
    customer_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    store_id BIGINT UNSIGNED,
    subtotal DECIMAL(10,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'bank') DEFAULT 'cash',
    payment_reference VARCHAR(255),
    amount_paid DECIMAL(10,2),
    status ENUM('completed', 'pending', 'cancelled') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create sale_items table
CREATE TABLE sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create settings table
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(255) NOT NULL,
    value TEXT,
    type ENUM('text', 'number', 'boolean', 'email', 'url') DEFAULT 'text',
    store_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_setting (key, store_id)
);

-- Add foreign key constraints
ALTER TABLE users ADD FOREIGN KEY (store_id) REFERENCES stores(id);
ALTER TABLE categories ADD FOREIGN KEY (store_id) REFERENCES stores(id);
ALTER TABLE products ADD FOREIGN KEY (category_id) REFERENCES categories(id);
ALTER TABLE products ADD FOREIGN KEY (store_id) REFERENCES stores(id);
ALTER TABLE customers ADD FOREIGN KEY (store_id) REFERENCES stores(id);
ALTER TABLE suppliers ADD FOREIGN KEY (store_id) REFERENCES stores(id);
ALTER TABLE sales ADD FOREIGN KEY (customer_id) REFERENCES customers(id);
ALTER TABLE sales ADD FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE sales ADD FOREIGN KEY (store_id) REFERENCES stores(id);
ALTER TABLE sale_items ADD FOREIGN KEY (sale_id) REFERENCES sales(id);
ALTER TABLE sale_items ADD FOREIGN KEY (product_id) REFERENCES products(id);
ALTER TABLE settings ADD FOREIGN KEY (store_id) REFERENCES stores(id);
```

---

## 🎯 Step 6: First Access

### 6.1 Access Your Site
1. Visit your domain: `https://yourdomain.com`
2. You should see the setup wizard automatically
3. If not, visit: `https://yourdomain.com/setup`

### 6.2 Initial Setup
The setup wizard will guide you through:
1. **Business Information** - Shop name, address, contact details
2. **Owner Information** - Owner details and admin account creation
3. **Business Settings** - Currency, tax rate configuration
4. **Logo Upload** - Shop logo for branding

### 6.3 Complete Setup
After completing the wizard:
- Admin account will be created
- Business settings will be saved
- You'll be redirected to the dashboard

---

## 🔒 Step 7: Security Hardening

### 7.1 Environment Security
- Set `APP_DEBUG=false` in production
- Use strong database passwords
- Secure your `.env` file (chmod 600)

### 7.2 File Security
```apache
# Add to .htaccess in root directory
<Files .env>
    Order allow,deny
    Deny from all
</Files>

<Files composer.json>
    Order allow,deny
    Deny from all
</Files>

<Files composer.lock>
    Order allow,deny
    Deny from all
</Files>
```

### 7.3 Regular Updates
- Keep Laravel updated
- Monitor security advisories
- Regular database backups

---

## 📊 Step 8: Testing

### 8.1 Basic Functionality Test
1. **Login Test** - Verify admin login works
2. **Product Management** - Add/edit products
3. **Customer Management** - Add customers
4. **POS Test** - Make a test sale
5. **Receipt Generation** - Verify receipt printing
6. **Reports** - Check report generation

### 8.2 Performance Test
- Test with multiple products
- Verify search functionality
- Check mobile responsiveness

---

## 🚀 Step 9: Going Live

### 9.1 Final Checks
- [ ] SSL certificate installed
- [ ] Database backups configured
- [ ] Email notifications working
- [ ] All features tested
- [ ] White-label branding verified

### 9.2 User Training
Provide training on:
- Basic POS operations
- Product management
- Customer management
- Report generation
- System administration

---

## 📞 Support & Maintenance

### Technical Support
- **Developer**: Conzec Technologies
- **Documentation**: Complete user manual included
- **Updates**: Regular feature updates available

### Maintenance Tasks
- **Daily**: Database backup
- **Weekly**: System health check
- **Monthly**: Security updates

---

## 🎉 Congratulations!

Your white-label POS system is now live and ready for business!

**System Features Deployed:**
✅ Complete setup wizard  
✅ User management with roles  
✅ Inventory management  
✅ Point of sale interface  
✅ Customer & supplier management  
✅ Sales & purchase tracking  
✅ Comprehensive reporting  
✅ Barcode support  
✅ Multi-store capability  
✅ Professional white-label branding  

**Made by Conzec Technologies. Contact WhatsApp +923325223746** - Professional POS Solutions for Your Business

---

## 📁 File Structure Reference

```
pos-system/
├── app/
│   ├── Console/Commands/
│   ├── Helpers/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   └── factories/
├── public/
├── resources/
│   └── views/
│       ├── auth/
│       ├── layouts/
│       ├── sales/
│       ├── setup/
│       └── reports/
├── routes/
├── storage/
├── .env (create this)
├── artisan
└── composer.json
```

This completes your professional white-label POS system deployment! 