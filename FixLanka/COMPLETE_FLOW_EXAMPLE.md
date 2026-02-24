# 🎯 COMPLETE MVC FLOW - REAL EXAMPLES

This document shows **EXACTLY** how data flows through your FixLanka application with real code snippets from your project.

---

## 📚 EXAMPLE 1: Loading All Moderators (GET Request)

This is what happens when an admin opens the moderators page and sees the list of moderators.

### **STEP 1: User Opens Page** 👤 → 🖥️

**File**: `views/admin/moderators.php` (Line 189)

When admin visits: `http://localhost/2nd-Year-Group-Project/FixLanka/views/admin/moderators.php`

```php
<!-- The HTML page loads -->
<table class="moderators-table">
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Assigned Section</th>
            <th>Created Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="moderatorsTableBody">
        <!-- Data will be loaded here via JavaScript -->
    </tbody>
</table>

<script>
    const API_URL = '/2nd-Year-Group-Project/FixLanka/api/moderators.php';

    // This runs when page loads
    document.addEventListener('DOMContentLoaded', () => {
        loadModeratorsFromAPI();  // ← This is called automatically!
    });
</script>
```

**What happens**: Browser renders empty table, JavaScript starts running.

---

### **STEP 2: JavaScript Makes API Call** 🖥️ → 🌐

**File**: `views/admin/moderators.php` (Line 200)

```javascript
// Fetch moderators from API
async function loadModeratorsFromAPI() {
  try {
    // AJAX call to backend API
    const response = await fetch(`${API_URL}?action=getAll`);
    //                             ↑ This becomes:
    //                             /2nd-Year-Group-Project/FixLanka/api/moderators.php?action=getAll

    const result = await response.json();

    if (result.success) {
      allModerators = result.data; // ← Store the data
      loadModerators(); // ← Render the data
    } else {
      showMessage(result.message || "Failed to load moderators", "error");
    }
  } catch (error) {
    console.error("Error fetching moderators:", error);
    showMessage("Failed to load moderators", "error");
  }
}
```

**What happens**: Browser sends HTTP GET request to API endpoint with `action=getAll` parameter.

---

### **STEP 3: API Router Receives Request** 🌐 → 🚦

**File**: `api/moderators.php`

```php
<?php
session_start();

require_once __DIR__ . '/../controllers/ModeratorController.php';

header('Content-Type: application/json');

// Get the action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';
//        ↑ From URL: ?action=getAll
//        So $action = 'getAll'

$controller = new ModeratorController();  // ← Create controller instance

switch ($action) {
    case 'getAll':
        $controller->getAllModerators();  // ← Call this method!
        break;

    case 'add':
        $controller->addModerator();
        break;

    case 'update':
        $controller->updateModerator();
        break;

    case 'delete':
        $controller->deleteModerator();
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
?>
```

**What happens**: API router reads `action=getAll`, creates controller, calls `getAllModerators()` method.

---

### **STEP 4: Controller Executes Business Logic** 🚦 → 🧠

**File**: `controllers/ModeratorController.php`

```php
<?php
class ModeratorController {
    private $model;

    public function __construct() {
        require_once __DIR__ . '/../models/ModeratorModel.php';
        $this->model = new ModeratorModel();  // ← Create model instance
    }

    /**
     * Get all moderators
     */
    public function getAllModerators() {
        try {
            // Call model to fetch data from database
            $moderators = $this->model->getAllModerators();  // ← Call model method!

            // Return success response with data
            $this->jsonResponse([
                'success' => true,
                'data' => $moderators
            ]);

        } catch (Exception $e) {
            // Handle errors
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to fetch moderators: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send JSON response
     */
    private function jsonResponse($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
?>
```

**What happens**:

- Controller creates a model instance
- Calls `$this->model->getAllModerators()` to get data
- Wraps data in JSON format with `success: true`
- Sends response back

---

### **STEP 5: Model Queries Database** 🧠 → 🗄️

**File**: `models/ModeratorModel.php`

