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
- MySQL/PostgreSQL
- Laravel 9.x/10.x

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

## Implementation Details

### Models

#### Task Model (`app/Models/Task.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Migrations

#### Create Tasks Table (`database/migrations/xxxx_xx_xx_create_tasks_table.php`)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('status', ['pending', 'in-progress', 'completed'])->default('pending');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
```

### Controllers

#### TaskController (`app/Http/Controllers/TaskController.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())->paginate(10);
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,in-progress,completed',
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'status' => $validated['status'],
            'user_id' => Auth::id(),
        ]);

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:pending,in-progress,completed',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        $task->delete();

        return response()->json(null, 204);
    }
}
```

### Routes

#### API Routes (`routes/api.php`)

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Task routes
    Route::apiResource('tasks', TaskController::class);
});
```

### Authentication Controller

#### AuthController (`app/Http/Controllers/AuthController.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
```

## Usage Examples

### Authentication

#### Register a new user

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'
```

#### Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
```

#### Using the authentication token

```bash
# Replace YOUR_TOKEN with the token received from login or register
curl -X GET http://localhost:8000/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Task Management

#### Get all tasks

```bash
curl -X GET http://localhost:8000/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

#### Create a task

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title":"Complete README","status":"pending"}'
```

#### Update a task

```bash
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status":"completed"}'
```

#### Delete a task

```bash
curl -X DELETE http://localhost:8000/api/tasks/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

## Testing

Run the tests with PHPUnit:

```bash
php artisan test
```

## Security

- This API is secured with Laravel Sanctum for token-based authentication
- Users can only access their own tasks
- Input validation is implemented to prevent malicious data

## License

The Laravel Task Manager API is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
