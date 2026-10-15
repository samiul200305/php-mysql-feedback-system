# PHP MySQL Feedback Management System

A database course project developed using **PHP, MySQL, and XAMPP**.

The system allows users to register, log in, submit feedback for different places, rate places, and view feedback. It also includes an admin section for managing places, feedback, users, and reports.

---

## Course Project

This project was developed as part of a **Database Course**.

The main goal of the project is to demonstrate practical use of:

- Database design
- MySQL tables
- Primary and foreign keys
- Relationships between tables
- CRUD operations
- User authentication
- PHP and MySQL integration
- Database-driven web application development

---

## Technologies Used

| Technology | Purpose |
|---|---|
| PHP | Backend development |
| MySQL / MariaDB | Database |
| XAMPP | Local Apache and MySQL server |
| phpMyAdmin | Database management |
| HTML | Page structure |
| CSS | User interface styling |

---

## Main Features

- User registration
- User login and logout
- User and admin roles
- Add places
- Add feedback
- Rating system
- Feedback comments
- Feedback image support
- View all feedback
- Edit records
- Delete feedback
- Admin dashboard
- Report page
- Category management

---

## Database

Database name:

```text
feedback_system
```

Main tables:

```text
categories
feedback
feedback_images
places
users
```

The project uses relationships between tables using primary keys and foreign keys.

---

## Database Schema

The database structure is included in:

```text
database/feedback_system.sql
```

The SQL file contains the database table structure required to run the project.

---

## Project Structure

```text
php-mysql-feedback-system/
├── database/
│   └── feedback_system.sql
├── EDIT.php
├── REPORT.php
├── add_feedback.php
├── add_place.php
├── addd_place.php
├── admin.php
├── admin_dashboard.php
├── all_feedback.php
├── db.example.php
├── delete_feedback.php
├── give_feedback.php
├── index.php
├── login.php
├── logout.php
├── signup.php
├── test.php
├── view_feedback.php
└── .gitignore
```

---

## How to Run the Project

### 1. Install XAMPP

Install XAMPP and start:

- Apache
- MySQL

---

### 2. Place the Project in htdocs

Move or clone the project inside:

```text
/Applications/XAMPP/xamppfiles/htdocs/
```

Example:

```text
/Applications/XAMPP/xamppfiles/htdocs/20_23203034
```

---

### 3. Create the Database

Open:

```text
http://localhost/phpmyadmin
```

Create a database named:

```text
feedback_system
```

Then import:

```text
database/feedback_system.sql
```

---

### 4. Configure Database Connection

Copy:

```text
db.example.php
```

and create:

```text
db.php
```

Use the following configuration for a default XAMPP setup:

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "feedback_system";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

---

### 5. Run the Project

Open the browser and visit:

```text
http://localhost/20_23203034/
```

---

## Database Relationships

The project uses multiple relational database concepts.

Examples:

- A user can submit multiple feedback records.
- A place can receive multiple feedback records.
- A feedback record can contain an uploaded image.
- A place can belong to a category.

These relationships are maintained using foreign keys.

---

## Learning Outcomes

Through this project, I practiced:

- Relational database design
- MySQL queries
- Database normalization concepts
- Primary keys and foreign keys
- One-to-many relationships
- PHP database connectivity
- CRUD operations
- User authentication
- Admin-based database management
- Importing and exporting databases using phpMyAdmin

---

## Security Note

The actual `db.php` file is excluded from the repository.

A sample configuration file is provided as:

```text
db.example.php
```

Users should create their own `db.php` file based on their local database configuration.

---

## Disclaimer

This project was created for academic and educational purposes as part of a Database Course.

It is intended to demonstrate database concepts and PHP-MySQL integration in a local XAMPP environment.

---

## Author

**Samiul Islam**
```