# public/js structure

```
js/
├── shared/
│   ├── csrf.js           window.getCsrfToken()
│   └── user-display.js   window.displayWelcomeUser()
├── auth/
│   ├── login.js
│   ├── logout.js
│   └── change-password.js
├── forms/
│   ├── employee-form.js
│   ├── department-form.js
│   └── user-form.js
└── pages/
    └── employees-table.js   (list/view/edit/deactivate — no CSRF calls, unchanged)
```

## What changed
- `getCsrfToken()` was duplicated in 6 files — now lives once in `shared/csrf.js`.
- The `localStorage` "Welcome, {name}" logic was duplicated in the 3 `*-form.js` files — now lives once in `shared/user-display.js` as `displayWelcomeUser()`.
- Everything else (form fields, endpoints, validation, error handling) is untouched — just relocated and deduplicated.

## Script tags
Load `shared/` scripts before anything that depends on them:

```html
<!-- Auth pages -->
<script src="/js/shared/csrf.js"></script>
<script src="/js/auth/login.js"></script>
```

```html
<!-- Form pages -->
<script src="/js/shared/csrf.js"></script>
<script src="/js/shared/user-display.js"></script>
<script src="/js/forms/employee-form.js"></script>
```

```html
<!-- Employees table page -->
<script src="/js/pages/employees-table.js"></script>
```

`logout.js` calls `logout()` — keep that as a global, wired to your logout button's `onclick="logout()"`.
