# Fitness Tracker

personalfitness-cs519 is a fitness tracking and management application.

![PHP](https://img.shields.io/badge/PHP-2563EB?style=flat-square) ![MySQL](https://img.shields.io/badge/MySQL-0F172A?style=flat-square)

## Overview

The data model covers `admins`, `daily_meals`, `daily_water_intake`, `daily_workouts`, `feedback`, `fitness_tips`, `public_routines`, `trainer_responses`, `users`.

## Application areas

| Area | Pages in the repository |
| --- | --- |
| Admin | [`admin/add_fitness_tip.php`](admin/add_fitness_tip.php), [`admin/add_public_routine.php`](admin/add_public_routine.php), [`admin/add_users.php`](admin/add_users.php), [`admin/admin_home.php`](admin/admin_home.php), [`admin/admin_login.php`](admin/admin_login.php) |
| Root Pages | [`login.php`](login.php), [`register.php`](register.php) |
| Trainer | [`trainer/add_public_routine.php`](trainer/add_public_routine.php), [`trainer/dashboard.php`](trainer/dashboard.php), [`trainer/edit_public_routine.php`](trainer/edit_public_routine.php), [`trainer/view_public_routines.php`](trainer/view_public_routines.php), [`trainer/view_user_logs.php`](trainer/view_user_logs.php) |
| User | [`user/add_meal.php`](user/add_meal.php), [`user/add_water.php`](user/add_water.php), [`user/add_workout.php`](user/add_workout.php), [`user/dashboard.php`](user/dashboard.php), [`user/edit_meal.php`](user/edit_meal.php) |

## Getting started

### 1. Get the project

```bash
git clone https://github.com/nasirabbas827/personalfitness-cs519.git
cd personalfitness-cs519
```

### 2. Configure PHP and the database

1. Use a local PHP server with MySQL or MariaDB, such as XAMPP.
2. Import [`Database/fitness-cs519db.sql`](Database/fitness-cs519db.sql) into your local database server.
3. Set your local database connection details in [`admin/config.php`](admin/config.php), [`config.php`](config.php), [`trainer/config.php`](trainer/config.php), [`user/config.php`](user/config.php).
4. Keep the existing asset and upload directory paths intact.

### 3. Start the application

With the database running, start a local development server from the repository root:

```bash
php -S 127.0.0.1:8000
```

Open http://127.0.0.1:8000/index.php. If using Apache/XAMPP instead, place the project in its document root and open the matching localhost folder.

## Repository guide

| Path | Purpose |
| --- | --- |
| [`Database`](Database) | Database scripts |
| [`config.php`](config.php) | Application/database configuration |
| [`css`](css) | Stylesheets |
| [`images`](images) | Image assets |
| [`index.php`](index.php) | Web entry page |

## Development notes

These setup instructions are based on the repository source. The application has not been executed as part of this documentation update; verify dependencies and configuration in your local environment.
