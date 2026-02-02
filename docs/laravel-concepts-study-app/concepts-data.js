// Concepts Data - Embedded to avoid CORS issues when opening HTML directly
const CONCEPTS_DATA = {
  "concepts": [
    {
      "id": "routing",
      "title": "🚦 Routing",
      "icon": "🚦",
      "description": "Routing defines the URLs and which controller methods handle requests. In Laravel, routes map HTTP requests to controller actions.",
      "sections": [
        {
          "title": "What is Routing?",
          "content": "Routing is how Laravel knows what code to run when someone visits a URL. Think of it as a map that says 'when someone goes to /employee/dashboard, run the dashboard() method in EmployeeController'.",
          "expanded": true,
          "codeExample": {
            "file": "routes/employee.php",
            "code": "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\nuse App\\Http\\Controllers\\EmployeeController;\n\nRoute::middleware(['auth', 'role:employee'])->group(function () {\n    Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])\n        ->name('employee.dashboard');\n    Route::post('/employee/request', [EmployeeController::class, 'storeRequest'])\n        ->name('employee.store-request');\n    Route::patch('/request/{id}/cancel', [EmployeeController::class, 'cancelRequest'])\n        ->name('request.cancel');\n});"
          },
          "explanation": "This code creates routes that only employees can access. The `GET` route shows the dashboard, `POST` creates a request, and `PATCH` cancels one. The `{id}` is a route parameter - a variable part of the URL."
        },
        {
          "title": "Route Files Organization",
          "content": "Routes are split into multiple files for organization. The main web.php loads all route files.",
          "expanded": false,
          "codeExample": {
            "file": "routes/web.php",
            "code": "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n// Load all route files\nrequire __DIR__.'/auth.php';\nrequire __DIR__.'/employee.php';\nrequire __DIR__.'/manager.php';\nrequire __DIR__.'/hr.php';"
          },
          "explanation": "This keeps routes organized by role. Each file handles routes for one user type (employee, manager, HR)."
        },
        {
          "title": "Named Routes",
          "content": "Routes can have names, making them easy to reference in views and controllers.",
          "expanded": false,
          "usage": [
            {
              "location": "In Views",
              "description": "Use route('employee.dashboard') instead of hardcoding '/employee/dashboard'"
            },
            {
              "location": "In Controllers",
              "description": "redirect()->route('employee.dashboard') - easier to maintain if URL changes"
            }
          ]
        }
      ]
    },
    {
      "id": "middleware",
      "title": "🛡️ Middleware",
      "icon": "🛡️",
      "description": "Middleware runs before requests reach your controllers. It's perfect for authentication, authorization, and other checks.",
      "sections": [
        {
          "title": "What is Middleware?",
          "content": "Middleware is like a security guard. It checks requests before they reach your controller. If the check fails, the request is stopped or redirected.",
          "expanded": true,
          "codeExample": {
            "file": "app/Http/Middleware/RoleMiddleware.php",
            "code": "<?php\n\nnamespace App\\Http\\Middleware;\n\nuse Closure;\nuse Illuminate\\Http\\Request;\nuse Illuminate\\Support\\Facades\\Auth;\n\nclass RoleMiddleware\n{\n    public function handle(Request $request, Closure $next, ...$roles)\n    {\n        // Check if user is authenticated\n        if (!Auth::check()) {\n            return redirect()->route('login');\n        }\n\n        $user = Auth::user();\n\n        // Check if user has required role\n        if (!in_array($user->role, $roles)) {\n            abort(403, 'Unauthorized access.');\n        }\n\n        return $next($request);\n    }\n}"
          },
          "explanation": "This middleware checks: 1) Is the user logged in? 2) Does the user have the right role? If both pass, `$next($request)` continues to the controller. Otherwise, redirect or block."
        },
        {
          "title": "Built-in Middleware",
          "content": "Laravel comes with built-in middleware like 'auth' (checks login) and 'guest' (checks not logged in).",
          "expanded": false,
          "usage": [
            {
              "location": "routes/auth.php",
              "description": "Guest middleware prevents logged-in users from seeing login page"
            },
            {
              "location": "All role routes",
              "description": "Auth middleware ensures only logged-in users access protected pages"
            }
          ]
        },
        {
          "title": "Middleware Registration",
          "content": "Custom middleware must be registered in bootstrap/app.php",
          "expanded": false,
          "codeExample": {
            "file": "bootstrap/app.php",
            "code": "$middleware->alias([\n    'role' => \\App\\Http\\Middleware\\RoleMiddleware::class,\n]);"
          },
          "explanation": "This creates the 'role' alias so you can use `middleware(['auth', 'role:employee'])` in routes."
        }
      ]
    },
    {
      "id": "controllers",
      "title": "🎮 Controllers",
      "icon": "🎮",
      "description": "Controllers handle HTTP requests. They contain the business logic and decide what view to show or what response to send.",
      "sections": [
        {
          "title": "What is a Controller?",
          "content": "Controllers are classes with methods that handle requests. Each method typically corresponds to one action (like 'show dashboard' or 'create request').",
          "expanded": true,
          "codeExample": {
            "file": "app/Http/Controllers/EmployeeController.php",
            "code": "class EmployeeController extends Controller\n{\n    public function dashboard()\n    {\n        $user = Auth::user();\n        $currentYear = now()->year;\n\n        // Fetch user's leave balances for this year\n        $balances = $user->leaveBalances()\n            ->where('year', $currentYear)\n            ->orderBy('leave_type')\n            ->get();\n\n        $requests = $user->leaveRequests()\n            ->orderBy('created_at', 'desc')\n            ->paginate(10);\n\n        return view('employee.dashboard', compact('balances', 'requests', 'user'));\n    }\n}"
          },
          "explanation": "This controller method: 1) Gets the logged-in user, 2) Fetches data from database, 3) Returns a view with that data. The 'compact()' function creates an array with variables to pass to the view."
        },
        {
          "title": "Request Validation",
          "content": "Controllers validate incoming data before processing it.",
          "expanded": false,
          "codeExample": {
            "file": "app/Http/Controllers/EmployeeController.php",
            "code": "public function storeRequest(Request $request)\n{\n    $validated = $request->validate([\n        'leave_type' => 'required|string',\n        'start_date' => 'required|date|after_or_equal:today',\n        'end_date' => 'required|date|after_or_equal:start_date',\n        'reason' => 'required|string|max:1000',\n    ]);\n    // ... rest of method\n}"
          },
          "explanation": "If validation fails, Laravel automatically redirects back with errors. If it passes, you get clean $validated data."
        },
        {
          "title": "Redirects and Flash Messages",
          "content": "After an action, controllers often redirect with success/error messages.",
          "expanded": false,
          "usage": [
            {
              "location": "EmployeeController@storeRequest",
              "description": "redirect()->route('employee.dashboard')->with('success', 'Leave request submitted successfully.')"
            },
            {
              "location": "All controllers",
              "description": "Flash messages show once then disappear - perfect for confirmations"
            }
          ]
        }
      ]
    },
    {
      "id": "eloquent-models",
      "title": "📊 Eloquent Models",
      "icon": "📊",
      "description": "Models represent database tables. They let you query and manipulate data using PHP instead of SQL.",
      "sections": [
        {
          "title": "What is a Model?",
          "content": "A model is a PHP class that represents a database table. Instead of writing SQL, you use methods like `User::find(1)` or `$user->leaveRequests()`.",
          "expanded": true,
          "codeExample": {
            "file": "app/Models/User.php",
            "code": "class User extends Authenticatable\n{\n    protected $fillable = [\n        'name',\n        'email',\n        'password',\n        'role',\n        'department_id',\n        'manager_id',\n    ];\n\n    public function leaveRequests()\n    {\n        return $this->hasMany(Request::class, 'employee_id');\n    }\n\n    public function leaveBalances()\n    {\n        return $this->hasMany(Balance::class);\n    }\n}"
          },
          "explanation": "$fillable specifies which fields can be mass-assigned (filled in one go). The methods define relationships - a User has many leaveRequests and leaveBalances."
        },
        {
          "title": "Eloquent Relationships",
          "content": "Relationships define how models connect (User has many Requests, Request belongs to User).",
          "expanded": false,
          "visualDiagram": {
            "steps": ["User", "hasMany", "Request", "belongsTo", "User"]
          },
          "usage": [
            {
              "location": "User Model",
              "description": "hasMany(Request), hasMany(Balance), belongsTo(Department)"
            },
            {
              "location": "Request Model",
              "description": "belongsTo(User as employee), belongsTo(User as departmentManager)"
            }
          ]
        },
        {
          "title": "Query Scopes",
          "content": "Scopes are reusable query filters that make code cleaner.",
          "expanded": false,
          "codeExample": {
            "file": "app/Models/Request.php",
            "code": "public function scopePending(Builder $query): Builder\n{\n    return $query->where('status', 'pending');\n}\n\n// Usage: LeaveRequest::pending()->get()\n// Instead of: LeaveRequest::where('status', 'pending')->get()"
          },
          "explanation": "Scopes let you write `pending()` instead of repeating `where('status', 'pending')` everywhere."
        },
        {
          "title": "Attribute Casting",
          "content": "Casting automatically converts database values (strings) to PHP types (dates, booleans).",
          "expanded": false,
          "codeExample": {
            "file": "app/Models/Request.php",
            "code": "protected function casts(): array\n{\n    return [\n        'start_date' => 'date',\n        'end_date' => 'date',\n        'approved_by_dept_at' => 'datetime',\n    ];\n}"
          },
          "explanation": "When you get start_date from database, Laravel automatically converts it to a Carbon date object. No manual parsing needed!"
        }
      ]
    },
    {
      "id": "database-migrations",
      "title": "🗄️ Database Migrations",
      "icon": "🗄️",
      "description": "Migrations are like version control for your database. They define table structure and can be run to create/update tables.",
      "sections": [
        {
          "title": "What are Migrations?",
          "content": "Migrations are PHP files that describe your database structure. Instead of manually creating tables, you write migration code that Laravel runs.",
          "expanded": true,
          "info": "Migrations live in database/migrations/ and are run with: php artisan migrate"
        },
        {
          "title": "Migration Files in This Project",
          "content": "This project has migrations for: users, departments, requests (leave requests), balances, policies, cache, and jobs tables.",
          "expanded": false,
          "usage": [
            {
              "location": "database/migrations/0001_01_01_000000_create_users_table.php",
              "description": "Creates users table with email, password, role, department_id fields"
            },
            {
              "location": "database/migrations/2026_01_11_030045_create_requests_table.php",
              "description": "Creates requests table for leave requests with status, dates, approvals"
            }
          ]
        }
      ]
    },
    {
      "id": "authentication",
      "title": "🔐 Authentication",
      "icon": "🔐",
      "description": "Authentication handles user login/logout. Laravel provides built-in auth with the Auth facade.",
      "sections": [
        {
          "title": "The Auth Facade",
          "content": "Auth:: provides methods to check login status, get the current user, attempt login, and logout.",
          "expanded": true,
          "codeExample": {
            "file": "app/Http/Controllers/AuthController.php",
            "code": "public function login(Request $request)\n{\n    $request->validate([\n        'email' => 'required|email',\n        'password' => 'required',\n    ]);\n\n    $credentials = [\n        'email' => trim($request->input('email')),\n        'password' => $request->input('password'),\n    ];\n    \n    if (!Auth::attempt($credentials, $request->boolean('remember'))) {\n        throw ValidationException::withMessages([\n            'email' => ['The provided credentials are incorrect.'],\n        ]);\n    }\n\n    $request->session()->regenerate();\n    $user = Auth::user();\n\n    return match($user->role) {\n        'employee' => redirect()->route('employee.dashboard'),\n        'dept_manager' => redirect()->route('manager.dashboard'),\n        'hr_admin', 'ceo' => redirect()->route('hr.dashboard'),\n    };\n}"
          },
          "explanation": "Auth::attempt() tries to log in. If successful, session is regenerated (security), user is fetched with Auth::user(), then redirected based on role."
        },
        {
          "title": "Session Management",
          "content": "Laravel manages sessions automatically. regenerate() creates a new session ID after login (prevents session hijacking).",
          "expanded": false,
          "usage": [
            {
              "location": "AuthController@login",
              "description": "$request->session()->regenerate() - security best practice"
            },
            {
              "location": "AuthController@logout",
              "description": "session()->invalidate() - destroys session on logout"
            }
          ]
        }
      ]
    },
    {
      "id": "validation",
      "title": "✅ Validation",
      "icon": "✅",
      "description": "Validation ensures incoming data is correct before it's used. Laravel validates and returns errors automatically.",
      "sections": [
        {
          "title": "Form Request Validation",
          "content": "Validate data in controllers using $request->validate(). If validation fails, user is redirected back with errors.",
          "expanded": true,
          "codeExample": {
            "file": "app/Http/Controllers/EmployeeController.php",
            "code": "$validated = $request->validate([\n    'leave_type' => 'required|string',\n    'start_date' => 'required|date|after_or_equal:today',\n    'end_date' => 'required|date|after_or_equal:start_date',\n    'reason' => 'required|string|max:1000',\n]);"
          },
          "explanation": "Rules like 'required' (must exist), 'date' (must be date), 'after_or_equal:today' (can't be in past), 'max:1000' (character limit). All checked automatically!"
        },
        {
          "title": "Common Validation Rules",
          "content": "Laravel has many built-in rules.",
          "expanded": false,
          "usage": [
            {
              "location": "HRController@storeEmployee",
              "description": "'email' => 'required|email|unique:users,email' - must be valid and not duplicate"
            },
            {
              "location": "HRController@updatePolicy",
              "description": "'annual_entitlement' => 'required|integer|min:0|max:365' - must be number between 0-365"
            }
          ]
        }
      ]
    },
    {
      "id": "blade-templating",
      "title": "🪶 Blade Templating",
      "icon": "🪶",
      "description": "Blade is Laravel's templating engine. It lets you write HTML with PHP variables and special directives.",
      "sections": [
        {
          "title": "What is Blade?",
          "content": "Blade files (.blade.php) are HTML templates with PHP mixed in. Laravel compiles them to regular PHP.",
          "expanded": true,
          "codeExample": {
            "file": "resources/views/employee/dashboard.blade.php",
            "code": "<x-layout>\n    <h1>Welcome, {{ $user->name }}!</h1>\n    \n    @if($balances->isEmpty())\n        <p>No leave balances found.</p>\n    @else\n        @foreach($balances as $balance)\n            <x-balance-card :balance=\"$balance\" />\n        @endforeach\n    @endif\n</x-layout>"
          },
          "explanation": "{{ }} outputs variables. @if/@else/@endif are Blade conditionals. <x-balance-card> is a component. Variables come from controller's compact() or with()."
        },
        {
          "title": "Blade Directives",
          "content": "Blade has special @ directives for common PHP patterns.",
          "expanded": false,
          "usage": [
            {
              "location": "All views",
              "description": "@if, @else, @endif - conditionals. @foreach, @endforeach - loops. @isset, @endisset - check if exists"
            },
            {
              "location": "components/layout.blade.php",
              "description": "@isset($navigation) checks if variable exists before displaying"
            }
          ]
        }
      ]
    },
    {
      "id": "blade-components",
      "title": "🔧 Blade Components",
      "icon": "🔧",
      "description": "Components are reusable Blade templates. They accept props (data) and can have slots (flexible content areas).",
      "sections": [
        {
          "title": "What are Components?",
          "content": "Components are like functions for views. You pass data to them and they render HTML. This keeps views DRY (Don't Repeat Yourself).",
          "expanded": true,
          "codeExample": {
            "file": "resources/views/components/balance-card.blade.php",
            "code": "@props(['balance'])\n\n@php\n    $available = $balance->getAvailableBalance();\n    $total = $balance->balance;\n    $percentage = $total > 0 ? ($available / $total) * 100 : 0;\n@endphp\n\n<div class=\"card\">\n    <h3>{{ $balance->leave_type }}</h3>\n    <p>{{ number_format($available, 1) }} / {{ number_format($total, 1) }}</p>\n</div>"
          },
          "explanation": "@props(['balance']) defines what data the component accepts. Usage: <x-balance-card :balance=\"$balance\" /> - the : passes the variable as a prop."
        },
        {
          "title": "Layout Component with Slots",
          "content": "The layout component uses slots for flexible content.",
          "expanded": false,
          "codeExample": {
            "file": "resources/views/components/layout.blade.php",
            "code": "<body>\n    @isset($navigation)\n        {{ $navigation }}\n    @endisset\n\n    <main>\n        {{ $slot }}\n    </main>\n</body>"
          },
          "explanation": "{{ $slot }} is where the content goes when you use <x-layout>content here</x-layout>. Optional slots like $navigation can be named."
        },
        {
          "title": "Component Props",
          "content": "Props are how you pass data to components. Use :prop for variables, prop=\"value\" for strings.",
          "expanded": false,
          "usage": [
            {
              "location": "balance-card component",
              "description": "@props(['balance']) - accepts one prop"
            },
            {
              "location": "status-badge component",
              "description": "@props(['status']) - accepts status string"
            }
          ]
        }
      ]
    },
    {
      "id": "eloquent-relationships",
      "title": "🔗 Eloquent Relationships",
      "icon": "🔗",
      "description": "Relationships define how models connect. hasMany, belongsTo, hasOne are the most common.",
      "sections": [
        {
          "title": "Understanding Relationships",
          "content": "Relationships connect models. A User hasMany Requests. A Request belongsTo User. This lets you access related data easily.",
          "expanded": true,
          "visualDiagram": {
            "steps": ["User (1)", "hasMany", "Request (many)", "belongsTo", "User (1)"]
          },
          "codeExample": {
            "file": "app/Models/User.php",
            "code": "// User has many leave requests\npublic function leaveRequests()\n{\n    return $this->hasMany(Request::class, 'employee_id');\n}\n\n// Usage: $user->leaveRequests()->get()\n// Gets all requests where employee_id = $user->id"
          },
          "explanation": "hasMany means one User can have many Requests. The foreign key is 'employee_id' (in requests table)."
        },
        {
          "title": "Eager Loading",
          "content": "Eager loading prevents N+1 queries (loading related data efficiently).",
          "expanded": false,
          "codeExample": {
            "file": "app/Http/Controllers/ManagerController.php",
            "code": "$pendingRequests = LeaveRequest::whereIn('employee_id', $employeeIds)\n    ->pending()\n    ->with('employee')\n    ->orderBy('created_at', 'desc')\n    ->get();"
          },
          "explanation": "->with('employee') loads employee data in one query instead of one per request. Much faster!"
        },
        {
          "title": "Relationship Types in Project",
          "content": "This project uses hasMany, belongsTo relationships.",
          "expanded": false,
          "usage": [
            {
              "location": "User Model",
              "description": "hasMany(Request), hasMany(Balance), belongsTo(Department), belongsTo(User as manager)"
            },
            {
              "location": "Request Model",
              "description": "belongsTo(User as employee), belongsTo(User as departmentManager), belongsTo(User as hrApprover)"
            },
            {
              "location": "Department Model",
              "description": "belongsTo(User as manager), hasMany(User as employees)"
            }
          ]
        }
      ]
    },
    {
      "id": "query-builder",
      "title": "🔍 Query Builder",
      "icon": "🔍",
      "description": "Query Builder lets you build database queries using a fluent interface. Much easier than raw SQL!",
      "sections": [
        {
          "title": "What is Query Builder?",
          "content": "Query Builder provides methods to build SQL queries. You chain methods like where(), orderBy(), get() instead of writing SQL.",
          "expanded": true,
          "codeExample": {
            "file": "app/Http/Controllers/EmployeeController.php",
            "code": "$balances = $user->leaveBalances()\n    ->where('year', $currentYear)\n    ->orderBy('leave_type')\n    ->get();\n\n// Equivalent SQL:\n// SELECT * FROM balances WHERE user_id = ? AND year = ? ORDER BY leave_type"
          },
          "explanation": "Method chaining makes queries readable. ->where() filters, ->orderBy() sorts, ->get() executes and returns results."
        },
        {
          "title": "Common Query Methods",
          "content": "where(), whereIn(), orderBy(), paginate(), with() are frequently used.",
          "expanded": false,
          "usage": [
            {
              "location": "ManagerController@dashboard",
              "description": "whereIn('employee_id', $employeeIds) - get requests for multiple employees"
            },
            {
              "location": "HRController@dashboard",
              "description": "whereIn('status', ['pending', 'dept_manager_approved']) - multiple status filter"
            },
            {
              "location": "All controllers",
              "description": "->paginate(10) - splits results into pages (prevents loading 1000s of records)"
            }
          ]
        }
      ]
    },
    {
      "id": "pagination",
      "title": "📄 Pagination",
      "icon": "📄",
      "description": "Pagination splits large result sets into pages. Laravel handles all the logic automatically.",
      "sections": [
        {
          "title": "What is Pagination?",
          "content": "Instead of showing all 1000 requests on one page, pagination shows 10-15 at a time with Next/Previous buttons.",
          "expanded": true,
          "codeExample": {
            "file": "app/Http/Controllers/EmployeeController.php",
            "code": "$requests = $user->leaveRequests()\n    ->orderBy('created_at', 'desc')\n    ->paginate(10);\n\n// In view:\n// {{ $requests->links() }} - renders pagination buttons"
          },
          "explanation": "paginate(10) returns 10 records per page. Laravel automatically handles page numbers, links, and navigation."
        },
        {
          "title": "Using Pagination in Views",
          "content": "In Blade templates, use {{ $requests->links() }} to display pagination controls.",
          "expanded": false,
          "usage": [
            {
              "location": "Employee dashboard",
              "description": "Shows 10 requests per page with pagination controls"
            },
            {
              "location": "Manager dashboard",
              "description": "Shows 10 pending requests per page"
            },
            {
              "location": "HR dashboard",
              "description": "Shows 15 requests per page"
            }
          ]
        }
      ]
    },
    {
      "id": "facades",
      "title": "🏛️ Facades",
      "icon": "🏛️",
      "description": "Facades provide static access to Laravel services. Auth::, Route::, DB:: are facades.",
      "sections": [
        {
          "title": "What are Facades?",
          "content": "Facades look like static classes but actually resolve services from Laravel's container. They make code cleaner.",
          "expanded": true,
          "codeExample": {
            "file": "app/Http/Controllers/EmployeeController.php",
            "code": "use Illuminate\\Support\\Facades\\Auth;\n\n// Instead of:\n// $auth = app('auth');\n// $auth->user();\n\n// You write:\n$user = Auth::user();"
          },
          "explanation": "Auth:: is a facade. Behind the scenes, Laravel resolves the auth service. But you write clean static syntax."
        },
        {
          "title": "Common Facades in Project",
          "content": "Auth, Route, Hash, DB are used throughout.",
          "expanded": false,
          "usage": [
            {
              "location": "All controllers",
              "description": "Auth::user() - get current user, Auth::check() - check if logged in"
            },
            {
              "location": "HRController@storeEmployee",
              "description": "Hash::make($password) - hash password before saving"
            },
            {
              "location": "routes/*.php",
              "description": "Route::get(), Route::post() - define routes"
            }
          ]
        }
      ]
    },
    {
      "id": "mass-assignment",
      "title": "📦 Mass Assignment",
      "icon": "📦",
      "description": "Mass assignment lets you create/update models by passing an array. The $fillable property controls which fields are allowed.",
      "sections": [
        {
          "title": "What is Mass Assignment?",
          "content": "Instead of setting each field one by one, you pass an array to create() or update(). $fillable specifies which fields are safe.",
          "expanded": true,
          "codeExample": {
            "file": "app/Models/User.php",
            "code": "protected $fillable = [\n    'name',\n    'email',\n    'password',\n    'role',\n    'department_id',\n    'manager_id',\n];\n\n// Usage:\nUser::create([\n    'name' => 'John',\n    'email' => 'john@example.com',\n    // ... other fields\n]);"
          },
          "explanation": "$fillable is a whitelist. Only these fields can be mass-assigned. This prevents users from setting protected fields (like is_admin)."
        },
        {
          "title": "Using Mass Assignment",
          "content": "Model::create() and $model->update() use mass assignment.",
          "expanded": false,
          "usage": [
            {
              "location": "EmployeeController@storeRequest",
              "description": "LeaveRequest::create([...]) - creates request with array of data"
            },
            {
              "location": "HRController@storeEmployee",
              "description": "User::create($validated) - creates user from validated form data"
            }
          ]
        }
      ]
    },
    {
      "id": "flash-messages",
      "title": "💬 Flash Messages",
      "icon": "💬",
      "description": "Flash messages show success/error notifications that appear once then disappear. Perfect for feedback after actions.",
      "sections": [
        {
          "title": "What are Flash Messages?",
          "content": "Flash messages are session data that shows once then is removed. Perfect for 'Request submitted successfully!' messages.",
          "expanded": true,
          "codeExample": {
            "file": "app/Http/Controllers/EmployeeController.php",
            "code": "return redirect()\n    ->route('employee.dashboard')\n    ->with('success', 'Leave request submitted successfully.');\n\n// In view:\n// @if(session('success'))\n//     <div class=\"alert alert-success\">{{ session('success') }}</div>\n// @endif"
          },
          "explanation": "->with('success', ...) stores the message in session. It's displayed once, then Laravel removes it. No manual cleanup needed!"
        },
        {
          "title": "Using Flash Messages",
          "content": "Flash messages work with redirects. They're perfect for POST-after-GET pattern (redirect after form submission).",
          "expanded": false,
          "usage": [
            {
              "location": "All controllers",
              "description": "->with('success', ...) for successful actions, ->withErrors([...]) for validation errors"
            },
            {
              "location": "All views",
              "description": "Check session('success') or session('error') to display messages"
            }
          ]
        }
      ]
    },
    {
      "id": "form-requests",
      "title": "📝 Form Requests",
      "icon": "📝",
      "description": "Form Requests are classes that handle validation and authorization. They keep controllers clean by moving validation logic out.",
      "sections": [
        {
          "title": "Note: Not Used in This Project",
          "content": "This project validates directly in controllers using $request->validate(). Form Request classes would be an improvement for larger apps.",
          "expanded": true,
          "info": "For this project, validation happens in controllers. Form Requests are a Laravel feature that would make the code even cleaner, but aren't required."
        }
      ]
    }
  ]
};