# PHP User Auth API

A secure, object-oriented PHP backend API designed for user authentication. This project features a modern, decoupled architecture (Controller-Repository) and communicates via JSON, making it ideal for consumption by any JavaScript-based front-end (like React, Vue, or static HTML) using the `fetch` API.

## 🚀 Features

* User Registration (with hashed passwords)
* User Login (with session management)
* User Logout
* Secure, JSON-based request and response handling
* Decoupled architecture for easy maintenance and testing

## 💻 Technology Stack

* **Backend:** PHP (Object-Oriented)
* **Database:** MySQL (via PDO for security and flexibility)
* **Communication:** JSON (JavaScript Object Notation)
* **Security:** `password_hash()` and `password_verify()` for password management

## 🏛️ Architecture

This project is moving away from a traditional, tightly-coupled PHP model to a modern, decoupled API structure. The front-end is considered completely separate.

* **`Database.php` (Singleton)**
    * Manages the PDO database connection.
    * Uses the Singleton pattern to ensure only one connection is ever made.
    * Configured with PDO options for error handling (`ERRMODE_EXCEPTION`) and security (`EMULATE_PREPARES = false`).

* **`UsuarioRepository.php` (Data Access Layer)**
    * The **only** class that directly interacts with the database.
    * Contains all SQL queries, centralized as constants for easy maintenance.
    * Handles all business logic related to data (hashing passwords, verifying passwords, checking for duplicate emails).
    * Does **not** know about JSON or HTTP. It simply receives data and returns data (e.g., `true`/`false` or an array).

* **`UsuarioController.php` (API Layer)**
    * Acts as the "traffic cop" or entry point for the API.
    * **Does not** contain any SQL or business logic.
    * **Responsibilities:**
        1.  Receives the raw HTTP request.
        2.  Decodes the incoming JSON (`json_decode`) from the `fetch` request.
        3.  Calls the appropriate `UsuarioRepository` method.
        4.  Takes the pure data response from the repository.
        5.  Encodes a JSON response (`json_encode`) to send back to the front-end.
        6.  Manages HTTP headers (`Content-Type: application/json`) and status codes.

## ⚙️ Getting Started

### 1. Prerequisites

* A local server environment (e.g., XAMPP, WAMP, MAMP, or Docker)
* PHP 7.4 or higher
* MySQL Database

### 2. Installation

1.  **Clone the repository:**
    ```sh
    git clone [https://your-repository-url.com/](https://your-repository-url.com/) project-name
    ```

2.  **Database Setup:**
    * Create a new database in your MySQL server.
    * Run the following SQL to create the `usuario` table:
    ```sql
    CREATE TABLE IF NOT EXISTS `usuario` (
      `user_id` INT AUTO_INCREMENT PRIMARY KEY,
      `username` VARCHAR(100) NOT NULL,
      `email` VARCHAR(255) NOT NULL UNIQUE,
      `hash_password` VARCHAR(255) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ```

3.  **Configure Connection:**
    * Open the `Database.php` file.
    * Update the private attributes with your database credentials:
    ```php
    private string $host = "localhost";
    private string $username = "your_db_user";
    private string $db_name = "your_db_name";
    private string $password = "your_db_password";
    ```

## 🔐 Security Features

* **SQL Injection Prevention:** All database queries are executed using **PDO prepared statements**.
* **Password Security:** Passwords are never stored in plain text. They are securely hashed using `password_hash()` (BCRYPT) and verified using `password_verify()`.
* **Session Management:** Secure, server-side sessions (`$_SESSION`) are used to manage user login state.

## 📡 API Endpoints

All endpoints receive and respond with JSON.

---