```php
<?php
class ModeratorModel {
    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $this->pdo = $pdo;  // ← Get database connection
    }

    /**
     * Get all moderators from database
     */
    public function getAllModerators() {
        $sql = "SELECT
                    moderator_id,
                    username,
                    email,
                    assigned_section,
                    created_at
                FROM Moderator
                ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);  // ← Prepare SQL statement
        $stmt->execute();                   // ← Execute query

        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // ← Return all rows as array
    }
}
?>
```

**What happens**:

- Model connects to database using PDO
- Executes SQL SELECT query
- Returns array of moderator records like:

```php
[
    [
        'moderator_id' => 1,
        'username' => 'john_doe',
        'email' => 'john@fixlanka.com',
        'assigned_section' => 'Advertisement Review',
        'created_at' => '2026-01-01 10:30:00'
    ],
    [
        'moderator_id' => 2,
        'username' => 'jane_smith',
        'email' => 'jane@fixlanka.com',
        'assigned_section' => 'User Reports',
        'created_at' => '2026-01-02 14:20:00'
    ]
]
```

---

### **STEP 6: Data Flows Back Up** 🗄️ → 🧠 → 🚦 → 🌐

The data travels back through the layers:

```
Model returns array
    ↓
Controller receives array → wraps in JSON → sends to API
    ↓
API outputs JSON
    ↓
Browser receives JSON response
```

**JSON Response sent to browser**:

```json
{
  "success": true,
  "data": [
    {
      "moderator_id": 1,
      "username": "john_doe",
      "email": "john@fixlanka.com",
      "assigned_section": "Advertisement Review",
      "created_at": "2026-01-01 10:30:00"
    },
    {
      "moderator_id": 2,
      "username": "jane_smith",
      "email": "jane@fixlanka.com",
      "assigned_section": "User Reports",
      "created_at": "2026-01-02 14:20:00"
    }
  ]
}
```

---

### **STEP 7: JavaScript Renders Data** 🌐 → 🖥️

**File**: `views/admin/moderators.php` (Line 202-209, 316)

```javascript
async function loadModeratorsFromAPI() {
  const response = await fetch(`${API_URL}?action=getAll`);
  const result = await response.json(); // ← Parse JSON

  if (result.success) {
    allModerators = result.data; // ← Store data in variable
    loadModerators(); // ← Call render function
  }
}

function renderModerators(moderators) {
  const tbody = document.getElementById("moderatorsTableBody");

  tbody.innerHTML = moderators
    .map(
      (moderator) => `
        <tr>
            <td>${escapeHtml(moderator.username)}</td>
            <td>${escapeHtml(moderator.email || "-")}</td>
            <td>
                <span class="moderators-section-badge">
                    ${escapeHtml(moderator.assigned_section)}
                </span>
            </td>
            <td>${formatDate(moderator.created_at)}</td>
            <td>
                <button onclick="editModerator(${moderator.moderator_id})">
                    <i data-lucide="pencil"></i>
                </button>
                <button onclick="deleteModerator(${moderator.moderator_id})">
                    <i data-lucide="trash-2"></i>
                </button>
            </td>
        </tr>
    `
    )
    .join("");

  lucide.createIcons(); // ← Render icons
}
```

**What happens**: JavaScript loops through data array and creates HTML table rows dynamically.

---

### **STEP 8: User Sees Final Result** 🖥️ → 👤

The browser now displays:

```
┌─────────────────────────────────────────────────────────────────┐
│                    Moderator Management                          │
│  Add, edit, and remove system moderators                        │
│                                                    [+ Add]       │
├─────────────────────────────────────────────────────────────────┤
│ Username   │ Email              │ Section          │ Date       │
├─────────────────────────────────────────────────────────────────┤
│ john_doe   │ john@fixlanka.com  │ [Ad Review]      │ Jan 1 2026 │
│ jane_smith │ jane@fixlanka.com  │ [User Reports]   │ Jan 2 2026 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📚 EXAMPLE 2: Adding a New Moderator (POST Request)

This shows what happens when admin clicks "Add Moderator" and submits the form.

### **STEP 1: User Fills Form** 👤 → 🖥️

**File**: `views/admin/moderators.php` (Modal form)

```html
<!-- User clicks "Add Moderator" button -->
<button onclick="openAddModeratorModal()">Add Moderator</button>

