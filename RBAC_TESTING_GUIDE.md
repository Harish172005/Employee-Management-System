# Role-Based Access Control - Testing Guide

## 🧪 Testing the RBAC System

Follow these steps to thoroughly test the implementation.

---

## Setup Before Testing

1. **Start XAMPP:**
   - Start Apache
   - Start MySQL

2. **Create Database:**
   - Open phpMyAdmin
   - Create database: `employee_management`
   - Import `database/user.sql`

3. **Seed Admin User:**
   - Navigate to: `http://localhost/Employee_Management_System/database/seed_admin.php`
   - Should see message: "Admin user created successfully!"
   - Username: `admin`
   - Password: `admin123`

---

## Test 1: Admin Login & Dashboard

### Steps
1. Go to: `http://localhost/Employee_Management_System/views/pages/login.html`
2. Enter:
   - Username: `admin`
   - Password: `admin123`
3. Click "Sign In"

### Expected Results
- ✅ Login succeeds without errors
- ✅ Redirects to admin dashboard
- ✅ URL shows: `.../admin-dashboard.html`
- ✅ Navbar shows "Welcome, System Admin"
- ✅ Admin dashboard displays with sidebar
- ✅ Statistics cards visible (Total Employees, Active Employees, etc.)

### Dashboard Navigation
- Click "Dashboard" → Shows overview stats
- Click "Manage Employees" → Shows employee table
- Click "Settings" → Shows settings section

---

## Test 2: Admin Logout

### Steps
1. From admin dashboard
2. Click "Logout" button

### Expected Results
- ✅ Session destroyed
- ✅ LocalStorage cleared
- ✅ Redirects to login page
- ✅ Trying to go back shows login form (no cache issues)

---

## Test 3: Invalid Login Attempts

### Test 3A: Wrong Password
1. Go to login page
2. Enter:
   - Username: `admin`
   - Password: `wrongpassword`
3. Click "Sign In"

**Expected:** Error message "Invalid username or password."

### Test 3B: Non-existent User
1. Go to login page
2. Enter:
   - Username: `nonexistent`
   - Password: `admin123`
3. Click "Sign In"

**Expected:** Error message "Invalid username or password."

### Test 3C: Empty Fields
1. Leave fields empty
2. Click "Sign In"

**Expected:** Error message "Please enter both username and password"

---

## Test 4: Create Employee User (for testing employee role)

### Steps
1. Open phpMyAdmin
2. Navigate to `employee_management` database
3. Click `users` table
4. Click **Insert** tab
5. Add new employee:
   - Name: `John Employee`
   - Username: `employee1`
   - Email: `employee1@example.com`
   - Password: (use bcrypt hash of "employee123")
     - Use online bcrypt tool: https://www.bcrypt-generator.com/
     - Password: `employee123`
   - Role: `employee`
   - Status: `active`
   - Click **Go**

**Alternative: Use SQL Insert**
```sql
INSERT INTO users (name, username, email, password, role, status) VALUES 
('John Employee', 'employee1', 'employee1@example.com', 
'$2y$10$...hash...', 'employee', 'active');
```

---

## Test 5: Employee Login & Dashboard

### Steps
1. Logout from admin account
2. Go to: `http://localhost/Employee_Management_System/views/pages/login.html`
3. Enter:
   - Username: `employee1`
   - Password: `employee123`
4. Click "Sign In"

### Expected Results
- ✅ Login succeeds
- ✅ Redirects to employee dashboard (blue theme)
- ✅ URL shows: `.../employee-dashboard.html`
- ✅ Navbar shows "Welcome, John Employee"
- ✅ Shows profile card with:
   - Name: "John Employee"
   - Email: "employee1@example.com"
   - Username: "employee1"
   - Role badge: "Employee"
   - Status badge: "Active"
- ✅ Quick Actions buttons visible

---

## Test 6: Employee Cannot Access Admin Features

### Steps
1. Stay logged in as employee
2. Manually try to access admin dashboard:
   - Edit URL to: `.../admin-dashboard.html`
   - Press Enter

### Expected Results
- ✅ Redirected back to login page
- ✅ Message: "You do not have permission to access this page"

---

## Test 7: Admin Cannot Access as Employee

### Steps
1. Login as admin
2. Log out
3. Check browser console:
   ```javascript
   console.log(getUserRole());  // Should be empty/null
   ```

### Expected Results
- ✅ No user data in localStorage
- ✅ Trying to access dashboards redirects to login

---

## Test 8: Browser Storage Test

### Admin User
1. Login as admin
2. Open Developer Tools (F12)
3. Go to **Application** → **Local Storage**
4. Check `Employee_Management_System`

**Should contain:**
```
user: {
  "id": 1,
  "name": "System Admin",
  "username": "admin",
  "email": "admin@example.com",
  "role": "admin",
  "status": "active"
}
userRole: "admin"
```

### Employee User
1. Login as employee
2. Check localStorage

**Should contain:**
```
user: {
  "id": 2,
  "name": "John Employee",
  "username": "employee1",
  "email": "employee1@example.com",
  "role": "employee",
  "status": "active"
}
userRole: "employee"
```

---

## Test 9: Console Function Testing

Open developer console (F12) and test:

