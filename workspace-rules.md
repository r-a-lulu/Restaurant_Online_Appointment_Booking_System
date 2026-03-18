# Workspace Rules — PHP Web Application Project

## 🐘 PHP Rules

* Always use **PDO**
* Never use deprecated procedural database functions
* Separate business logic from presentation
* Validate and sanitize ALL user input
* Escape output using `htmlspecialchars()`
* Use prepared statements for ALL database queries

Example:

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

---

## 🌐 HTML Rules

* Use semantic HTML5 tags (`section`, `nav`, `main`, `header`, `footer`)
* Avoid inline styles
* Maintain accessibility basics (labels, alt text, button types)
* Keep layout clean and structured

Example:

```
<section class="login-container">
```

---

## 🎨 CSS Rules

* Use external stylesheet only. Analyze existing styles, if there are inline styles, convert them to external styles. 
* Prefer class selectors over ID selectors
* Follow responsive design
* Use consistent spacing system (4px or 8px grid)
* Avoid overly deep selector nesting

Example:

```
.container {
    padding: 16px;
}
```

---

## ⚡ JavaScript Rules

* Use modular reusable functions
* Avoid global variables
* Use `addEventListener`
* Prefer `fetch()` API over old AJAX
* Minimize DOM queries

Example:

```
loginButton.addEventListener("click", handleLogin);
```

---

## 🔐 Security Rules (Critical)

* Prevent SQL Injection via prepared statements
* Escape output to prevent XSS
* Implement CSRF protection on forms
* Validate file uploads (type + size)
* Never expose database credentials in frontend
* Use session checks for protected pages

---

## 📦 Reusability Rules

If logic or UI is used **more than twice**, convert into:

* Function
* Include file
* Component

Standard components:

* header.php
* navbar.php
* footer.php
* modal components

---

## 🚀 Performance Rules

* Load JavaScript using `defer` or at bottom
* Optimize images before uploading
* Avoid large synchronous loops in PHP
* Reduce unnecessary database queries
* Cache repeated data when possible

---

## 🧪 Debugging Rules

* Use `error_log()` for backend debugging
* Avoid `echo` debugging in production
* Use `console.log()` only during development
* Always provide meaningful error messages

---

## 🏷️ Naming Conventions

| Item          | Convention |
| ------------- | ---------- |
| PHP variables | camelCase  |
| JS functions  | camelCase  |
| CSS classes   | kebab-case |
| File names    | lowercase  |

Examples:

```
user-profile.php
booking-controller.js
main-style.css
```

---

## ⭐ Architecture Guidance

* Suggest improvements in:

  * Security
  * Performance
  * Maintainability
  * Folder organization

* Prefer scalable solutions suitable for future feature expansion.

---

## ✅ AI Assistant Behavior Rule

When generating code:

* Explain briefly why the solution is used
* Prefer simple and safe implementations
* Follow existing project structure strictly
* Do not introduce frameworks unless explicitly requested

---