<!-- Modal opens with form -->
<form id="moderatorForm">
  <input type="hidden" name="action" value="add" />
  <input type="text" name="username" required />
  <input type="email" name="email" required />
  <input type="password" name="password" minlength="6" />
  <select name="assigned_section" required>
    <option value="Advertisement Review">Advertisement Review</option>
    <!-- more options -->
  </select>
  <button type="submit">Save Moderator</button>
</form>
```

User enters:

- Username: `mike_wilson`
- Email: `mike@fixlanka.com`
- Password: `secure123`
- Section: `Content Management`

Then clicks **"Save Moderator"**

---

### **STEP 2: JavaScript Submits Form** 🖥️ → 🌐

**File**: `views/admin/moderators.php` (Line 220)

```javascript
document
  .getElementById("moderatorForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault(); // ← Prevent normal form submission

    const formData = new FormData(this); // ← Collect form data
    // formData contains:
    // action: 'add'
    // username: 'mike_wilson'
    // email: 'mike@fixlanka.com'
    // password: 'secure123'
    // assigned_section: 'Content Management'

    const response = await fetch(API_URL, {
      method: "POST", // ← POST request
      body: formData, // ← Send form data
    });

    const result = await response.json();

    if (result.success) {
      showMessage(result.message, "success");
      closeModal("moderatorModal");
      await loadModeratorsFromAPI(); // ← Reload table
    } else {
      showMessage(result.message, "error");
    }
  });
```

**What happens**: JavaScript sends POST request with form data to API.

---

### **STEP 3: API Router Receives POST** 🌐 → 🚦

**File**: `api/moderators.php`

```php
<?php
$action = $_POST['action'] ?? $_GET['action'] ?? '';
// From form: $action = 'add'

$controller = new ModeratorController();

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->addModerator();  // ← Call this!
        break;
}
?>
```

**What happens**: API checks method is POST, then calls controller's `addModerator()`.

---

### **STEP 4: Controller Validates & Processes** 🚦 → 🧠

**File**: `controllers/ModeratorController.php`

```php
public function addModerator() {
    try {
        // 1. GET INPUT DATA
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $assigned_section = trim($_POST['assigned_section'] ?? '');

        // 2. VALIDATE REQUIRED FIELDS
        if (empty($username) || empty($email) || empty($password) || empty($assigned_section)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'All fields are required'
            ], 400);
            return;
        }

        // 3. VALIDATE EMAIL FORMAT
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Invalid email format'
            ], 400);
            return;
        }

        // 4. VALIDATE PASSWORD LENGTH
        if (strlen($password) < 6) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Password must be at least 6 characters'
            ], 400);
            return;
        }

        // 5. CHECK IF USERNAME EXISTS
        if ($this->model->usernameExists($username)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Username already exists'
            ], 400);
            return;
        }

        // 6. CHECK IF EMAIL EXISTS
        if ($this->model->emailExists($email)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Email already exists'
            ], 400);
            return;
        }

        // 7. HASH PASSWORD (Security!)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // $hashedPassword = '$2y$10$abcdefg...'  ← bcrypt hash

        // 8. CREATE MODERATOR IN DATABASE
        $moderatorId = $this->model->createModerator(
            $username,
            $email,
            $hashedPassword,  // ← Never store plain password!
            $assigned_section
        );

        // 9. FETCH NEWLY CREATED MODERATOR
        $newModerator = $this->model->getModeratorById($moderatorId);

        // 10. RETURN SUCCESS RESPONSE
        $this->jsonResponse([
            'success' => true,
            'message' => 'Moderator added successfully',
            'data' => $newModerator
        ], 201);

    } catch (Exception $e) {
        $this->jsonResponse([
            'success' => false,
            'message' => 'Failed to add moderator: ' . $e->getMessage()
        ], 500);
    }
}
```

**What happens**: Controller validates all data, hashes password, then calls model to insert.

---

### **STEP 5: Model Inserts into Database** 🧠 → 🗄️

**File**: `models/ModeratorModel.php`

```php
/**
 * Check if username exists
 */
