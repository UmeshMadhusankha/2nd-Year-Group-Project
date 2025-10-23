# Moderator Management System - MVC Architecture

## 📐 Architecture Overview

This system follows the **Model-View-Controller (MVC)** pattern with a clean separation of concerns.

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT (Browser)                         │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    VIEW (moderators.php)                         │
│  - User Interface (HTML/CSS)                                     │
│  - JavaScript for AJAX calls                                     │
│  - No business logic or database queries                         │
└───────────────────────────────┬─────────────────────────────────┘
                                │ AJAX Request
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    API (api/moderators.php)                      │
│  - Routes requests to controller                                 │
│  - Handles HTTP methods (GET/POST)                               │
│  - No business logic                                             │
└───────────────────────────────┬─────────────────────────────────┘
                                │ Calls method
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│              CONTROLLER (ModeratorController.php)                │
│  - Business logic layer                                          │
│  - Input validation                                              │
│  - Password hashing                                              │
│  - Error handling                                                │
│  - Calls Model methods                                           │
│  - Returns JSON responses                                        │
└───────────────────────────────┬─────────────────────────────────┘
                                │ Calls data methods
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│               MODEL (ModeratorModel.php)                         │
│  - Database operations ONLY                                      │
│  - CRUD methods (Create, Read, Update, Delete)                   │
│  - No validation or business logic                               │
│  - Returns raw data                                              │
└───────────────────────────────┬─────────────────────────────────┘
                                │ SQL Queries
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    DATABASE (fix_lanka)                          │
│  - Moderator table                                               │
│  - Stores data persistently                                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📁 File Structure

```
FixLanka/
├── api/
│   └── moderators.php          # API Router
├── controllers/
│   └── ModeratorController.php # Business Logic
├── models/
│   └── ModeratorModel.php      # Database Operations
├── views/
│   └── admin/
│       └── moderators.php      # User Interface
└── config/
    └── databse.php             # Database Connection
```

---

## 🔄 Request Flow Examples

### **Example 1: Get All Moderators**

```
1. User opens page
   ↓
2. VIEW: JavaScript calls loadModeratorsFromAPI()
   ↓
3. API: GET /api/moderators.php?action=getAll
   ↓
4. CONTROLLER: ModeratorController->getAllModerators()
   ↓
5. MODEL: ModeratorModel->getAllModerators()
   ↓
6. DATABASE: SELECT moderator_id, username, email... FROM Moderator
   ↓
7. MODEL: Returns array of moderator data
   ↓
8. CONTROLLER: Wraps in JSON response
   ↓
9. VIEW: Renders data in table
```

### **Example 2: Add Moderator**

```
1. User clicks "Add Moderator" and fills form
   ↓
2. VIEW: Form submission via AJAX
   ↓
3. API: POST /api/moderators.php (action=add)
   ↓
4. CONTROLLER: ModeratorController->addModerator()
   │   - Validates input (required fields, email format, password length)
   │   - Checks username exists via MODEL
   │   - Checks email exists via MODEL
   │   - Hashes password with password_hash()
   ↓
5. MODEL: ModeratorModel->createModerator()
   ↓
6. DATABASE: INSERT INTO Moderator VALUES (...)
   ↓
7. MODEL: Returns last insert ID
   ↓
8. MODEL: Fetches newly created moderator
   ↓
9. CONTROLLER: Returns success JSON with new moderator data
   ↓
10. VIEW: Shows success message and refreshes table
```

---

## 📄 Component Responsibilities

### **VIEW (moderators.php)**

**Responsibilities:**

- Display HTML interface
- Handle user interactions (clicks, form inputs)
- Make AJAX calls to API
- Render data received from API
- Show success/error messages

**Does NOT:**

- ❌ Validate business rules
- ❌ Hash passwords
- ❌ Execute database queries
- ❌ Handle authentication

---

### **API (api/moderators.php)**

**Responsibilities:**

- Route incoming requests
- Instantiate controller
- Call appropriate controller method based on action
- Return controller's response

**Does NOT:**

- ❌ Validate data
- ❌ Access database
- ❌ Hash passwords
- ❌ Implement business logic

---

### **CONTROLLER (ModeratorController.php)**

**Responsibilities:**

- Validate input data (required fields, formats, lengths)
- Hash passwords (password_hash)
- Coordinate between API and Model
- Handle errors and exceptions
- Format responses as JSON
- Business rule enforcement

**Does NOT:**

- ❌ Write SQL queries directly
- ❌ Know about database structure
- ❌ Handle HTTP routing

**Methods:**

```php
- getAllModerators()     // Get all records
- addModerator()         // Create new moderator
- updateModerator()      // Update existing moderator
- deleteModerator()      // Delete moderator
- jsonResponse()         // Format JSON output
```

---

### **MODEL (ModeratorModel.php)**

**Responsibilities:**

- Execute database queries ONLY
- Return raw data
- Handle database-specific operations

**Does NOT:**

- ❌ Validate business rules
- ❌ Hash passwords
- ❌ Return JSON
- ❌ Handle errors beyond database exceptions

**Methods:**

```php
- getAllModerators()              // SELECT all
- getModeratorById($id)           // SELECT by ID
- usernameExists($username)       // Check username
- emailExists($email, $exclude)   // Check email
- createModerator(...)            // INSERT
- updateModerator(...)            // UPDATE without password
- updateModeratorWithPassword()   // UPDATE with password
- deleteModerator($id)            // DELETE
```

