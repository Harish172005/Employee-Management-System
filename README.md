# Employee Management System

A web-based Employee Management System built with PHP and MySQL using a structured MVC-based layered architecture. The system provides role-based access for administrators and employees, allowing administrators to manage users, employees, and departments while employees can view and update their own permitted information.

---

## 1. Project Introduction

The **Employee Management System (EMS)** is a web application designed to simplify employee and department management within an organization.

The application follows an **MVC-based layered architecture**, separating responsibilities between routing, controllers, services, repositories, middleware, and the presentation layer.

The system provides two main user roles:

* **Admin** – manages users, employees, and departments.
* **Employee** – views and updates permitted personal information and views department information.

The application also implements authentication, authorization, CSRF protection, input validation, password hashing, session management, and prepared SQL statements.

---

## 2. Features

### Authentication

* User login and logout
* Session-based authentication
* Authentication status checking
* Change password functionality
* Password hashing using PHP's password hashing API
* Session regeneration after login

### Admin Features

* Admin dashboard
* User management
* Employee management
* Add employees
* View employee details
* Update employee details
* Deactivate employees
* Employee search
* Employee filtering
* Employee pagination
* Department management
* Add departments
* View department details
* Update departments
* Deactivate departments
* Department search and filtering
* Dashboard statistics

### Employee Features

* Employee dashboard
* View own profile
* Update permitted personal information
* View own department information
* Change password

### Security Features

* Role-Based Access Control (RBAC)
* Authentication middleware
* Authorization middleware
* CSRF protection
* Password hashing
* Password verification
* Input validation
* Output escaping
* PDO prepared statements
* Session-based user identification
* Restricted employee profile updates
* HTTP status codes for API responses
* Custom 403 Access Denied page

---

## 3. Technologies Used

### Backend

* PHP 8.2+
* MySQL / MariaDB
* PDO
* PHP Sessions

### Frontend

* HTML5
* CSS3
* JavaScript
* Bootstrap 5
* Bootstrap Icons
* Fetch API

### Development Tools

* XAMPP
* Apache
* MySQL / MariaDB
* Composer
* Git
* GitHub
* Visual Studio Code

### Architecture

* MVC
* Layered Architecture
* Repository Pattern
* Service Layer
* Dependency Injection
* Repository Interfaces
* Middleware

---

## 4. Requirements

Before installing the project, make sure the following are installed:

* PHP 8.2 or later
* Apache Web Server
* MySQL or MariaDB
* XAMPP
* Composer
* Git
* Modern web browser

Recommended environment:

```text
PHP       8.2+
Apache    2.4+
MySQL     8.x / MariaDB
Composer  2.x
```

---

## 5. Installation Steps

### Step 1: Clone the Repository

Clone the project into the XAMPP `htdocs` directory:

```bash
cd C:\xampp\htdocs

git clone <YOUR_GITHUB_REPOSITORY_URL> Employee_Management_System
```

Replace `<YOUR_GITHUB_REPOSITORY_URL>` with your repository URL.

---

### Step 2: Start XAMPP

Open XAMPP Control Panel and start:

* Apache
* MySQL

---

### Step 3: Install Composer Dependencies

Navigate to the project directory:

```bash
cd C:\xampp\htdocs\Employee_Management_System
```

Run:

```bash
composer install
```

This installs the dependencies specified in `composer.lock`.

---

### Step 4: Configure Apache

The application uses the `public` directory as the web root.

The Apache VirtualHost should point to:

```text
C:/xampp/htdocs/Employee_Management_System/public
```

Example:

