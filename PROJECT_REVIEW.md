# ChooseChow Project Review - UX & Logical Issues

## 🔴 CRITICAL ISSUES

### 1. **Dark Mode Code Still Present (User Requested Removal)**
- **Location**: `resources/views/layouts/app.blade.php` (Lines 17-25, multiple dark classes)
- **Issue**: Despite user requesting dark mode removal, the layout still contains:
  - Dark mode initialization script
  - Multiple `dark:` Tailwind classes throughout
  - Theme toggle button with JavaScript references
  - Alpine.js dark mode toggle logic
- **Impact**: User experience inconsistent; dark mode classes conflict with light-only design
- **Fix Required**: Remove all dark mode references from layout

### 2. **Order Model Relationship Error**
- **Location**: `app/Models/User.php` (Line 79-86)
- **Issue**: 
```php
// As a Chef (Selling food) - Maps to 'user_id' in orders table
public function ordersReceived()
{
    return $this->hasMany(Order::class, 'user_id');  // ❌ WRONG!
}
```
- **Problem**: This maps to the customer, not the chef. Should be `chef_id`
- **Impact**: Chefs cannot see their received orders; queries return customer orders instead
- **Fix**: Change to `->hasMany(Order::class, 'chef_id')`

### 3. **Order Status Inconsistencies**
- **Location**: Multiple files
- **Issue**: Order statuses are inconsistent across the codebase:
  - Migration says: `pending, accepted, ready, completed, cancelled`
  - CheckoutController says: `pending_payment, pending, preparing, ready, completed, cancelled`
  - Chef/Customer controllers use different status names
  - Dashboard uses: `pending, processing, ready_for_pickup`
- **Impact**: Queries fail; orders disappear from lists; status checks break
- **Example**: 
```php
// Chef OrderController filters by: 'pending', 'preparing', 'ready'
// But checkout creates: 'pending_payment' initially
// These don't match!
```

### 4. **Payment Flow Logic Broken**
- **Location**: `CheckoutController.php` (Lines 45-180)
- **Issue**:
  - Orders created with status `pending_payment`
  - After payment callback, status should update to `pending`
  - But checkout doesn't verify if payment actually succeeded before marking as `paid`
  - No validation that Paystack response is authentic
- **Impact**: Users can mark payments as complete without actually paying

### 5. **Session-Based Cart Inconsistency**
- **Location**: `CartController.php` and views
- **Issue**: 
  - Cart stored in session (lost on logout)
  - No database persistence
  - Different properties used: `menu_id` vs index, `price` vs properties
  - No validation of menu availability when checkout happens
- **Impact**: Cart lost on page refresh/logout; stale menu items can be ordered

---

## ⚠️ MAJOR UX ISSUES

### 6. **No Delivery Zone Validation**
- **Location**: `CheckoutController.php`, `DeliveryZone.php`
- **Issue**: 
  - Delivery zones created but never validated during checkout
  - No check if customer's delivery address is in chef's delivery zone
  - Delivery fee calculated incorrectly (no zone lookup)
- **Impact**: Chefs can be asked to deliver outside their service area

### 7. **Multi-Chef Orders Not Handled**
- **Location**: `CheckoutController.php` (Line 90-95)
- **Issue**:
```php
$firstItemId = array_key_first($cart);
$firstMenu = Menu::find($firstItemId);
$primaryChefId = $firstMenu ? $firstMenu->user_id : 1;
$order = Order::create([
    'chef_id' => $primaryChefId,  // ❌ Only first chef!
]);
```
- **Problem**: If customer orders from 2 chefs, only first chef's ID is saved
- **Impact**: Second chef never sees the order; customer's order is incomplete
- **Fix Needed**: Either:
  - Create separate order per chef
  - Add `chef_id` to `OrderItem` table
  - Use junction table for multiple chefs

### 8. **No Wallet/Balance Verification**
- **Location**: `WalletController.php` (Lines 46-47)
- **Issue**: Insufficient balance check works, but:
  - No verification that completed orders have been added to wallet
  - No audit trail of balance changes
  - Payout system doesn't check if funds came from actual orders
- **Impact**: Chefs could claim payout for orders they didn't complete

### 9. **Email Configuration Issues**
- **Location**: `.env` and `config/mail.php`
- **Issue**:
  - SMTP host is `choosechow.com` (not a valid SMTP server)
  - Should be `smtp.gmail.com` or other mail service
  - App password has special characters that might cause encoding issues
- **Impact**: Transactional emails don't send; users don't get confirmations

### 10. **Menu Availability Not Checked During Order**
- **Location**: `CheckoutController.php`, `CartController.php`
- **Issue**:
  - Cart can contain unavailable menus (`is_available = false`)
  - No validation when order is created
  - Chef can toggle menu availability after customer ordered it
