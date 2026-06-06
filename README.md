# Multi-Tenant Application

A multi-tenant application built with **Laravel 12**, **PostgreSQL schema isolation**, and **Filament** admin panel.

---

## ✨ Features

### Admin Panel (Filament)
- Super Admin login at `/admin`
- Dashboard with live tenant & user statistics
- Full Tenant CRUD — create, view, edit, delete
- One-click Activate / Deactivate tenants
- Automatic schema provisioning on tenant creation

### Tenant Application
- Isolated login per organization (`/login`)
- Self-registration within an organization
- Dashboard with task statistics, overdue alerts, quick actions
- Full Task CRUD with filtering, search, and pagination
- Tenant data isolation enforced at middleware and controller level

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- PostgreSQL 14+

### 1. Clone & Install

```bash
git clone <repo-url>
cd multi-tenant

composer run setup


```

Edit `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mt
DB_USERNAME=postgres
DB_PASSWORD=your_password

SESSION_DRIVER=file

```

### 3. Create Database & Run Migrations

```bash

# Run migrations
php artisan migrate

# Seed with demo data (super admin)
php artisan db:seed
```

### 4. Run

```bash
php artisan serve
```

## 🔐 Default Credentials

After seeding (`php artisan db:seed`):

### Super Admin (Filament)
| Field    | Value             |
|----------|-------------------|
| URL      | `/admin`          |
| Email    | `admin@test.com` |
| Password | `1234`        |


## 🗺 Route Map

```
GET  /                          → redirect to /login
GET  /login                     → tenant-login (auth.tenant-login)
POST /login                     → TenantAuthController@login
GET  /register                  → tenant-register
POST /register                  → TenantAuthController@register
GET  /suspended                 → tenant.suspended view

# Protected tenant app routes (prefix: /app)
POST /app/logout                → TenantAuthController@logout
GET  /app/dashboard             → DashboardController@index
GET  /app/tasks                 → TaskController@index
GET  /app/tasks/create          → TaskController@create
POST /app/tasks                 → TaskController@store
GET  /app/tasks/{task}          → TaskController@show
GET  /app/tasks/{task}/edit     → TaskController@edit
PUT  /app/tasks/{task}          → TaskController@update
DELETE /app/tasks/{task}        → TaskController@destroy

# Admin panel (Filament)
GET  /admin                     → Filament login
GET  /admin/tenants             → Tenant list
GET  /admin/tenants/create      → Create tenant
GET  /admin/tenants/{id}        → View tenant
GET  /admin/tenants/{id}/edit   → Edit tenant
```

---

## 📝 License

MIT