```apache
<VirtualHost *:80>
    ServerName employee-management.test
    DocumentRoot "C:/xampp/htdocs/Employee_Management_System/public"

    <Directory "C:/xampp/htdocs/Employee_Management_System/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add the following entry to the Windows hosts file:

```text
127.0.0.1 employee-management.test
```

Restart Apache after making these changes.

---

## 6. Database Setup

### Step 1: Open phpMyAdmin

Open phpMyAdmin through your XAMPP installation.

Create the database:

```sql
CREATE DATABASE `employee-management`;
```

---

### Step 2: Select the Database

```sql
USE `employee-management`;
```

> Because the database name contains a hyphen, use backticks around the database name.

---

### Step 3: Import the Database

Import the project's SQL/database file if one is provided.

The database contains the application's main tables, including tables for:

* Users
* Employees
* Departments

The exact schema should be taken from the SQL/database files included with the project.

---

## 7. Configuration

The application uses a database configuration class to establish the PDO connection.

The database connection should be configured using environment variables rather than hardcoded credentials.

The application expects the database configuration to provide:

```text
Host
Database name
Username
Password
```

The application also uses:

* Apache VirtualHost configuration
* PHP session configuration
* Composer autoloading
* `.env` configuration

---

## 8. Environment Variables

Create a `.env` file in the project root:

```env
DB_HOST=localhost
DB_NAME=employee-management
DB_USER=root
DB_PASSWORD=
```

### Example

```text
Employee_Management_System/
├── .env
├── composer.json
├── composer.lock
└── ...
```

The `.env` file should **not be committed to Git**.

Add it to `.gitignore:

```gitignore
.env
```

For production environments, use a dedicated database user instead of the default `root` account.

---

## 9. Folder Structure

```text
Employee_Management_System/
│
├── config/
│   └── dbConfig.php
│
├── controllers/
│   ├── AuthController.php
│   ├── BaseController.php
│   ├── EmployeeController.php
│   ├── DepartmentController.php
│   └── UserController.php
│
├── database/
│
├── middlewares/
│   ├── AuthMiddleware.php
│   └── CsrfMiddleware.php
│
├── models/
│   ├── EmployeeRepository.php
│   ├── EmployeeRepositoryInterface.php
│   ├── DepartmentRepository.php
│   ├── DepartmentRepositoryInterface.php
│   └── ...
│
├── public/
│   ├── index.php
│   ├── js/
│   ├── style/
│   └── ...
│
├── routes/
│   ├── AuthRoutes.php
│   ├── EmployeeRoutes.php
│   ├── DepartmentRoutes.php
│   ├── UserRoutes.php
│   └── DashboardRoutes.php
│
├── services/
│   ├── AuthService.php
│   ├── EmployeeService.php
│   └── DepartmentService.php
│
├── tests/
│
├── traits/
│   └── FieldValidationTrait.php
│
├── utilities/
│
├── views/
│   ├── pages/
│   │   ├── admin/
│   │   └── employee/
│   ├── login.html
│   ├── change-password.html
│   └── 403.html
│
├── vendor/
│
├── .env
├── .gitignore
├── composer.json
└── composer.lock
```

> `vendor/` and `.env` should not be committed to the repository.

---

## 10. User Roles

The application currently supports two roles.

### Admin

Administrators have access to management functionality.

| Feature               | Admin |
| --------------------- | ----- |
| Admin Dashboard       | Yes   |
| User Management       | Yes   |
| Employee Management   | Yes   |
| Add Employee          | Yes   |
| Update Employee       | Yes   |
| Deactivate Employee   | Yes   |
| Department Management | Yes   |
| Add Department        | Yes   |
| Update Department     | Yes   |
| Deactivate Department | Yes   |
| View Employee Details | Yes   |
| View Own Profile      | Yes   |
| Change Password       | Yes   |

### Employee

Employees have access to self-service functionality.

| Feature                         | Employee |
| ------------------------------- | -------- |
| Employee Dashboard              | Yes      |
| View Own Profile                | Yes      |
| Update Permitted Profile Fields | Yes      |
| View Own Department             | Yes      |
| Change Password                 | Yes      |
| User Management                 | No       |
| Employee Management             | No       |
| Department Management           | No       |

Employee profile updates are restricted to permitted fields. Administrative fields such as employee ID, department, designation, salary, status, and date of joining cannot be modified by employees.

---

## 11. API Documentation

The application provides REST-style API endpoints for authentication, employees, departments, users, and dashboard data.

### Authentication

#### Login

```http
POST /api/auth/login
```

Example request:

```json
{
    "username": "username",
    "password": "password"
}
```

#### Logout

```http
POST /api/auth/logout
```

#### Authentication Check

