# Role-Based Access Control - Quick Reference Guide

## 🎯 Quick Start

### Login Flow
1. User enters credentials on login page
2. System authenticates and returns user info with role
3. Frontend stores user data in localStorage
4. **Admin** → Admin Dashboard
5. **Employee** → Employee Dashboard

---

## 🔐 Backend Usage

### Protect a Route with Role Check

```php
// Check if user is logged in
AuthMiddleware::requireLogin();

// Check specific role
AuthMiddleware::requireAdmin();           // Admin only
AuthMiddleware::requireEmployee();         // Employee only
AuthMiddleware::requireRole('admin');     // Explicit role

// Multiple roles allowed
AuthMiddleware::requireRole(['admin', 'manager']);

// Get current user info
$user = AuthMiddleware::getCurrentUser();
echo $user['id'];      // User ID
echo $user['name'];    // User name
echo $user['role'];    // User role
echo $user['email'];   // User email
```

### Complete Example Controller

```php
<?php
include_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class EmployeeController {
    public function create() {
        // Only admins can create employees
        AuthMiddleware::requireAdmin();
        
        // Get admin info for audit log
        $admin = AuthMiddleware::getCurrentUser();
        
        // Your code here
        $data = json_decode(file_get_contents('php://input'), true);
        // Process...
        
        return ['success' => true, 'message' => 'Employee created'];
    }
}
```

---

## 🎨 Frontend Usage

### Check User Role

```javascript
// Check if logged in
if (isLoggedIn()) {
    console.log('User is logged in');
}

// Check role
if (isAdmin()) {
    // Show admin panel
}

if (isEmployee()) {
    // Show employee profile
}

// Check multiple roles
if (hasRole(['admin', 'manager'])) {
    // Show management features
}
```

### Redirect Based on Role

```javascript
// At top of dashboard pages
initializePageWithAuth('admin');  // Only admin can access

// Or custom logic
if (!hasRole('admin')) {
    window.location.href = '/login.html';
}
```

### Show/Hide UI Elements

```html
<!-- Admin button (hidden by default) -->
<button id="deleteBtn" style="display: none;">Delete Employee</button>

<script>
    // Show only if admin
    showIfRole('deleteBtn', 'admin');
</script>
```

### Get Logged-In User Info

```javascript
const user = getStoredUser();
console.log(user.name);      // "John Doe"
console.log(user.role);      // "admin"
console.log(user.email);     // "john@example.com"
```

### Logout

```javascript
logout();  // Clears data and redirects to login
```

---

## 📊 Database Schema

```sql
-- User roles (in ENUM)
role ENUM('employee', 'admin') NOT NULL

-- Default admin user
username: admin
password: admin123 (hashed)
role: admin
```

---

## 🔑 API Endpoints

### Login
```
POST /api/auth/login
{ "username": "admin", "password": "admin123" }
```

### Logout
```
POST /api/auth/logout
```

### Check Status
```
GET /api/auth/check
```

---

## 📁 Key Files

| File | Purpose |
|------|---------|
| `middlewares/AuthMiddleware.php` | Backend role checking |
| `public/script/auth.js` | Frontend helper functions |
| `public/script/script.js` | Login form logic |
| `services/AuthService.php` | User authentication |
| `controllers/AuthController.php` | Auth endpoints |
| `routes/AuthRoutes.php` | Route definitions |
| `views/pages/admin-dashboard.html` | Admin interface |
| `views/pages/employee-dashboard.html` | Employee interface |

---

## ✅ Common Tasks

### Add Role Check to New Route

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uri === 'api/new-feature') {
    AuthMiddleware::requireAdmin();
    // Your code
}
```

### Create Admin-Only Button

```html
<button id="adminAction">Admin Action</button>
<script>
    showIfRole('adminAction', 'admin');
    document.getElementById('adminAction').onclick = async () => {
        const response = await fetch('/api/admin/action', {method: 'POST'});
        const data = await response.json();
        console.log(data);
    };
</script>
```

### Protect Entire Page

```html
<script>
    // Runs automatically when page loads
    initializePageWithAuth('admin');
</script>
```

---

## 🚀 Testing

### Test Login
1. Go to `/views/pages/login.html`
2. Login with: `admin` / `admin123`
3. Should redirect to Admin Dashboard

### Test Employee Login
1. Create a new employee in database with role 'employee'
2. Login with those credentials
3. Should redirect to Employee Dashboard

### Test Authorization
1. Try to access admin dashboard as employee
2. Should be redirected to login

---

## 📝 Session Variables (Backend)

After login, these are available in `$_SESSION`:

```php
$_SESSION['user_id']      // int
$_SESSION['user_name']    // string
$_SESSION['username']     // string
$_SESSION['email']        // string
$_SESSION['role']         // 'admin' or 'employee'
$_SESSION['status']       // 'active' or 'inactive'
```

---

## 🔒 Security Checklist

- [ ] Always validate roles in backend (never trust frontend)
- [ ] Use AuthMiddleware on all protected endpoints
- [ ] Clear localStorage on logout
- [ ] Use HTTPS in production
- [ ] Validate email before saving
- [ ] Hash passwords before storing
- [ ] Use prepared statements (SQL injection prevention)
- [ ] Regenerate session ID after login
- [ ] Validate all user inputs

---

## 🐛 Debugging

### Check if User Stored Correctly
```javascript
console.log(getStoredUser());
console.log(getUserRole());
```

### Check Session
```php
print_r($_SESSION);
```

### Check Middleware Errors
Look for 401/403 HTTP responses

### Clear Cache
- Ctrl+Shift+Del (Chrome/Firefox)
- Or use Incognito/Private Mode

---

## 📚 For More Details

See: `RBAC_IMPLEMENTATION.md`
