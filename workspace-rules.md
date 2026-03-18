# Workspace Rules — PHP Web Application Project

## 🐘 PHP Rules

* Always use **PDO**
* Never use deprecated procedural database functions
* Separate business logic from presentation (All data fetching, database operations, and form processing must occur at the very top of the PHP file, before any HTML is rendered)
* Validate and sanitize ALL user input
* Escape output using `htmlspecialchars()`
* Use prepared statements for ALL database queries
* Always use the `$basePath` variable for routing and asset linking (CSS, JS, images) in all PHP files to prevent broken links across different directory depths

Example:

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

---

## 🌐 HTML Rules

* Write strictly Semantic HTML5 (`<main>`, `<section>`, `<article>`, `nav`, `header`, `footer`)
* Avoid inline styles
* Maintain accessibility basics (Every image must have a descriptive `alt` tag, interactive elements must have proper `aria-labels`, inputs must have `labels`, and maintain keyboard accessibility)
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
* Before committing any frontend changes to Git, always visually verify (via browser tool or manual review) that the layout and CSS have not broken
* Always use semantic Conventional Commits for Git messages (e.g., `feat:`, `fix:`, `refactor:`, `style:`, `docs:`)

---
