# 🛍️ E-Commerce REST API

A Laravel-based REST API backend for a full-stack eCommerce 
platform, featuring Algolia Search with hierarchical category 
filtering, product and inventory management, order processing, 
and Laravel Sanctum authentication.

🔗 **Related Repositories**
- [Storefront (Next.js)](https://github.com/kaiser-hamid/shop_frontend)
- [Admin Panel (Next.js)](https://github.com/kaiser-hamid/shop_admin)

---

## ✨ Features

- Product management with category, brand, and inventory control
- Hierarchical category system (3-level deep) synced with Algolia
- Algolia Search integration with faceted filtering by category, 
  brand, price, and rating
- Customer management and order processing
- Laravel Sanctum authentication
- API Resources for consistent JSON response structure

---

## 🔍 Algolia Hierarchical Categories

One of the key features of this API is the hierarchical category 
implementation for Algolia Search. Products support up to 3 levels 
of category nesting.

This enables drill-down category filtering on the frontend 
using Algolia's InstantSearch widgets.

See [HIERARCHICAL_CATEGORIES.md](./HIERARCHICAL_CATEGORIES.md) 
for full implementation details.

---

## 🛠️ Tech Stack

**Backend:** Laravel 11, PHP 8.x  
**Database:** MySQL  
**Search:** Algolia Scout  
**Auth:** Laravel Sanctum  

---

## 🚀 Getting Started

### Requirements
- PHP 8.1+
- Composer
- MySQL
- Algolia account (free tier works)

### Installation

```bash
git clone https://github.com/kaiser-hamid/shop_api.git
cd shop_api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Environment Variables:

ALGOLIA_APP_ID=your_app_id
ALGOLIA_SECRET=your_admin_api_key
SANCTUM_STATEFUL_DOMAINS=localhost:3000

### Sync Algolia Index

```bash
php artisan scout:import "App\Models\Product"
php artisan products:reindex-hierarchical-categories
```

---

## 📁 Project Structure

app/

├── Http/

│   ├── Controllers/    # API controllers

│   └── Resources/      # API response formatting

├── Models/             # Eloquent models

└── Services/           # Business logic layer

database/

├── migrations/

└── seeders/

---

## 🔮 Planned Improvements

- Stripe payment integration
- Order status tracking and notifications
- Product reviews and ratings
- Redis caching for search results
- API rate limiting
