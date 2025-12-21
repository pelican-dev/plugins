# Billing

A simple billing plugin that allows users to purchase servers using Stripe as the payment processor.

> [!CAUTION]
> This plugin is incomplete!

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

## Dependencies

| Package | Version |
|---------|---------|
| `stripe/stripe-php` | ^18.0 |
