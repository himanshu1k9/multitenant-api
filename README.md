# Laravel Multi-Tenant SaaS API

This project is a minimal backend built using Laravel to manage a multi-tenant SaaS application, where a single user can create and switch between multiple companies. Each authenticated user can create, update, delete companies, and choose one company as their current working context. All future data will be scoped to this current company.

---

## Project Features

1. User authentication using Laravel Sanctum.
2. Each user can:
   - Register and login.
   - Create multiple companies.
   - Switch between companies.
3. All actions and data are scoped to the currently selected company (active company).

---

## Technology Stack

- Laravel (v10 or above)
- Sanctum for API authentication
- MySQL (or any supported database)
- PHP 8.1 or higher

---

## Getting Started

### Step 1: Clone the Repository
git clone https://github.com/himanshu1k9/multitenant-api.git
cd multitenant-api

### Step 2: Install Dependencies
composer install

### Step 3: Environment Setup

- Copy the `.env` file and set database credentials

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_tenant
DB_USERNAME=root
DB_PASSWORD=

### Step 4: Run Migrations
php artisan migrate

### Step 5: Run Server
php artisan serve

## Database Structure
users
- id
- name
- email
- password
- active_company_id (nullable, references companies.id)

companies
- id
- user_id (foreign key to users)
- name
- address
- industry
- timestamps
- deleted_at (soft deletes)

---

## API Endpoints

### User Authentication

#### Register
POST `/api/register`

Request Body:
{
"name": "John Doe",
"email": "john@example.com",
"password": "password",
"password_confirmation": "password"
}

#### Login
POST `/api/login`
Request Body:
{
"email": "john@example.com",
"password": "password"
}

#### Logout
POST `/api/logout`

- Invalidates the session
- Deletes the token
- Requires Bearer token in Authorization header

### Company Management (Authenticated)

All company routes are protected by Sanctum middleware. You must provide a valid token.

#### List Companies
GET `/api/companies`

Returns all companies created by the authenticated user.

---

#### Create Company
POST `/api/companies`

Request Body:
{
"name": "Acme Inc",
"address": "123 Street, NY",
"industry": "Technology"
}

#### Update Company
PUT `/api/companies/{id}`

Request Body:
{
"name": "Acme Solutions",
"address": "456 Lane, SF",
"industry": "IT Services"
}

#### Delete Company
DELETE `/api/companies/{id}`

Soft deletes the company. Only accessible to the owner.

---

#### Switch Active Company
POST `/api/companies/switch/{id}`

Sets the selected company as the active company for the logged-in user.
- When a user switches to a specific company using the switch API, it updates the `active_company_id` column in the `users` table.
- Future modules (like projects, invoices) will use this `active_company_id` to scope data.

Example:
```php
$activeCompanyId = auth()->user()->active_company_id;

Project::where('company_id', $activeCompanyId)->get();

Authorization Header
For all protected routes, include the token:

Authorization: Bearer YOUR_ACCESS_TOKEN



