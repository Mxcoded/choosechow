# ChooseChow 🍽️

**Connecting Food Lovers with Local Chefs**

ChooseChow is a modern food delivery platform built with Laravel that bridges the gap between talented local chefs and food enthusiasts. Our platform empowers home-based chefs to showcase their culinary skills while providing customers with authentic, homemade meals delivered right to their doorstep.

![ChooseChow Banner](https://via.placeholder.com/1200x400/ef4444/ffffff?text=ChooseChow+-+Choose+Your+Flavor)

## 🌟 Features

### For Food Lovers
- **Discover Local Chefs** - Browse profiles of verified home-based chefs in your area
- **Diverse Cuisines** - From traditional Nigerian dishes to international flavors
- **Real-time Tracking** - Track your order from kitchen to doorstep
- **Flexible Subscriptions** - Choose from Explorer (Free), Foodie, or Connoisseur plans
- **Exclusive Benefits** - Enjoy discounts, free delivery, and priority support

### For Chefs
- **Professional Profiles** - Showcase your culinary expertise and specialties
- **Menu Management** - Easy-to-use tools for managing your offerings
- **Order Dashboard** - Streamlined order management and customer communication
- **Analytics & Insights** - Track your performance and grow your business
- **Flexible Payouts** - Weekly, daily, or instant payout options
- **Marketing Tools** - Promote your dishes and reach more customers

### Platform Features
- **Secure Payments** - Multiple payment options with secure processing
- **Review System** - Build trust through customer reviews and ratings
- **Mobile Responsive** - Optimized experience across all devices
- **Admin Dashboard** - Comprehensive platform management tools
- **Multi-language Support** - English and local language support

## 🚀 Tech Stack

- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **Payments**: Paystack Integration
- **File Storage**: Laravel Storage (Local/S3)
- **Queue System**: Redis/Database
- **Real-time**: Laravel Echo with Pusher
- **Email**: Laravel Mail with SMTP
- **Testing**: PHPUnit, Laravel Dusk

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- PHP 8.1 or higher
- Composer
- Node.js 16+ and npm
- MySQL 8.0+
- Redis (optional, for queues and caching)

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/mxcoded/choosechow.git
   cd choosechow

Install PHP dependencies
composer install
Install Node.js dependencies
npm install
Environment setup
cp .env.example .env
php artisan key:generate
Configure your 
.env
 file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=choosechow
DB_USERNAME=your_username
DB_PASSWORD=your_password

PAYSTACK_PUBLIC_KEY=your_paystack_public_key
PAYSTACK_SECRET_KEY=your_paystack_secret_key

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
Database setup
php artisan migrate
php artisan db:seed
Storage setup
php artisan storage:link
Build assets
npm run build
Start the development server
php artisan serve
Visit 
http://localhost:8000
 to see your ChooseChow installation.

🗄️ Database Schema

Key Tables

users
 - Customer and chef accounts
chef_profiles
 - Extended chef information
menus
 - Chef menu items
orders
 - Order management
subscriptions
 - User subscription plans
reviews
 - Customer reviews and ratings
payments
 - Payment transaction records
🔧 Configuration

Payment Integration

Configure Paystack for payment processing:

// config/paystack.php
return [
    'publicKey' => env('PAYSTACK_PUBLIC_KEY'),
    'secretKey' => env('PAYSTACK_SECRET_KEY'),
    'paymentUrl' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
];

Subscription Plans

Modify subscription plans in:

// config/subscriptions.php
return [
    'customer_plans' => [
        'explorer' => ['name' => 'Explorer', 'price' => 0],
        'foodie' => ['name' => 'Foodie', 'price' => 2500],
        'connoisseur' => ['name' => 'Connoisseur', 'price' => 5000],
    ],
    'chef_plans' => [
        'starter' => ['name' => 'Starter', 'price' => 3000, 'commission' => 8],
        'professional' => ['name' => 'Professional', 'price' => 6000, 'commission' => 6],
        'master' => ['name' => 'Master', 'price' => 12000, 'commission' => 4],
    ],
];

🧪 Testing

Run the test suite:

# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

📱 API Documentation

ChooseChow provides a RESTful API for mobile app integration:

Authentication

POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout

Orders

GET /api/orders
POST /api/orders
GET /api/orders/{id}
PUT /api/orders/{id}/status

Menus

GET /api/menus
GET /api/chefs/{id}/menus
POST /api/menus (Chef only)

Full API documentation available at 
/api/documentation
 when running locally.

🚀 Deployment

Production Setup

Server Requirements
PHP 8.1+ with required extensions
MySQL 8.0+
Nginx or Apache
SSL certificate
Environment Configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
Queue Workers
php artisan queue:work --daemon
Scheduled Tasks

Add to crontab:
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
🤝 Contributing

We welcome contributions to ChooseChow! Please follow these steps:

Fork the repository
Create a feature branch (
git checkout -b feature/amazing-feature
)
Commit your changes (
git commit -m 'Add some amazing feature'
)
Push to the branch (
git push origin feature/amazing-feature
)
Open a Pull Request
Development Guidelines

Follow PSR-12 coding standards
Write tests for new features
Update documentation as needed
Use meaningful commit messages
📄 License

This project is licensed under the MIT License - see the 
LICENSE
 file for details.

🙏 Acknowledgments

Laravel community for the amazing framework
Tailwind CSS for the utility-first CSS framework
Paystack for payment processing
All the talented chefs who inspire this platform
📞 Support

Email
: support@choosechow.com
Documentation
: 
https://docs.choosechow.com
Issues
: 
https://github.com/mxcoded/choosechow/issues
🗺️ Roadmap

[ ] Mobile app (React Native)
[ ] Real-time chat between customers and chefs
[ ] Advanced analytics dashboard
[ ] Multi-city expansion
[ ] Chef certification program
[ ] Loyalty rewards system
Made with ❤️ for food lovers and talented chefs

ChooseChow - Where every meal tells a story


This README provides:

**🎯 Professional Structure:**
- Clear project description and value proposition
- Comprehensive feature list for all user types
- Complete installation and setup instructions
- Technical specifications and requirements

**💻 Developer-Friendly:**
- Detailed tech stack information
- Step-by-step installation guide
- Configuration examples
- Testing instructions
- API documentation overview

**🚀 Production-Ready:**
- Deployment guidelines
- Performance optimization tips
- Security considerations
- Contribution guidelines

**📈 Project Management:**
- Roadmap for future features
- Support information
- License details
- Acknowledgments