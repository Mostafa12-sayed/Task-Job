# Laravel Task Manager API

A simple RESTful API built with Laravel to manage tasks. This API allows users to create, read, update, and delete tasks, with authentication handled by Laravel Sanctum.

## Project Overview

This Task Manager API provides endpoints to manage tasks with different statuses (pending, in-progress, completed) and associates them with authenticated users.

## Features

- RESTful API architecture
- Task management (CRUD operations)
- User authentication with Laravel Sanctum
- Input validation
- Resource pagination

## Requirements

- PHP >= 8.0
- Composer
- MySQL
- Laravel 10.x

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/laravel-task-manager.git
   cd laravel-task-manager
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_manager
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Generate an application key:
   ```bash
   php artisan key:generate
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

## Database Structure

### Tasks Table

The application has a `tasks` table with the following structure:

| Column | Type | Description |
|--------|------|-------------|
| id | BigInteger | Primary key, auto-increments |
| title | String | Task title (required) |
| status | Enum | Task status: 'pending', 'in-progress', 'completed' |
| user_id | BigInteger | Foreign key to users table |
| created_at | Timestamp | When the task was created |
| updated_at | Timestamp | When the task was last updated |

## API Endpoints

All API routes are prefixed with `/api`.

### Authentication Endpoints

| Method | URI | Description |
|--------|-----|-------------|
| POST | `/register` | Register a new user |
| POST | `/login` | Log in a user and get a token |
| POST | `/logout` | Log out a user (revoke token) |

### Task Endpoints

All task endpoints require authentication.

| Method | URI | Action | Description |
|--------|-----|--------|-------------|
| GET | `/tasks` | index | Get all tasks for the authenticated user |
| POST | `/tasks` | store | Create a new task |
| GET | `/tasks/{id}` | show | Get a specific task |
| PUT | `/tasks/{id}` | update | Update a specific task |
| DELETE | `/tasks/{id}` | destroy | Delete a specific task |

