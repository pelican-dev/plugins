# Billing

A simple billing plugin that allows users to purchase servers using Stripe as the payment processor.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `billing` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |

## Features

- Stripe integration for payment processing
- Product management with customizable pricing intervals (monthly, yearly, etc.)
- Coupon system with percentage and fixed amount discounts
- Customer management linked to user accounts
- Order tracking and management
- Dashboard widget for users to view and purchase products
- Multi-currency support (USD, EUR, GBP)
- Configurable deployment tags for automatic server provisioning

## Technical Details

### Database Migrations

| Migration | Description |
|-----------|-------------|
| `001_create_coupons_table.php` | Creates the `coupons` table for discount codes |
| `002_create_customers_table.php` | Creates the `customers` table linking users to Stripe |
| `003_create_products_table.php` | Creates the `products` table for purchasable items |
| `004_create_product_prices_table.php` | Creates the `product_prices` table for pricing tiers |
| `005_create_orders_table.php` | Creates the `orders` table for tracking purchases |

### Models

- `Coupon` - Discount codes with type and amount
- `Customer` - Links users to Stripe customer IDs
- `Order` - Purchase records with status tracking
- `Product` - Purchasable server configurations
- `ProductPrice` - Pricing options for products

### Enums

- `CouponType` - Percentage or fixed amount discounts
- `OrderStatus` - Order lifecycle states
- `PriceInterval` - Billing intervals (monthly, yearly, etc.)

### Policies

- `CustomerPolicy` - Authorization for customer management
- `OrderPolicy` - Authorization for order management
- `ProductPolicy` - Authorization for product management

### Filament Resources

| Panel | Resources |
|-------|-----------|
| Admin | `CouponResource`, `CustomerResource`, `OrderResource`, `ProductResource` |
| App | `OrdersResource`, Dashboard page with product widgets |

### Console Commands

- `CheckOrdersCommand` - Scheduled command to verify order statuses

### API Controllers

- `CheckoutController` - Handles Stripe checkout sessions

### Views

- `widget.blade.php` - Product display widget

### Configuration

```php
// config/billing.php
return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'currency' => env('BILLING_CURRENCY', 'USD'),
    'deployment_tags' => env('BILLING_DEPLOYMENT_TAGS'),
];
```

### Environment Variables

| Variable | Description |
|----------|-------------|
| `STRIPE_KEY` | Stripe publishable key |
| `STRIPE_SECRET` | Stripe secret key |
| `BILLING_CURRENCY` | Currency for transactions (USD, EUR, GBP) |
| `BILLING_DEPLOYMENT_TAGS` | Default node tags for server deployment |

## Dependencies

| Package | Version |
|---------|---------|
| `stripe/stripe-php` | ^18.0 |
