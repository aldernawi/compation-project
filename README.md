# Competition Management Platform

A comprehensive, role-based web and mobile platform for organizing, managing, and evaluating competitions. Built with Laravel, Inertia.js, React, and Tailwind CSS, it supports Arabic localization, Libyan Dinar (LYD) currency, registration-only competitions, and a dedicated mobile application.

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Architecture & Tech Stack](#architecture--tech-stack)
4. [Project Structure](#project-structure)
5. [Requirements](#requirements)
6. [Installation](#installation)
7. [Configuration](#configuration)
8. [Database](#database)
9. [Usage](#usage)
10. [API](#api)
11. [User Roles & Permissions](#user-roles--permissions)
12. [Mobile Application](#mobile-application)
13. [Testing](#testing)
14. [Deployment](#deployment)
15. [Security](#security)
16. [License](#license)

---

## Overview

The Competition Management Platform streamlines the lifecycle of competitions — from creation and registration to submission, evaluation, ranking, and reporting. It provides dedicated dashboards for administrators, organizers, judges, and participants, ensuring each role has the tools needed for their responsibilities.

Key highlights:

- Arabic-first localization with Libyan Dinar (LYD) support.
- Registration-only competition access for authenticated users.
- Role-based dashboards and permissions.
- REST API for mobile and external integrations.
- Push notifications via FCM tokens.
- Media handling for submissions and reports.
- Modern React frontend powered by Inertia.js.

---

## Features

### Competition Management

- Create and categorize competitions with custom types.
- Configure prizes, dates, and registration requirements.
- Publish and unpublish competitions.
- Publish results with timestamps.
- Support registration-only participation.

### Submissions & Evaluations

- Participants submit entries and attachments.
- Judges evaluate submissions with structured criteria.
- Track evaluation status and rejection reasons.
- Publish final results and generate rankings.

### Prizes & Rankings

- Define prizes per competition.
- Associate prizes with winning submissions.
- Compute and display rankings by score.

### User Management

- Role-based users: Admin, Organizer, Judge, Participant.
- Profile and security settings.
- Phone number support and account suspension.
- Passkey support for modern authentication.

### Reporting & Notifications

- Admin and organizer reports.
- FCM push notifications.
- Notifications for results, submissions, and deadlines.

### Localization & Currency

- Arabic user interface and messaging.
- Libyan Dinar (LYD) currency formatting.
- Right-to-left (RTL) layout support.

---

## Architecture & Tech Stack

| Layer | Technology |
|-------|------------|
| Backend Framework | Laravel 13 (PHP 8.5) |
| Frontend Framework | React 19 |
| SPA Bridge | Inertia.js 3 |
| Styling | Tailwind CSS 4 |
| API Authentication | Laravel Sanctum 4 |
| Web Authentication | Laravel Fortify 1 |
| Testing | Pest 4 / PHPUnit 12 |
| Database | MySQL / PostgreSQL / SQLite (configurable) |
| Queue | Laravel Queues / Database driver |
| Cache | Laravel Cache / Database driver |
| Type-Safe Routes | Laravel Wayfinder 0 |
| Mobile App | Located in `mobile/` |

---

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/Admin/      # Admin dashboard controllers
│   │   ├── Controllers/Organizer/  # Organizer dashboard controllers
│   │   ├── Controllers/Judge/      # Judge dashboard controllers
│   │   ├── Controllers/Settings/   # Profile and security controllers
│   │   └── Controllers/Api/        # REST API controllers
│   ├── Models/                     # Eloquent models
│   └── ...
├── bootstrap/                      # Application bootstrap files
├── config/                         # Configuration files
├── database/
│   ├── factories/                  # Model factories
│   ├── migrations/                 # Database migrations
│   └── seeders/                    # Database seeders
├── mobile/                         # Mobile application source
├── public/                         # Public assets
├── resources/
│   ├── js/                         # React / Inertia pages and components
│   └── css/                        # Tailwind styles
├── routes/
│   ├── web.php                     # Inertia / web routes
│   └── api.php                     # API routes
├── storage/                        # Logs, uploads, caches
├── tests/                          # Pest / PHPUnit tests
├── .env.example                    # Environment template
├── composer.json
├── package.json
└── vite.config.js
```

---

## Requirements

- PHP 8.5 or higher
- Composer 2.x
- Node.js 20.x or higher
- npm or Yarn
- MySQL 8.0+ / PostgreSQL 14+ / SQLite 3
- Git

---

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/aldernawi/compation-project.git
   cd compation-project
   ```

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Install JavaScript dependencies:

   ```bash
   npm install
   ```

4. Generate Wayfinder typed routes:

   ```bash
   php artisan wayfinder:generate
   ```

---

## Configuration

1. Copy the environment file:

   ```bash
   cp .env.example .env
   ```

2. Generate the application key:

   ```bash
   php artisan key:generate
   ```

3. Update `.env` with your database, mail, FCM, and queue settings:

   ```env
   APP_NAME="Competition Management Platform"
   APP_URL=http://localhost

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=competition
   DB_USERNAME=root
   DB_PASSWORD=

   FCM_SERVER_KEY=your_fcm_key
   ```

4. Build the frontend:

   ```bash
   npm run build
   # or for development
   npm run dev
   ```

---

## Database

Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

For development with demo accounts:

```bash
php artisan db:seed --class=DatabaseSeeder
```

### Core Tables

- `users`
- `competition_types`
- `competitions`
- `prizes`
- `submissions`
- `competition_judge` (pivot)
- `evaluations`
- `notifications`
- `media`
- `passkeys`
- `personal_access_tokens`

---

## Usage

Start the development server:

```bash
php artisan serve
```

Run the Vite dev server in a separate terminal:

```bash
npm run dev
```

Open the application in your browser:

```
http://localhost:8000
```

---

## API

The platform exposes a REST API for mobile and third-party integrations, authenticated via Laravel Sanctum.

### Authentication

- `POST /api/register`
- `POST /api/login`
- `POST /api/logout`
- `POST /api/profile`

### Competitions

- `GET /api/competitions`
- `GET /api/competitions/{competition}`
- `POST /api/competitions/{competition}/register`

### Submissions

- `GET /api/submissions`
- `POST /api/submissions`
- `GET /api/submissions/{submission}`

### Notifications

- `GET /api/notifications`
- `POST /api/notifications/read`

For the complete list of routes, run:

```bash
php artisan route:list
```

---

## User Roles & Permissions

| Role | Capabilities |
|------|--------------|
| **Admin** | Manage users, competitions, types, prizes, reports, and platform settings. |
| **Organizer** | Create and manage competitions, assign judges, view submissions, publish results. |
| **Judge** | Evaluate assigned submissions and view rankings. |
| **Participant** | Register for competitions, submit entries, view results and rankings. |

---

## Mobile Application

A dedicated mobile application is included in the `mobile/` directory. Refer to `mobile/README.md` for setup instructions and platform-specific requirements.

---

## Testing

The project uses Pest for unit and feature testing.

Run the full test suite:

```bash
php artisan test
```

Run a compact test summary:

```bash
php artisan test --compact
```

Run a specific test or filter:

```bash
php artisan test --compact --filter=CompetitionTest
```

Run static analysis with Larastan:

```bash
vendor/bin/phpstan analyse
```

Format PHP code with Pint:

```bash
vendor/bin/pint
```

---

## Deployment

The recommended deployment target is [Laravel Cloud](https://cloud.laravel.com/).

For traditional deployment:

1. Install dependencies on the server:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

2. Set environment variables in `.env` on the server.

3. Run migrations:

   ```bash
   php artisan migrate --force
   ```

4. Optimize the application:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. Configure the web server (Nginx or Apache) to serve the `public/` directory.

---

## Security

- Never commit `.env` or API keys to version control.
- Keep dependencies updated.
- Use strong passwords and enable passkeys where possible.
- Validate and authorize all API requests.
- Store user uploads outside the web root or use Laravel's storage abstraction.

---

## License

This project is open-source and available under the [MIT License](LICENSE).
