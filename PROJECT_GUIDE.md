# EWGS Project Guide

A simple guide on how to navigate and use the Elementary Web Grading System.

---

## Folder Structure

```
ewgs/
├── config/          - Database settings
├── controllers/     - Handle requests and logic
├── core/            - Router and base classes
├── helpers/         - Reusable functions
├── models/          - Database operations
├── public/          - CSS, JS, images (assets)
├── routes/          - URL definitions
└── views/           - HTML pages
    ├── admin/       - Admin pages
    └── templates/   - Reusable header, sidebar, footer
```

---

## How to Use Bootstrap and jQuery

Bootstrap and jQuery are stored locally in the `public` folder.

### In your pages, they are loaded via header.php:
```php
<?php require_once 'views/templates/header.php'; ?>
```

### Bootstrap classes you can use:
```html
<!-- Containers -->
<div class="container">...</div>

<!-- Grid system -->
<div class="row">
    <div class="col-md-6">Half width</div>
    <div class="col-md-6">Half width</div>
</div>

<!-- Cards -->
<div class="card">
    <div class="card-header">Title</div>
    <div class="card-body">Content</div>
</div>

<!-- Buttons -->
<button class="btn btn-primary">Blue Button</button>
<button class="btn btn-danger">Red Button</button>
<button class="btn btn-success">Green Button</button>

<!-- Forms -->
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" class="form-control" name="name">
</div>

<!-- Alerts -->
<div class="alert alert-success">Success message</div>
<div class="alert alert-danger">Error message</div>
```

### Using jQuery:
```html
<script>
$(document).ready(function(){
    // Your code runs after page loads

    // Click event
    $('#myButton').on('click', function(){
        alert('Button clicked!');
    });

    // Toggle class
    $('body').toggleClass('dark-mode');

    // Hide/Show
    $('.element').hide();
    $('.element').show();
});
</script>
```

---

## Controllers

Controllers handle the logic for each page. They are stored in `controllers/` folder.

### Creating a Controller:
```php
<?php
// File: controllers/MyController.php

class MyController
{
    public function index()
    {
        // Show a page
        require_once 'views/mypage.php';
    }

    public function save()
    {
        // Get form data
        $name = $_POST['name'] ?? '';

        // Do something with data

        // Redirect
        header('Location: /ewgs/mypage');
        exit;
    }
}
```

### Connecting Controller to Route:
```php
// In routes/web.php
$router->get('/mypage', 'MyController@index');
$router->post('/mypage/save', 'MyController@save');
```

---

## Models

Models handle all database operations. They are stored in `models/` folder.

### Creating a Model:
```php
<?php
// File: models/StudentModel.php

class StudentModel
{
    // Get all students
    public static function getAll()
    {
        $conn = getConnection();
        $result = $conn->query("SELECT * FROM tbl_student");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get one student by ID
    public static function getById($id)
    {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_student WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Add new student
    public static function insert($data)
    {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO tbl_student (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $data['name'], $data['email']);
        return $stmt->execute();
    }

    // Update student
    public static function update($id, $data)
    {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE tbl_student SET name = ?, email = ? WHERE student_id = ?");
        $stmt->bind_param("ssi", $data['name'], $data['email'], $id);
        return $stmt->execute();
    }

    // Delete student
    public static function delete($id)
    {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM tbl_student WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
```

### Using a Model in Controller:
```php
// Get all students
$students = StudentModel::getAll();

// Get one student
$student = StudentModel::getById(1);

// Add student
StudentModel::insert(['name' => 'John', 'email' => 'john@email.com']);

// Update student
StudentModel::update(1, ['name' => 'John Updated', 'email' => 'john@email.com']);

// Delete student
StudentModel::delete(1);
```

---

## Helpers

Helpers are reusable functions. They are stored in `helpers/` folder and loaded automatically.

### Flash Messages (helpers/flash.php):
```php
// Set a message
setFlash('success', 'Record saved!');
setFlash('error', 'Something went wrong!');

// Display messages in your page
<?php echo displayFlash(); ?>
```

### Creating Your Own Helper:
```php
<?php
// File: helpers/myhelper.php

function formatDate($date)
{
    return date('F j, Y', strtotime($date));
}

function formatMoney($amount)
{
    return '₱' . number_format($amount, 2);
}
```

### Using Helper in Page:
```php
<?php echo formatDate('2024-01-15'); ?>
// Output: January 15, 2024

<?php echo formatMoney(1500); ?>
// Output: ₱1,500.00
```

---

## Templates

Templates are reusable page parts. They are stored in `views/templates/`.

### Available Templates:
- `header.php` - Contains HTML head, CSS, styles
- `sidebar.php` - Navigation sidebar
- `footer.php` - Scripts and closing tags

### Using Templates in Your Page:
```php
<?php require_once 'views/templates/header.php'; ?>
<?php require_once 'views/templates/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h4>My Page Title</h4>
    </div>

    <!-- Your page content here -->

</div>

<?php require_once 'views/templates/footer.php'; ?>
```

---

## Routes

Routes connect URLs to controllers. They are defined in `routes/web.php`.

### GET Route (for viewing pages):
```php
$router->get('/students', 'StudentController@index');
```

### POST Route (for form submissions):
```php
$router->post('/students/save', 'StudentController@save');
```

### Route with Closure (without controller):
```php
$router->get('/about', function(){
    require_once 'views/about.php';
});
```

---

## Creating a New Page (Step by Step)

### 1. Create the View
```php
// File: views/admin/students.php

<?php require_once 'views/templates/header.php'; ?>
<?php require_once 'views/templates/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h4>Students</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <p>Student list goes here</p>
        </div>
    </div>
</div>

<?php require_once 'views/templates/footer.php'; ?>
```

### 2. Create the Controller
```php
// File: controllers/StudentController.php

<?php

class StudentController
{
    public function index()
    {
        require_once 'views/admin/students.php';
    }
}
```

### 3. Add the Route
```php
// In routes/web.php

$router->get('/admin/students', 'StudentController@index');
```

### 4. Access the Page
Open browser: `http://localhost/ewgs/admin/students`

---

## Database Connection

Database settings are in `config/database.php`.

### To get connection in your code:
```php
$conn = getConnection();
```

### Running a query:
```php
$conn = getConnection();
$result = $conn->query("SELECT * FROM tbl_student");
while ($row = $result->fetch_assoc()) {
    echo $row['name'];
}
```

---

## Quick Reference

| Task | Code |
|------|------|
| Include header | `<?php require_once 'views/templates/header.php'; ?>` |
| Include sidebar | `<?php require_once 'views/templates/sidebar.php'; ?>` |
| Include footer | `<?php require_once 'views/templates/footer.php'; ?>` |
| Get form data | `$_POST['fieldname']` |
| Redirect | `header('Location: /ewgs/page'); exit;` |
| Show flash message | `setFlash('success', 'Message');` |
| Display flash | `<?php echo displayFlash(); ?>` |
| Get DB connection | `$conn = getConnection();` |

---

## Tips

1. Always use `exit;` after `header('Location: ...')` redirect
2. Use prepared statements to prevent SQL injection
3. Check if user is logged in before showing admin pages
4. Use `htmlspecialchars()` when displaying user input to prevent XSS
5. Keep controllers thin - put database logic in models
