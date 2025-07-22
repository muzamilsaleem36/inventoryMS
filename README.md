# Professional POS System

A full-featured, production-ready Point of Sale (POS) web application built with Laravel and MySQL. Perfect for small to medium businesses, this system can be sold as a SaaS or deployed as a self-hosted solution.

![POS System Dashboard](https://via.placeholder.com/800x400?text=POS+System+Dashboard)

## 🚀 Features

### Core Features
- **User Management**: Role-based access control (Admin, Manager, Cashier)
- **Inventory Management**: Products, categories, stock tracking, low stock alerts
- **Customer Management**: Customer profiles, purchase history, credit management
- **Supplier Management**: Supplier profiles, purchase tracking
- **Sales Management**: Point of sale interface, receipts, payment methods
- **Purchase Management**: Stock restocking, supplier purchase orders
- **Reports & Analytics**: Daily/weekly/monthly sales reports, top products, stock reports
- **Settings Management**: Business configuration, tax rates, currency settings

### Premium Features
- **Multi-store Support**: Manage multiple store locations
- **Barcode Generation**: Generate and print barcode labels
- **User Activity Logs**: Track all user actions for security
- **Expense Tracking**: Track business expenses and costs
- **Advanced Reporting**: Detailed analytics and insights

## 🛠️ Technology Stack

- **Backend**: PHP 8.0+, Laravel 10
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5, jQuery, Font Awesome
- **Additional**: Spatie Permissions, DomPDF, Intervention Image

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx)
- PHP Extensions: PDO, cURL, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, GD

## 🔧 Installation

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/pos-system.git
   cd pos-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pos_system
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seed data**
   ```bash
   php artisan migrate --seed
   ```

6. **Create storage link**
   ```bash
   php artisan storage:link
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

8. **Access the application**
   Open http://localhost:8000 in your browser

### Default Login Credentials
- **Admin**: admin@pos.com / password
- **Manager**: manager@pos.com / password
- **Cashier**: cashier@pos.com / password

## 🌐 Shared Hosting Deployment (Hostinger)

### Step 1: Prepare Files
1. Upload all files to your hosting account
2. Move the `public` folder contents to your domain's public_html folder
3. Update the `index.php` file paths to point to your Laravel installation

### Step 2: Database Setup
1. Create a MySQL database in your hosting control panel
2. Import the database structure:
   ```bash
   php artisan migrate --seed
   ```

### Step 3: Configure Environment
1. Rename `.env.example` to `.env`
2. Update database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

### Step 4: Set Permissions
Set the following folder permissions:
- `/storage` - 755 (recursive)
- `/bootstrap/cache` - 755 (recursive)
- `/public/uploads` - 755 (recursive)

### Step 5: Generate Application Key
```bash
php artisan key:generate
```

### Step 6: Create Storage Link
```bash
php artisan storage:link
```

### Step 7: Clear Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📱 Usage Guide

### Getting Started
1. **Login** with admin credentials
2. **Configure Settings**: Set up your business details, tax rates, currency
3. **Add Categories**: Create product categories
4. **Add Products**: Create your inventory items
5. **Add Customers**: Set up customer profiles
6. **Start Selling**: Use the POS interface to process sales

### User Roles & Permissions

#### Admin
- Full system access
- User management
- Settings configuration
- All reports and analytics

#### Manager
- Inventory management
- Customer/supplier management
- Sales and purchase operations
- Basic reports

#### Cashier
- Sales operations
- Customer management
- Basic inventory viewing

### Daily Operations

#### Processing Sales
1. Navigate to Sales → New Sale
2. Search and add products to cart
3. Select customer (optional)
4. Apply discounts or taxes
5. Process payment
6. Print receipt

#### Managing Inventory
1. Add new products via Products → Add Product
2. Monitor stock levels on dashboard
3. Adjust stock quantities as needed
4. Set up low stock alerts

#### Generating Reports
1. Access Reports from sidebar
2. Select date range and filters
3. Export reports as PDF/Excel
4. Schedule automatic reports (premium)

## 🔒 Security Features

- **Role-based Access Control**: Secure user permissions
- **CSRF Protection**: Built-in Laravel CSRF protection
- **XSS Protection**: Input sanitization and output encoding
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **Activity Logging**: Track all user actions
- **Secure File Uploads**: Validated file uploads with size limits

## 🎨 Customization

### Branding
- Update logo in `public/images/logo.png`
- Modify colors in `resources/views/layouts/app.blade.php`
- Customize email templates in `resources/views/emails/`

### Adding Features
- Create new controllers in `app/Http/Controllers/`
- Add routes in `routes/web.php`
- Create views in `resources/views/`
- Add permissions in database seeder

## 📊 Business Model

This POS system is designed to be:
- **Sold as SaaS**: Monthly/yearly subscriptions
- **Self-hosted**: One-time license fee
- **Freemium**: Basic features free, premium features paid
- **White-label**: Rebrand and resell to clients

## 🔧 API Documentation

### Authentication
All API endpoints require authentication token:
```
Authorization: Bearer {token}
```

### Endpoints
- `GET /api/products/search` - Search products
- `GET /api/customers/search` - Search customers
- `GET /api/dashboard/stats` - Dashboard statistics
- `POST /api/sales` - Create new sale

## 🚨 Troubleshooting

### Common Issues

**Database Connection Error**
- Check database credentials in `.env`
- Ensure MySQL service is running
- Verify database exists

**Permission Denied**
- Set proper folder permissions (755)
- Check file ownership
- Ensure web server has write access

**Missing Dependencies**
- Run `composer install`
- Check PHP version compatibility
- Install missing PHP extensions

**Slow Performance**
- Enable caching: `php artisan config:cache`
- Optimize database queries
- Consider using Redis for sessions

## 📞 Support

For technical support and updates:
- **Email**: support@possystem.com
- **Documentation**: https://docs.possystem.com
- **GitHub Issues**: https://github.com/yourusername/pos-system/issues

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 🙏 Acknowledgments

- Laravel Framework
- Bootstrap UI Components
- Font Awesome Icons
- Spatie Laravel Permissions
- DomPDF for PDF generation

---

**Made with ❤️ for small businesses worldwide**

For more information, visit our website: [www.possystem.com](https://www.possystem.com) 