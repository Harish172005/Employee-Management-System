# Role-Based Access Control (RBAC) Implementation

This document explains the role-based access control system implemented in the Employee Management System.

## Overview

The system has two user roles:
- **admin** - Full access to manage employees, settings, and system configuration
- **employee** - Limited access to view own profile and information

## Backend Implementation

### 1. AuthMiddleware (middlewares/AuthMiddleware.php)

The middleware provides role-based access control for backend routes.

#### Methods:

- **AuthMiddleware::requireLogin()** - Check if user is logged in
  ```php
  AuthMiddleware::requireLogin();
  ```

- **AuthMiddleware::requireRole($role)** - Require specific role(s)
  ```php
  AuthMiddleware::requireRole('admin');
  AuthMiddleware::requireRole(['admin', 'manager']);
  ```

- **AuthMiddleware::requireAdmin()** - Shortcut for admin role
  ```php
  AuthMiddleware::requireAdmin();
  ```

- **AuthMiddleware::requireEmployee()** - Shortcut for employee role
  ```php
  AuthMiddleware::requireEmployee();
  ```

- **AuthMiddleware::getCurrentUser()** - Get current user info
  ```php
  $user = AuthMiddleware::getCurrentUser();
  // Returns: ['id', 'name', 'username', 'email', 'role', 'status']
  ```

- **AuthMiddleware::requireActive()** - Check if user account is active
  ```php
  AuthMiddleware::requireActive();
  ```

#### Example Backend Route:

```php
// In your controller
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/employees/create') {
    AuthMiddleware::requireAdmin();  // Only admins can create employees
    
    // Get current user for audit logging
    $admin = AuthMiddleware::getCurrentUser();
    
    // Your code here
}
```

### 2. AuthService (services/AuthService.php)

Login returns user information including role:

```php
$service = new AuthService();
$result = $service->login($username, $password);

// Returns:
[
    'success' => true,
    'message' => 'Login successful.',
    'user' => [
        'id' => 1,
        'name' => 'Admin User',
        'username' => 'admin',
        'email' => 'admin@example.com',
        'role' => 'admin',
        'status' => 'active'
    ]
]
```

### 3. Session Variables

After login, the following session variables are set:

```php
$_SESSION['user_id']      // User ID
$_SESSION['user_name']    // Full name
$_SESSION['username']     // Username
$_SESSION['email']        // Email
$_SESSION['role']         // Role ('admin' or 'employee')
$_SESSION['status']       // Status ('active' or 'inactive')
```

## Frontend Implementation

### 1. Authentication Helper (public/script/auth.js)

Utility functions for frontend role-based access control:

#### Functions:

- **getStoredUser()** - Get user object from localStorage
  ```javascript
  const user = getStoredUser();
  // Returns: {id, name, username, email, role, status}
  ```

- **getUserRole()** - Get user role
  ```javascript
  const role = getUserRole();  // 'admin' or 'employee'
  ```

- **isLoggedIn()** - Check if user is logged in
  ```javascript
  if (isLoggedIn()) {
      console.log('User is logged in');
  }
  ```

- **hasRole(role)** - Check if user has specific role
  ```javascript
  if (hasRole('admin')) {
      // Show admin features
  }
  
  if (hasRole(['admin', 'manager'])) {
      // Check multiple roles
  }
  ```

- **isAdmin()** - Check if user is admin
  ```javascript
  if (isAdmin()) {
      // Admin only code
  }
  ```

- **isEmployee()** - Check if user is employee
  ```javascript
  if (isEmployee()) {
      // Employee only code
  }
  ```

- **redirectIfNotLoggedIn()** - Redirect if not authenticated
  ```javascript
  redirectIfNotLoggedIn();
  ```

- **redirectIfNotAuthorized(role)** - Redirect if wrong role
  ```javascript
  redirectIfNotAuthorized('admin');
  ```

- **logout()** - Logout user
  ```javascript
  logout();  // Clears localStorage and redirects to login
  ```

