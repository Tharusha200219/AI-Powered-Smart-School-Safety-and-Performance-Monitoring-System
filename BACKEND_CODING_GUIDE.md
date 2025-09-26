# Backend Coding Guide - Laravel Smart School System

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Coding Standards](#coding-standards)
3. [Project Structure](#project-structure)
4. [Component Patterns](#component-patterns)
5. [Implementation Guide](#implementation-guide)
6. [Code Examples](#code-examples)
7. [Best Practices](#best-practices)
8. [Common Patterns](#common-patterns)

## Architecture Overview

This Laravel application follows a **Repository-Service Pattern** with a layered architecture:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│    Controllers  │    │    Services     │    │  Repositories   │
│                 │───▶│                 │───▶│                 │
│ Handle Requests │    │ Business Logic  │    │  Data Access    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   DataTables    │    │     Models      │    │   Interfaces    │
│                 │    │                 │    │                 │
│ Data Display    │    │  Eloquent ORM   │    │  Contracts      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Key Components:

- **Controllers**: Handle HTTP requests and responses
- **Services**: Contain business logic and orchestrate operations
- **Repositories**: Abstract data layer operations
- **Models**: Eloquent ORM models representing database entities
- **DataTables**: Handle data grid functionality
- **Helpers**: Utility functions and validation rules

## Coding Standards

### 1. Naming Conventions

#### Controllers

```php
// Pattern: {Entity}Controller extends BaseManagementController
class SecurityStaffController extends BaseManagementController
{
    // Properties use camelCase
    protected string $parentViewPath = 'admin.pages.management.security.';
    protected string $parentRoutePath = 'admin.management.security.';
    protected string $entityName = 'Security Staff';
    protected string $entityType = 'security';
}
```

#### Models

```php
// Pattern: Singular entity name
class SecurityStaff extends Model
{
    // Table name (if different from convention)
    protected $table = 'security_staff';

    // Primary key (if different from 'id')
    protected $primaryKey = 'security_id';

    // Fillable fields
    protected $fillable = [
        'user_id',
        'security_code',
        'first_name',
        // ... other fields
    ];
}
```

#### Repositories

```php
// Pattern: {Entity}Repository implements {Entity}RepositoryInterface
class SecurityStaffRepository implements SecurityStaffRepositoryInterface
{
    protected $model;

    public function __construct(SecurityStaff $model)
    {
        $this->model = $model;
    }
}
```

### 2. Method Naming

- Use descriptive, verb-based names
- Follow Laravel conventions
- Use camelCase for methods

```php
// Good Examples
public function createUserWithRole()
public function getByShift()
public function generateSecurityCode()
public function handleProfileImageUpload()

// Avoid
public function doStuff()
public function handle()
public function process()
```

## Project Structure

```
app/
├── DataTables/              # Data grid handling
│   └── Admin/
│       └── Management/
├── Enums/                   # Enumeration classes
├── Helpers/                 # Utility functions
├── Http/
│   └── Controllers/         # Request handlers
│       └── Admin/
│           ├── BaseManagementController.php
│           └── Management/
├── Models/                  # Eloquent models
├── Repositories/            # Data access layer
│   ├── Admin/
│   └── Interfaces/         # Repository contracts
├── Services/               # Business logic layer
└── Traits/                # Reusable code traits
```

## Component Patterns

### 1. Controller Pattern

All management controllers extend `BaseManagementController`:

```php
class SecurityStaffController extends BaseManagementController
{
    // Required properties
    protected string $parentViewPath = 'admin.pages.management.security.';
    protected string $parentRoutePath = 'admin.management.security.';
    protected string $entityName = 'Security Staff';
    protected string $entityType = 'security';

    // Constructor injection
    public function __construct(
        SecurityStaffRepositoryInterface $repository,
        UserService $userService,
        DatabaseTransactionService $transactionService,
        ImageUploadService $imageService
    ) {
        parent::__construct($repository, $userService, $imageService, $transactionService);
    }

    // Required abstract methods implementation
    protected function getFormData($id = null): array
    protected function getValidationRules(bool $isUpdate = false, $id = null): array
    protected function performCreate(Request $request)
    protected function performUpdate(Request $request, $id)
}
```

### 2. Repository Pattern

#### Interface Definition

```php
interface SecurityStaffRepositoryInterface
{
    public function getAll();
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getWithRelations($id);
}
```

#### Repository Implementation

```php
class SecurityStaffRepository implements SecurityStaffRepositoryInterface
{
    protected $model;

    public function __construct(SecurityStaff $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (!isset($data['security_code'])) {
                $data['security_code'] = $this->generateSecurityCode();
            }
            return $this->model->create($data);
        });
    }
}
```

### 3. Service Pattern

Services handle business logic and coordinate between repositories:

```php
class UserService
{
    public function createUserWithRole(array $userData, UserType $userType, array $roles): User
    {
        return DB::transaction(function () use ($userData, $userType, $roles): User {
            // Create user account
            $user = User::create([
                'name' => $this->buildFullName($userData),
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'usertype' => $userType->value,
                'status' => Status::ACTIVE->value,
            ]);

            // Assign roles to user
            if (!empty($roles)) {
                $user->assignRole($roles);
            }

            return $user;
        });
    }
}
```

### 4. Model Pattern

Models use Laravel Eloquent with proper relationships and accessors:

```php
class SecurityStaff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'security_staff';
    protected $primaryKey = 'security_id';

    protected $fillable = [
        'user_id', 'security_code', 'first_name',
        // ... other fields
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return trim($this->first_name.' '.$this->middle_name.' '.$this->last_name);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Static methods
    public static function generateSecurityCode()
    {
        $year = date('Y');
        $lastSecurity = self::whereYear('joining_date', $year)
            ->orderBy('security_id', 'desc')
            ->first();

        $sequence = $lastSecurity ? (int) substr($lastSecurity->security_code, -4) + 1 : 1;
        return 'SEC'.$year.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
```

## Implementation Guide

### Creating a New Management Entity

#### Step 1: Create the Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewEntity extends Model
{
    use SoftDeletes;

    protected $table = 'new_entities';
    protected $primaryKey = 'entity_id';

    protected $fillable = [
        'user_id',
        'entity_code',
        'first_name',
        'last_name',
        // ... other fields
    ];

    protected $casts = [
        'created_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
```

#### Step 2: Create Repository Interface

```php
<?php

namespace App\Repositories\Interfaces\Admin\Management;

interface NewEntityRepositoryInterface
{
    public function getAll();
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getWithRelations($id);
}
```

#### Step 3: Create Repository Implementation

```php
<?php

namespace App\Repositories\Admin\Management;

use App\Models\NewEntity;
use App\Repositories\Interfaces\Admin\Management\NewEntityRepositoryInterface;

class NewEntityRepository implements NewEntityRepositoryInterface
{
    protected $model;

    public function __construct(NewEntity $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getById($id)
    {
        return $this->model->with(['user'])
            ->where('entity_id', $id)
            ->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $entity = $this->model->where('entity_id', $id)->first();
        if (!$entity) {
            return false;
        }
        return $entity->update($data);
    }

    public function delete($id)
    {
        return $this->model->where('entity_id', $id)->delete();
    }

    public function getWithRelations($id)
    {
        return $this->model->with(['user'])
            ->where('entity_id', $id)
            ->first();
    }
}
```

#### Step 4: Create Controller

```php
<?php

namespace App\Http\Controllers\Admin\Management;

use App\DataTables\Admin\Management\NewEntityDataTable;
use App\Http\Controllers\Admin\BaseManagementController;
use App\Repositories\Interfaces\Admin\Management\NewEntityRepositoryInterface;
use Illuminate\Http\Request;

class NewEntityController extends BaseManagementController
{
    protected string $parentViewPath = 'admin.pages.management.newentity.';
    protected string $parentRoutePath = 'admin.management.newentity.';
    protected string $entityName = 'New Entity';
    protected string $entityType = 'newentity';

    public function __construct(
        NewEntityRepositoryInterface $repository,
        // ... other dependencies
    ) {
        parent::__construct($repository, /* ... other services */);
    }

    public function index(NewEntityDataTable $datatable)
    {
        return $this->renderIndex($datatable, $this->parentViewPath);
    }

    protected function getFormData($id = null): array
    {
        // Return any data needed for the form
        return [];
    }

    protected function getValidationRules(bool $isUpdate = false, $id = null): array
    {
        // Return validation rules
        return [];
    }

    protected function performCreate(Request $request)
    {
        // Implementation for creating entity
    }

    protected function performUpdate(Request $request, $id)
    {
        // Implementation for updating entity
    }
}
```

### Common Operations

#### 1. Creating with User Account

```php
protected function performCreate(Request $request)
{
    // Create user account
    $user = $this->userService->createUserWithRole(
        $request->all(),
        UserType::SECURITY, // or appropriate type
        $request->input('roles', [])
    );

    // Prepare entity data
    $entityData = $request->except(['password', 'password_confirmation', 'roles', 'profile_image']);
    $entityData['user_id'] = $user->id;
    $entityData['is_active'] = true;

    // Handle profile image upload
    $imagePath = $this->handleProfileImageUpload($request);
    if ($imagePath) {
        $entityData['photo_path'] = $imagePath;
    }

    $entity = $this->repository->create($entityData);

    // Create notification
    $this->notifyCreated($this->entityName, $entity);

    return $entity;
}
```

#### 2. Updating with User Account

```php
protected function performUpdate(Request $request, $id)
{
    $entity = $this->repository->getById($id);
    if (!$entity) {
        throw new \Exception($this->entityName . ' not found.');
    }

    // Update user account
    $userData = [
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'email' => $request->input('email'),
    ];

    if ($request->filled('password')) {
        $userData['password'] = $request->input('password');
    }

    $updatedUser = $this->userService->updateUser($entity->user, $userData);

    // Update roles
    if ($request->has('roles')) {
        $this->userService->updateUserRoles($updatedUser, $request->input('roles'));
    }

    // Prepare entity data
    $entityData = $request->except(['password', 'password_confirmation', 'roles', 'profile_image']);

    // Handle profile image upload
    $imagePath = $this->handleProfileImageUpload($request, $entity);
    if ($imagePath) {
        $entityData['photo_path'] = $imagePath;
    }

    $updatedEntity = $this->repository->update($id, $entityData);

    // Create notification
    $this->notifyUpdated($this->entityName, $updatedEntity);

    return $updatedEntity;
}
```

## Code Examples

### 1. DataTable Implementation

```php
class SecurityStaffDataTable extends DataTable
{
    protected $model = 'security';

    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                // Action buttons logic
                return $this->generateActionButtons($row);
            })
            ->addColumn('name', function ($row) {
                return '<div class="d-flex align-items-center">
                    <span class="fw-bold">' . $row->full_name . '</span>
                </div>';
            })
            ->rawColumns(['action', 'name', 'status'])
            ->orderColumn('name', function ($query, $order) {
                return $query->orderBy('first_name', $order);
            });
    }

    public function query(SecurityStaff $model): QueryBuilder
    {
        return $model->with(['user'])
            ->orderBy('created_at', 'desc');
    }
}
```

### 2. Validation Rules Helper

```php
class ValidationRules
{
    public const PERSONAL_NAME_RULES = 'required|min:2|max:50';
    public const EMAIL_RULES = 'required|email|max:255';
    public const PASSWORD_RULES = 'required|min:8|confirmed';

    public static function getSecurityStaffRules(bool $isUpdate = false, ?int $userId = null): array
    {
        $rules = self::getPersonRules($isUpdate, $userId);

        return array_merge($rules, [
            'joining_date' => 'required|date',
            'employee_id' => 'nullable|max:50',
            'shift' => 'required|in:Morning,Afternoon,Night',
            'position' => 'required|max:100',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);
    }
}
```

### 3. Service Dependency Injection

```php
// In RepositoryServiceProvider.php
public function register()
{
    $this->app->bind(
        SecurityStaffRepositoryInterface::class,
        SecurityStaffRepository::class
    );
}
```

## Best Practices

### 1. Use Type Hints

Always use type hints for parameters and return types:

```php
public function createUserWithRole(array $userData, UserType $userType, array $roles): User
{
    // Implementation
}
```

### 2. Database Transactions

Wrap complex operations in database transactions:

```php
public function create(array $data)
{
    return DB::transaction(function () use ($data) {
        // Multiple database operations
        return $this->model->create($data);
    });
}
```

### 3. Validation

Use centralized validation rules:

```php
protected function getValidationRules(bool $isUpdate = false, $id = null): array
{
    return ValidationRules::getSecurityStaffRules($isUpdate, $id);
}
```

### 4. Error Handling

Use proper exception handling:

```php
protected function performUpdate(Request $request, $id)
{
    $entity = $this->repository->getById($id);
    if (!$entity) {
        throw new \Exception($this->entityName . ' not found.');
    }
    // Continue with update logic
}
```

### 5. Consistent Naming

- Controllers: `{Entity}Controller`
- Models: Singular entity name
- Repositories: `{Entity}Repository`
- Services: `{Entity}Service` or descriptive name
- DataTables: `{Entity}DataTable`

### 6. Use Enums

Define constants using enums:

```php
enum UserType: string
{
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case PARENT = 'parent';
    case SECURITY = 'security';
}
```

## Common Patterns

### 1. Repository Registration

```php
// In RepositoryServiceProvider
$repositories = [
    SecurityStaffRepositoryInterface::class => SecurityStaffRepository::class,
    StudentRepositoryInterface::class => StudentRepository::class,
    // ... more repositories
];

foreach ($repositories as $interface => $implementation) {
    $this->app->bind($interface, $implementation);
}
```

### 2. Notification Pattern

```php
// Using CreatesNotifications trait
protected function performCreate(Request $request)
{
    $entity = $this->repository->create($data);

    // Create notification
    $this->notifyCreated($this->entityName, $entity);

    return $entity;
}
```

### 3. Image Upload Pattern

```php
protected function handleProfileImageUpload(Request $request, $entity = null): ?string
{
    if (!$request->hasFile('profile_image') || !$this->imageService) {
        return null;
    }

    $oldImagePath = $entity?->photo_path ?? null;
    $userId = $entity?->user_id ?? time();

    return $this->imageService->uploadProfileImage(
        $request->file('profile_image'),
        $this->entityType,
        $userId,
        $oldImagePath
    );
}
```

### 4. Permission Checking Pattern

```php
public function index(SecurityStaffDataTable $datatable)
{
    checkPermissionAndRedirect($this->getPermissionKey('index'));
    // Continue with logic
}
```

This guide provides a comprehensive overview of how to code in this Laravel backend system. Follow these patterns and standards to maintain consistency and quality across the codebase.