- **Impact**: Order created for unavailable item; chef must manually cancel

### 11. **Review System Only Works for First Order**
- **Location**: `app/Models/Review.php` migration
- **Issue**:
  - Unique constraint on `order_id`: `$table->unique('order_id')`
  - Customer can only leave one review per order (correct)
  - BUT: No scope to prevent reviewing same chef twice
  - No verification that reviewer actually placed the order
- **Impact**: Reviews can be fraudulent; no multi-order review tracking

### 12. **Chef Profile Verification Without Proper Validation**
- **Location**: `AdminController.php` (Lines 159-168)
- **Issue**:
```php
public function verifyChef($id)
{
    $chef = User::findOrFail($id);
    if ($chef->chefProfile) {
        $chef->chefProfile->update(['is_verified' => true]);
        return back()->with('success', 'Kitchen verified!');
    }
}
```
- **Problem**: 
  - No verification of documents
  - No check of chef qualifications
  - No audit trail
  - Can't unverify a chef if documents become invalid
- **Impact**: Bad/fraudulent chefs can be approved

---

## ⚠️ DATA INTEGRITY ISSUES

### 13. **Order Snapshot Data Incomplete**
- **Location**: `OrderItem.php`, migrations
- **Issue**: Only saves `menu_name` and `price` at purchase time
- **Missing**: 
  - Chef name (if chef later updates their business name, order loses context)
  - Ingredients/allergens (for refund/dispute purposes)
  - Original menu description
- **Impact**: Can't recreate original order; disputes are harder

### 14. **Wallet Balance Tracking Issues**
- **Location**: `Wallet.php`, `WalletController.php`
- **Issue**:
  - No transaction history table
  - Balance can only go forward
  - Can't trace money flow: order → wallet → payout
  - No dispute resolution mechanism
- **Impact**: Hard to debug balance problems; no audit trail

### 15. **Withdrawal Request Missing CRITICAL Field**
- **Location**: `database/migrations/2026_02_05_131715_add_reference_id_to_withdrawals_table.php`
- **Issue**: Just added `reference_id` but:
  - No `withdrawal_requests` or `withdrawals` table definition found
  - Unclear where withdrawal requests are stored
  - Missing status tracking: requested → approved → processed → completed/failed
- **Impact**: Withdrawal system incomplete; chefs can't track requests

### 16. **No Subscription Integration with Orders**
- **Location**: `Order.php`, `UserSubscription.php`
- **Issue**: 
  - Subscription system exists but orders don't reference subscription
  - No way to track if order is from subscriber
  - Subscribers don't get discount applied
  - No exclusive menu items for subscribers
- **Impact**: Subscription feature ineffective; no revenue from subscriptions

---

## ⚠️ SECURITY ISSUES

### 17. **No Permission Checks for Route Access**
- **Location**: `routes/web.php`, various controllers
- **Issue**:
  - Chef route: `/chef/{chef}` uses regex but no ownership check
  - Admin routes have `middleware(['admin'])` but no role verification in controller
  - Customer can potentially access other customer's orders if they guess ID
- **Impact**: Unauthorized access; data leaks

### 18. **No Rate Limiting**
- **Location**: `routes/web.php`
- **Issue**: 
  - Contact form submission (`/contact`) not rate limited
  - Payment callbacks not protected
  - Cart add/remove not rate limited
- **Impact**: Spam attacks; fake orders

### 19. **SQL Injection Risk in Dashboard**
- **Location**: `DashboardController.php` (Line 24)
- **Issue**: 
```php
$totalSales = Order::where('payment_status', 'paid')->sum('total_amount');
// This is fine, but...
$platformProfit = $totalSales * 0.05; // Hard-coded commission rate
```
- **Problem**: Commission rate should be configurable, not hard-coded
- **Impact**: Can't change rates without code change; no audit trail

### 20. **No CSRF Protection Check**
- **Location**: Tests disable CSRF: `$this->withoutMiddleware([ValidateCsrfToken::class])`
- **Issue**: Tests bypass security; production might too
- **Impact**: Potential CSRF attacks

---

## 🟡 LOGICAL ISSUES

### 21. **Chef Order Status Flow Unclear**
- **Location**: `Chef/OrderController.php`
- **Issue**: 
  - Order can be: pending, preparing, ready, completed, cancelled
  - No `accepted` status
  - Chef can skip from pending straight to completed
  - No time tracking (when chef started, etc.)
- **Impact**: No accountability; can't calculate actual prep time

### 22. **Payment Method Stored But Not Used**
- **Location**: `Order.php`, migrations
- **Issue**:
  - `payment_method` column stores 'paystack', 'card', 'cash'
  - All orders use Paystack
  - Cash/transfer methods not implemented