- **initializePageWithAuth(role)** - Initialize page with auth check
  ```javascript
  // At top of page script
  initializePageWithAuth('admin');  // Only admins can access
  ```

#### Example: Role-Based UI Elements

```html
<!-- Admin only button -->
<button id="adminBtn" style="display: none;">Admin Action</button>

<script>
    showIfRole('adminBtn', 'admin');
</script>
```

### 2. Login Flow (public/script/script.js)

- User logs in with username/password
- On success, stores user info in localStorage
- Redirects based on role:
  - **admin** → `/views/pages/admin-dashboard.html`
  - **employee** → `/views/pages/employee-dashboard.html`

### 3. Dashboard Pages

#### Admin Dashboard (views/pages/admin-dashboard.html)
- Sidebar with navigation
- Manage employees
- System settings
- Overview statistics

#### Employee Dashboard (views/pages/employee-dashboard.html)
- View own profile
- Edit profile
- Change password
- View work history

## API Endpoints

### Login
```
POST /api/auth/login
Content-Type: application/json

{
    "username": "admin",
    "password": "admin123"
}

Response (200 OK):
{
    "success": true,
    "message": "Login successful.",
    "user": {
        "id": 1,
        "name": "Admin User",
        "username": "admin",
        "email": "admin@example.com",
        "role": "admin",
        "status": "active"
    }
}

Error Response (401):
{
    "error": "Invalid username or password."
}
```

### Logout
```
POST /api/auth/logout

Response (200 OK):
{
    "success": true,
    "message": "Logged out successfully"
}
```

### Check Authentication Status
```
GET /api/auth/check

Response (200 OK):
{
    "success": true,
    "user": {
        "id": 1,
        "name": "Admin User",
        "username": "admin",
        "email": "admin@example.com",
        "role": "admin",
        "status": "active"
    }
}

Error Response (401):
{
    "success": false,
    "message": "Not authenticated"
}
```

## Default Admin Account

**Username:** admin  
**Password:** admin123  
**Role:** admin

This account is created when you run `/database/seed_admin.php`

## Security Best Practices

1. **Always validate on backend** - Never trust frontend role checks alone
2. **Use middleware for protected routes** - Always call `AuthMiddleware::requireRole()` at the start of protected endpoints
3. **Session security** - Sessions are regenerated after login
4. **Password security** - All passwords are hashed with bcrypt
5. **Clear localStorage on logout** - User data is removed from browser storage

## Implementing Role-Based Features

### Example: Admin-Only Endpoint

**Backend (controllers/EmployeeController.php):**
```php
public function create() {
    AuthMiddleware::requireAdmin();  // Only admins can create
    
    // Get logged-in admin info for audit trail
    $admin = AuthMiddleware::getCurrentUser();
    
    // Process employee creation
}
```

**Frontend (admin-dashboard.html):**
```javascript
// Protected by page-level auth check
initializePageWithAuth('admin');

// Show create button
document.getElementById('createBtn').style.display = 'block';
```

### Example: Multi-Role Access

**Backend:**
```php
// Allow both admin and manager
AuthMiddleware::requireRole(['admin', 'manager']);
```

**Frontend:**
```javascript
// Check if user has either role
if (hasRole(['admin', 'manager'])) {
    showReportButton();
}
```

## Extending the System

To add a new role (e.g., 'manager'):

1. **Update database schema:**
   ```sql
   ALTER TABLE users MODIFY role ENUM('employee', 'admin', 'manager') NOT NULL;
   ```

2. **Update frontend auth check:**
   ```javascript
   function isManager() {
       return hasRole('manager');
   }
   ```

3. **Use in backend:**
   ```php
   AuthMiddleware::requireRole(['admin', 'manager']);
   ```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Login redirects to wrong page | Check browser localStorage for correct role value |
| Unauthorized error on API | Ensure AuthMiddleware is called before processing |
| Session lost after logout | Check localStorage is cleared properly |
| Role not updating | Clear browser cache and re-login |