---

## 🔐 Security Features

### **Password Hashing** (Controller Layer)

```php
// In ModeratorController->addModerator()
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$this->model->createModerator($username, $email, $hashedPassword, ...);
```

- Uses bcrypt algorithm
- Automatic salt generation
- Secure by default

### **Prepared Statements** (Model Layer)

```php
// In ModeratorModel->createModerator()
$stmt = $this->pdo->prepare("INSERT INTO Moderator (...) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $email, $hashedPassword, $assigned_section]);
```

- Prevents SQL injection
- Automatic escaping

### **Input Validation** (Controller Layer)

```php
// Required fields
if (empty($username) || empty($email)) { ... }

// Email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { ... }

// Password strength
if (strlen($password) < 6) { ... }
```

### **XSS Prevention** (View Layer)

```javascript
// In JavaScript
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}
```

---

## ✅ Advantages of This Architecture

### **1. Separation of Concerns**

- Each layer has one responsibility
- Easy to understand and maintain
- Changes in one layer don't affect others

### **2. Reusability**

- Model methods can be used by multiple controllers
- Controller logic can be reused across different views

### **3. Testing**

- Each component can be tested independently
- Model tests don't need controller logic
- Controller tests can mock the model

### **4. Scalability**

- Easy to add new features
- Can switch database without changing controller
- Can add new endpoints without changing model

### **5. Team Collaboration**

- Frontend developers work on View
- Backend developers work on Controller/Model
- Database admins work on Model
- Clear boundaries between roles

---

## 🆚 Comparison with Old Approach

### **Before Refactoring:**

```php
// Everything in Controller
class ModeratorController {
    public function addModerator() {
        // Validation
        if (empty($username)) { ... }

        // Database query directly in controller
        $stmt = $this->pdo->prepare("SELECT * FROM Moderator WHERE username = ?");
        $stmt->execute([$username]);

        // More queries...
        $stmt = $this->pdo->prepare("INSERT INTO Moderator VALUES (...)");
    }
}
```

**Problems:**

- ❌ Controller knows database structure
- ❌ Can't reuse queries
- ❌ Hard to test
- ❌ Mixing concerns

### **After Refactoring:**

```php
// Controller (Business Logic)
class ModeratorController {
    public function addModerator() {
        // Validation only
        if (empty($username)) { ... }

        // Use model methods
        if ($this->model->usernameExists($username)) { ... }
        $this->model->createModerator(...);
    }
}

// Model (Database Operations)
class ModeratorModel {
    public function usernameExists($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM Moderator WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }
}
```

**Benefits:**

- ✅ Clear separation
- ✅ Reusable methods
- ✅ Easy to test
- ✅ Single responsibility

---

## 🔧 How to Extend

### **Add New Feature: "Reset Moderator Password"**

**1. Add Model Method:**

```php
// In ModeratorModel.php
public function resetPassword($moderator_id, $hashedPassword) {
    $stmt = $this->pdo->prepare("UPDATE Moderator SET password = ? WHERE moderator_id = ?");
    return $stmt->execute([$hashedPassword, $moderator_id]);
}
```

**2. Add Controller Method:**

```php
// In ModeratorController.php
public function resetPassword() {
    $moderator_id = (int)($_POST['moderator_id'] ?? 0);
    $new_password = $_POST['new_password'] ?? '';

    // Validation
    if (!$moderator_id || strlen($new_password) < 6) {
        $this->jsonResponse(['success' => false, 'message' => 'Invalid input'], 400);
        return;
    }

    // Hash and update
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
    $this->model->resetPassword($moderator_id, $hashedPassword);

    $this->jsonResponse(['success' => true, 'message' => 'Password reset successfully']);
}
```

**3. Add API Route:**

```php
// In api/moderators.php
case 'resetPassword':
    $controller->resetPassword();
    break;
```

**4. Add View Function:**

```javascript
// In moderators.php
async function resetModeratorPassword(moderatorId) {
  const newPassword = prompt("Enter new password (min 6 chars):");
  if (!newPassword) return;

  const formData = new FormData();
  formData.append("action", "resetPassword");
  formData.append("moderator_id", moderatorId);
  formData.append("new_password", newPassword);

  const response = await fetch(API_URL, { method: "POST", body: formData });
  const result = await response.json();

  showMessage(result.message, result.success ? "success" : "error");
}
```

---

## 📚 Summary

| Layer          | File                      | Purpose        | Contains                                   |
| -------------- | ------------------------- | -------------- | ------------------------------------------ |
| **View**       | `moderators.php`          | User Interface | HTML, CSS, JavaScript                      |
| **API**        | `api/moderators.php`      | Request Router | HTTP handling, routing                     |
| **Controller** | `ModeratorController.php` | Business Logic | Validation, password hashing, coordination |
| **Model**      | `ModeratorModel.php`      | Data Access    | SQL queries, database operations           |
| **Database**   | `fix_lanka.Moderator`     | Data Storage   | Table structure, data persistence          |

**Data Flow:**

```
User → View → API → Controller → Model → Database
```

**Response Flow:**

```
Database → Model → Controller → API → View → User
```

This is **proper MVC architecture** following industry best practices! 🎯