```javascript
// Test helper functions
console.log(isLoggedIn());        // true
console.log(getUserRole());       // "admin" or "employee"
console.log(isAdmin());           // true if admin, false if employee
console.log(isEmployee());        // true if employee, false if admin
console.log(getStoredUser());     // Shows user object
console.log(hasRole('admin'));    // true/false
```

---

## Test 10: API Endpoint Testing

### Using Postman or curl:

#### Test Login Endpoint
```bash
curl -X POST http://localhost/Employee_Management_System/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

**Expected Response (200 OK):**
```json
{
    "success": true,
    "message": "Login successful.",
    "user": {
        "id": 1,
        "name": "System Admin",
        "username": "admin",
        "email": "admin@example.com",
        "role": "admin",
        "status": "active"
    }
}
```

#### Test Failed Login
```bash
curl -X POST http://localhost/Employee_Management_System/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"wrong"}'
```

**Expected Response (401):**
```json
{
    "error": "Invalid username or password."
}
```

#### Test Logout Endpoint
```bash
curl -X POST http://localhost/Employee_Management_System/api/auth/logout \
  -H "Content-Type: application/json"
```

**Expected Response (200 OK):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

#### Test Auth Check Endpoint
```bash
curl -X GET http://localhost/Employee_Management_System/api/auth/check
```

**Expected Response (401 if not logged in):**
```json
{
    "success": false,
    "message": "Not authenticated"
}
```

---

## Test 11: Session Test (PHP)

Create a test file: `test_session.php`

```php
<?php
session_start();

echo "Session Variables:<br>";
print_r($_SESSION);

echo "<br><br>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";
echo "User Name: " . ($_SESSION['user_name'] ?? 'Not set') . "<br>";
echo "Role: " . ($_SESSION['role'] ?? 'Not set') . "<br>";
echo "Status: " . ($_SESSION['status'] ?? 'Not set') . "<br>";
?>
```

Access: `http://localhost/Employee_Management_System/test_session.php`

**Expected (after login):**
- Session variables populated
- User role shows correct value

---

## Test 12: Unauthorized Access Test

### Simulate Missing Login
1. Open Developer Tools
2. Delete localStorage items:
   - localStorage.removeItem('user')
   - localStorage.removeItem('userRole')
3. Try to access admin dashboard
4. Should redirect to login

### Manual URL Access
1. Delete localStorage
2. Manually type admin dashboard URL
3. Should immediately redirect to login

---

## Test 13: Role Boundary Testing

### Test Admin Access to Employee Features
1. Login as admin
2. All features should be accessible
3. "Manage Employees" should work

### Test Employee Access to Admin Features
1. Login as employee
2. Cannot access admin dashboard
3. Trying to load admin page redirects

---

## Test 14: Responsive Design Test

### Test on Different Screen Sizes
1. Open any dashboard
2. Press F12 for DevTools
3. Click device toolbar (mobile view)
4. Test sizes:
   - Mobile (375x667)
   - Tablet (768x1024)
   - Desktop (1920x1080)

**Expected:** 
- ✅ Forms responsive
- ✅ Navigation adapts
- ✅ No overflow issues
- ✅ Buttons clickable

---

## Test 15: Database Role Validation

### Verify Database ENUM Values

```sql
-- Run in phpMyAdmin SQL tab
DESC users;
```

**Should show:**
- role: ENUM('employee','admin')
- status: ENUM('active','inactive')

**Note:** Value must be lowercase in database!

---

## ✅ Testing Checklist

| Test | Admin | Employee | Result |
|------|-------|----------|--------|
| Login works | ✓ | ✓ | Pass |
| Dashboard redirect correct | ✓ | ✓ | Pass |
| Logout clears data | ✓ | ✓ | Pass |
| Wrong password rejected | ✓ | ✓ | Pass |
| Cannot access other role | ✓ | ✓ | Pass |
| LocalStorage correct | ✓ | ✓ | Pass |
| API endpoints work | ✓ | ✓ | Pass |
| Session variables set | ✓ | ✓ | Pass |
| Responsive on mobile | ✓ | ✓ | Pass |
| Role ENUM correct | ✓ | ✓ | Pass |

---

## 🐛 Troubleshooting Issues

### Issue: Login always shows error
- Check database connection
- Verify default admin account exists
- Check password hash matches

### Issue: Redirect to wrong page
- Check userRole in localStorage
- Verify role values are lowercase
- Clear browser cache

### Issue: Session not persisting
- Check PHP session config
- Verify server time sync
- Check cookie settings

### Issue: Dashboard is blank
- Check browser console for errors
- Verify auth.js is loaded
- Check localStorage isn't empty

### Issue: Can access both dashboards
- Clear localStorage completely
- Check localStorage clearing logic
- Verify role validation on page load

---

## 📝 Test Report Template

```
Date: ___________
Tester: ___________
Version: 1.0

Test Results:
✓ Admin Login: PASS/FAIL
✓ Employee Login: PASS/FAIL
✓ Admin Dashboard: PASS/FAIL
✓ Employee Dashboard: PASS/FAIL
✓ Logout: PASS/FAIL
✓ Role Protection: PASS/FAIL
✓ API Endpoints: PASS/FAIL
✓ Responsive Design: PASS/FAIL

Issues Found:
1. _______________
2. _______________

Notes:
_______________
```

---

**All tests passing?** 🎉 RBAC implementation is complete and working!
