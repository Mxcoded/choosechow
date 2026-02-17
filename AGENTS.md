# AGENTS.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

ChooseChow is a food delivery platform connecting home-based chefs with customers. Built with Laravel 12, Tailwind CSS 4, and Alpine.js. Payment processing via Paystack (Nigerian payment gateway).

## Development Commands

```powershell
# Full dev environment (server + queue + logs + vite)
composer dev

# Individual services
php artisan serve              # Laravel server at localhost:8000
npm run dev                    # Vite dev server with HMR

# Testing
composer test                  # Run all tests (clears config cache first)
php artisan test               # Run tests directly
php artisan test --filter=ChefOrderTest  # Run specific test

# Assets
npm run build                  # Production build

# Database
php artisan migrate            # Run migrations
php artisan db:seed            # Seed database
php artisan migrate:fresh --seed  # Reset and seed
```

## Architecture

### User Roles & Middleware

Three roles managed via Spatie Permission: `admin`, `chef`, `customer`

| Role | Middleware | Route Prefix | Controller Namespace |
|------|------------|--------------|---------------------|
| Admin | `admin` | `/admin` | `App\Http\Controllers\Admin\` |
| Chef | `chef` | `/chef` | `App\Http\Controllers\Chef\` |
| Customer | `customer` | `/customer` | `App\Http\Controllers\Customer\` |

Role checks use `$user->hasRole('rolename')` with fallback to legacy `user_type` field.

### Key Models & Relationships

**User** - Central model for all roles
- `ordersPlaced()` → orders as customer (`user_id` FK)
- `ordersReceived()` / `chefOrders()` → orders as chef (`chef_id` FK)
- `chefProfile()` → chef-specific profile data
- `menus()` → chef's menu items
- `wallet()` → chef's earnings wallet

**Order** - Links customer, chef, and payment
- Has `user_id` (customer) and `chef_id` (chef)
- Status flow: `pending_payment` → `pending` → `preparing` → `ready` → `completed`

**Menu** - Dishes offered by chefs
- Belongs to User (chef) via `user_id`
- `is_available` toggle for availability

### Services

`App\Services\PaystackService` - Handles all Paystack payment operations:
- `initializeTransaction()` - Creates payment, returns redirect URL
- `verifyTransaction()` - Validates payment after callback
- `verifySignature()` - Security check for webhooks

### View Organization

```
resources/views/
├── admin/          # Admin dashboard views
├── chef/           # Chef dashboard, menus, orders, wallet
├── customer/       # Customer profile, orders
├── layouts/        # Base layouts (app.blade.php)
├── pages/          # Public pages (landing, about, contact)
├── auth/           # Login, register, password reset
├── cart/           # Shopping cart
└── emails/         # Email templates
```

## Configuration

- **Database**: SQLite file `choosechow_db` for dev; MySQL configured in `.env`
- **Queue**: Sync in dev; configure Redis/database for production
- **Mail**: SMTP via `.env` settings
- **Paystack**: Keys in `config/services.php`, uses env vars `PAYSTACK_SECRET_KEY`, `PAYSTACK_PUBLIC_KEY`

## Testing

Tests use SQLite in-memory database (see `phpunit.xml`). Feature tests are in `tests/Feature/`.

When writing tests:
- Extend `Tests\TestCase`
- Use `RefreshDatabase` trait for database tests
- Create users with roles via `$user->assignRole('chef')`

## Known Issues

See `PROJECT_REVIEW.md` for documented issues including:
- Order status inconsistencies between controllers
- Multi-chef cart orders only save first chef's ID
- Incomplete features: coupons, notifications, favorites, referrals

## Code Patterns

### Route Constraints
The public chef profile route `/chef/{chef}` uses regex to avoid shadowing dashboard routes:
```php
Route::get('/chef/{chef}', ...)->where('chef', '^(?!menus|orders|wallet|profile|personal-info).*$');
```

### Cart Storage
Cart is session-based (`session('cart')`), not persisted to database.

### Accessors
User model uses Laravel Attribute casts for `fullName`, `avatarUrl` computed properties.