public function usernameExists($username) {
    $sql = "SELECT moderator_id FROM Moderator WHERE username = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$username]);
    return $stmt->fetch() !== false;  // Returns true if exists
}

/**
 * Check if email exists
 */
public function emailExists($email, $excludeId = null) {
    $sql = "SELECT moderator_id FROM Moderator WHERE email = ?";
    if ($excludeId) {
        $sql .= " AND moderator_id != ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email, $excludeId]);
    } else {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
    }
    return $stmt->fetch() !== false;
}

/**
 * Create new moderator
 */
public function createModerator($username, $email, $hashedPassword, $assigned_section) {
    $sql = "INSERT INTO Moderator (username, email, password, assigned_section, created_at)
            VALUES (?, ?, ?, ?, NOW())";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        $username,
        $email,
        $hashedPassword,  // ← Hashed password stored
        $assigned_section
    ]);

    return $this->pdo->lastInsertId();  // ← Return new ID (e.g., 3)
}

/**
 * Get moderator by ID
 */
public function getModeratorById($id) {
    $sql = "SELECT moderator_id, username, email, assigned_section, created_at
            FROM Moderator
            WHERE moderator_id = ?";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

**What happens**:

1. Check username doesn't exist (SELECT query)
2. Check email doesn't exist (SELECT query)
3. Insert new moderator (INSERT query)
4. Get ID of inserted row
5. Fetch complete moderator data (SELECT query)
6. Return moderator data

**Database after insert**:

```sql
Moderator Table:
+---------------+-------------+----------------------+---------+-------------------+---------------------+
| moderator_id  | username    | email                | password| assigned_section  | created_at          |
+---------------+-------------+----------------------+---------+-------------------+---------------------+
| 1             | john_doe    | john@fixlanka.com    | $2y$... | Ad Review         | 2026-01-01 10:30:00 |
| 2             | jane_smith  | jane@fixlanka.com    | $2y$... | User Reports      | 2026-01-02 14:20:00 |
| 3             | mike_wilson | mike@fixlanka.com    | $2y$... | Content Mgmt      | 2026-01-05 16:45:00 | ← NEW!
+---------------+-------------+----------------------+---------+-------------------+---------------------+
```

---

### **STEP 6: Response Flows Back** 🗄️ → 🧠 → 🚦 → 🌐

```
Model returns:
{
    moderator_id: 3,
    username: 'mike_wilson',
    email: 'mike@fixlanka.com',
    assigned_section: 'Content Management',
    created_at: '2026-01-05 16:45:00'
}
    ↓
Controller wraps it:
{
    success: true,
    message: 'Moderator added successfully',
    data: { ... moderator object ... }
}
    ↓
API outputs JSON
    ↓
Browser receives response
```

---

### **STEP 7: JavaScript Updates UI** 🌐 → 🖥️

```javascript
const result = await response.json();

if (result.success) {
  showMessage(result.message, "success"); // ← "Moderator added successfully"
  closeModal("moderatorModal"); // ← Close the form
  await loadModeratorsFromAPI(); // ← Reload table with new data
}
```

**What happens**:

1. Success message appears: ✅ "Moderator added successfully"
2. Modal closes
3. Table reloads (goes back to EXAMPLE 1 flow)
4. New moderator appears in table

---

## 🎯 KEY TAKEAWAYS

### **The Flow Pattern** (Always the same!)

```
VIEW (HTML/JS)
    ↓ User action (page load, button click, form submit)
    ↓ JavaScript AJAX call (fetch/XMLHttpRequest)
API ROUTER (api/*.php)
    ↓ Read action/method
    ↓ Instantiate controller
CONTROLLER (controllers/*.php)
    ↓ Validate input
    ↓ Apply business rules (hash passwords, check permissions)
    ↓ Call model methods
MODEL (models/*.php)
    ↓ Execute SQL queries (SELECT, INSERT, UPDATE, DELETE)
    ↓ Return raw data
DATABASE
    ← Data flows back up
CONTROLLER
    ← Wrap in JSON format
API ROUTER
    ← Output JSON
VIEW (JavaScript)
    ← Parse JSON
    ← Update HTML/UI
USER SEES RESULT
```

### **Responsibilities Review**

| Layer          | Does                                                       | Does NOT                                                |
| -------------- | ---------------------------------------------------------- | ------------------------------------------------------- |
| **VIEW**       | Display UI, capture input, render data                     | Validate business rules, hash passwords, query database |
| **API Router** | Route requests, instantiate controller                     | Validate data, business logic, database queries         |
| **CONTROLLER** | Validate input, hash passwords, business rules, coordinate | Write SQL, know database structure                      |
| **MODEL**      | Execute SQL queries, return data                           | Validate business rules, hash passwords, format JSON    |

---

## 🔧 HOW TO ADD YOUR OWN FEATURE

Let's say you want to add: **"Deactivate Moderator"** feature

### **1. Add Model Method** (Database operation)

```php
// models/ModeratorModel.php
public function deactivateModerator($moderatorId) {
    $sql = "UPDATE Moderator SET is_active = 0 WHERE moderator_id = ?";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([$moderatorId]);
}
```

### **2. Add Controller Method** (Business logic)

```php
// controllers/ModeratorController.php
public function deactivateModerator() {
    try {
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);

        // Validate
        if (!$moderator_id) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Invalid moderator ID'
            ], 400);
            return;
        }

        // Check if exists
        $moderator = $this->model->getModeratorById($moderator_id);
        if (!$moderator) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Moderator not found'
            ], 404);
            return;
        }

        // Deactivate
        $this->model->deactivateModerator($moderator_id);

        // Success
        $this->jsonResponse([
            'success' => true,
            'message' => 'Moderator deactivated successfully'
        ]);

    } catch (Exception $e) {
        $this->jsonResponse([
            'success' => false,
            'message' => 'Failed to deactivate: ' . $e->getMessage()
        ], 500);
    }
}
```

### **3. Add API Route** (Router)

```php
// api/moderators.php
switch ($action) {
    // ... existing cases ...

    case 'deactivate':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->deactivateModerator();
        break;
}
```

### **4. Add View/JavaScript** (Frontend)

```javascript
// views/admin/moderators.php
function deactivateModerator(moderatorId) {
  if (!confirm("Deactivate this moderator?")) return;

  const formData = new FormData();
  formData.append("action", "deactivate");
  formData.append("moderator_id", moderatorId);

  fetch(API_URL, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        showMessage(result.message, "success");
        loadModeratorsFromAPI(); // Reload table
      } else {
        showMessage(result.message, "error");
      }
    });
}

// Add button in table
<button onclick="deactivateModerator(${moderator.moderator_id})">
  Deactivate
</button>;
```

**Done!** Your feature is complete following the MVC pattern.

---

## 📂 File Structure Summary

```
FixLanka/
├── views/
│   └── admin/
│       └── moderators.php          ← VIEW: HTML + JavaScript
├── api/
│   └── moderators.php              ← ROUTER: Routes actions
├── controllers/
│   └── ModeratorController.php     ← CONTROLLER: Business logic
├── models/
│   └── ModeratorModel.php          ← MODEL: Database queries
└── config/
    └── database.php                ← CONNECTION: PDO setup
```

---

## 🚀 Practice Exercise

Try implementing these features following the pattern:

1. **Reset Moderator Password**: Admin can reset a moderator's password
2. **Search Moderators by Email**: Add email search functionality
3. **Bulk Deactivate**: Deactivate multiple moderators at once
4. **Activity Log**: Track when moderators last logged in

For each feature, follow the 4 steps:

1. Model method (SQL)
2. Controller method (validation + logic)
3. API route (action mapping)
4. View/JS (UI + AJAX)

Good luck! 🎉
