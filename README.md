<p align="center">
  <img src="public/images/logo.png" alt="WashWise Logo" width="160" height="160" style="border-radius: 50%; object-fit: cover;">
</p>

# WashWise

WashWise is a web-based car wash management system developed for JNJ Car Wash. It helps customers book car wash appointments online while giving the staff and administrator a central panel for monitoring schedules, services, sales, inventory, notifications, and business settings.

This project was created for the Web-Based Management Information System subject. It demonstrates how a small service business can use a management information system to organize daily operations, reduce manual record keeping, and make appointment, sales, and inventory information easier to manage.

## Features

- Public appointment booking for customers.
- Service and vehicle-size selection.
- Appointment scheduling based on business settings.
- Admin and staff panel for managing appointments.
- Service, size, sales, and inventory management.
- Inventory logs and stock adjustment records.
- Notifications for operational updates.
- Business profile and account settings.
- Admin-only user management.
- Daily report export.

## Technology Stack

- Laravel 12
- PHP 8.2+
- Vue 3
- Bootstrap 5
- Vite
- MySQL or SQLite

## Installation

1. Install PHP, Composer, Node.js, and a supported database.
2. Clone the project and install dependencies:

```bash
composer install
npm install
```

3. Create the environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure the database connection in `.env`, then run the migrations and seeders:

```bash
php artisan migrate --seed
```

5. Build frontend assets:

```bash
npm run build
```

6. Start the local server:

```bash
php artisan serve
```

Open the application in the browser at the URL shown by Laravel, usually `http://127.0.0.1:8000`.

## Development

Run the backend and frontend development servers while working on the project:

```bash
composer run dev
```

You can also run Vite separately:

```bash
npm run dev
```

## Testing

Run the Laravel test suite with:

```bash
composer test
```

## Purpose

WashWise is intended as an academic system prototype for managing the operations of a car wash business. The project focuses on practical MIS functions such as customer appointment handling, operational monitoring, sales tracking, inventory control, and administrative management.