- **Impact**: Confusing for users; incomplete feature

### 23. **Admin Settings Table Unused**
- **Location**: `AdminSettings.php`
- **Issue**: 
  - Table created but never used
  - Commission rate hard-coded in Dashboard
  - Minimum order amount hard-coded
- **Impact**: Settings can't be changed without code edit

### 24. **Coupon System Only Partially Implemented**
- **Location**: `Coupon.php`, `CouponUsage.php`
- **Issue**:
  - Models exist but not used in CheckoutController
  - No coupon validation during checkout
  - No expiration date checking
  - No usage limit enforcement
- **Impact**: Coupon system doesn't work

### 25. **Dietary Preferences Collected But Unused**
- **Location**: `DietaryPreference.php`, migrations
- **Issue**:
  - Model exists but not linked to orders
  - Users can set preferences but it's not considered during recommendations
  - Not used in review/rating system
- **Impact**: Feature is incomplete

### 26. **Subscription Plans Not Enforced**
- **Location**: `SubscriptionPlan.php`, `SubscriptionController.php`
- **Issue**:
  - Plans defined but no pricing
  - No features differentiated by plan
  - Free plan doesn't exist despite being marketed
  - Subscription status not checked in meal access
- **Impact**: Users don't benefit from subscription

### 27. **Notification System Exists But Unused**
- **Location**: `Notification.php`
- **Issue**:
  - Model exists
  - No code creates notifications
  - No notification delivery mechanism
- **Impact**: Users don't get alerts on orders/payments

### 28. **Favorite System Incomplete**
- **Location**: `Favorite.php`
- **Issue**:
  - Model exists
  - No controller/routes to add/remove favorites
  - Not displayed anywhere
- **Impact**: Can't use favorites

### 29. **Referral System Not Implemented**
- **Location**: `User.php` Line 106: `public function referrals()`
- **Issue**:
  - Table has `referred_by` and `referral_code`
  - No logic to generate codes
  - No referral rewards
  - No referral links in UI
- **Impact**: Incomplete feature

### 30. **Pagination Hardcoded**
- **Location**: Multiple controllers (15, 10, 5 items per page)
- **Issue**: No admin setting to change pagination
- **Impact**: Can't optimize for different pages

---

## 🔧 CONFIGURATION ISSUES

### 31. **Environment Variables Not Set Properly**
- **Location**: `.env`
- **Current State**:
  - MAIL_HOST set to `choosechow.com` (invalid)
  - PAYSTACK keys might be test keys (need verification)
  - QUEUE_CONNECTION now `sync` (good for testing, bad for production)
  - No CACHE configuration
- **Impact**: Production deployment will fail

### 32. **Missing Feature Toggles**
- **Location**: No configuration file
- **Issue**:
  - Can't enable/disable features without code changes
  - Can't change rates/limits without code changes
  - No A/B testing capability
- **Impact**: Inflexible system

---

## 📋 MIGRATION & SCHEMA ISSUES

### 33. **Migration File Name Typo**
- **Location**: `2025_09_20_221613_create_reviews_table.php`
- **Issue**: 
  - Migration creates `reviews` table
  - But down() drops `order_reviews` (non-existent)
- **Impact**: Rollback fails

### 34. **Missing Indexes for Performance**
- **Location**: Multiple migrations
- **Issue**: 
  - Order lookups missing index on `status`
  - Menu lookups missing index on `user_id` and `is_available`
  - No index on `payment_status`
- **Impact**: Slow queries on production with large datasets

### 35. **Inconsistent Foreign Key Naming**
- **Location**: Migrations
- **Issue**:
  - Some use `chef_id`, some use `user_id` for chef
  - Some models use `customer_id`, migrations use `user_id`
- **Impact**: Confusing; increases error risk

---

## 🎯 RECOMMENDATIONS PRIORITY

**MUST FIX (Before Launch):**
1. ❌ Remove dark mode code from layout
2. ❌ Fix `ordersReceived()` relationship
3. ❌ Standardize order statuses
4. ❌ Implement multi-chef order handling
5. ❌ Fix SMTP configuration
6. ❌ Add delivery zone validation
7. ❌ Add menu availability check at checkout
8. ❌ Add payment authentication verification

**SHOULD FIX (Before Public):**
9. Add admin settings system
10. Implement coupon system
11. Complete subscription integration
12. Add notification system
13. Implement favorites system
14. Add transaction audit trail
15. Add withdrawal request system
16. Rate limit sensitive endpoints

**NICE TO HAVE (Future):**
17. Referral system
18. Performance optimizations
19. Analytics dashboard
20. Admin settings UI