```http
GET /api/auth/check
```

#### Change Password

```http
POST /api/auth/change-password
```

Example request:

```json
{
    "currentPassword": "current-password",
    "newPassword": "new-password"
}
```

---

### Employee APIs

#### Get Employees

```http
GET /api/employees
```

Supports search, filtering, and pagination.

Example:

```http
GET /api/employees?page=2&search=harish&status=active
```

#### Get Employee

```http
GET /api/employees/{id}
```

Example:

```http
GET /api/employees/15
```

#### Create Employee

```http
POST /api/employees
```

#### Update Employee

```http
PUT /api/employees/{id}
```

#### Deactivate Employee

```http
PATCH /api/employees/{id}/deactivate
```

---

### Employee Self-Service APIs

#### Get Own Profile

```http
GET /api/employee/profile
```

#### Update Own Profile

```http
PUT /api/employee/profile
```

#### Get Own Department

```http
GET /api/employee/department
```

---

### Department APIs

#### Get Departments

```http
GET /api/departments
```

Supports search and status filtering.

Example:

```http
GET /api/departments?search=IT&status=active
```

#### Get Department

```http
GET /api/departments/{id}
```

#### Create Department

```http
POST /api/departments
```

#### Update Department

```http
PUT /api/departments/{id}
```

#### Deactivate Department

```http
PATCH /api/departments/{id}/deactivate
```

---

### User APIs

User-management endpoints are restricted to administrators.

```http
GET /api/users
POST /api/users
PUT /api/users/{id}
```

The exact available endpoints should match the route definitions in the project.

---

### HTTP Status Codes

The API uses standard HTTP status codes.

| Status Code | Meaning                        |
| ----------- | ------------------------------ |
| 200         | Successful request             |
| 201         | Resource created               |
| 400         | Bad request / validation error |
| 401         | Authentication required        |
| 403         | Access denied                  |
| 404         | Resource not found             |
| 409         | Conflict                       |
| 500         | Internal server error          |

---

## 12. Screenshots

Added in the screenshots directory of the main application pages.


---

## 13. Testing

Testing is performed to verify authentication, authorization, validation, API behavior, and application functionality.

### Authentication Testing

* Valid login credentials
* Invalid username
* Invalid password
* Login with inactive user
* Logout
* Authentication status
* Change password
* Invalid current password
* Invalid new password

### Authorization Testing

* Admin accessing admin pages
* Employee accessing employee pages
* Employee attempting to access admin pages
* Employee attempting to access admin APIs
* Unauthenticated user accessing protected resources

Unauthorized page navigation returns a **403 Access Denied** page.

Unauthorized API requests return a JSON `403 Forbidden` response.

### Employee Testing

* Create employee
* View employee
* Update employee
* Deactivate employee
* Search employees
* Filter employees
* Pagination
* Duplicate email validation
* Employee profile access
* Employee profile update restrictions

### Department Testing

* Create department
* View department
* Update department
* Deactivate department
* Search departments
* Filter departments
* Duplicate department validation
* Prevent department deactivation when employees are assigned

### Security Testing

* CSRF token validation
* Session authentication
* Role-based authorization
* Password hashing
* Prepared SQL statements
* Input validation
* Output escaping

---

## 14. Known Issues

The following are current limitations or areas that may require further improvement:

* The application is primarily configured for a local XAMPP development environment.
* API documentation can be expanded with complete request and response examples.
* Automated test coverage can be increased.
* Production deployment configuration has not been fully implemented.
* Error logging and monitoring can be improved for production environments.
* Some dependencies may still be manually included using `require_once` rather than being completely managed through Composer autoloading.
* The application currently has a relatively simple employee self-service dashboard.

---

## 15. Future Enhancements

Potential future improvements include:

### Employee Features

* Attendance management
* Leave management
* Leave request and approval workflow
* Employee notifications
* Company announcements

### Admin Features

* Advanced employee analytics
* Attendance reports
* Leave reports
* Advanced dashboard statistics
* Company announcements

### Security

* Two-factor authentication
* Password reset via email

---

## License

This project is developed for learning and demonstration purposes.

---

## Author

**Harish Kumar M.**


