<?php
$host = 'localhost';
$user = 'root'; // Default XAMPP/WAMP username
$pass = '';     // Default XAMPP/WAMP password

// Create connection without selecting a DB first
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Create database if it doesn't exist
$sql_db = "CREATE DATABASE IF NOT EXISTS Website01";
if ($conn->query($sql_db) === TRUE) {
    echo "<div style='color:green;'>Database 'Website01' checked/created successfully.</div>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db('Website01');

// 2. Create the admin_users table
$sql_table = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_table) === TRUE) {
    echo "<div style='color:green;'>Table 'admin_users' checked/created successfully.</div>";
} else {
    die("Error creating table admin_users: " . $conn->error);
}

// Create vehicles table
$sql_vehicles = "CREATE TABLE IF NOT EXISTS vehicles (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    plate_number VARCHAR(20) NOT NULL UNIQUE,
    daily_rate DECIMAL(10,2) NOT NULL,
    status ENUM('Available', 'Booked', 'Maintenance') DEFAULT 'Available',
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_vehicles) === TRUE) {
    echo "<div style='color:green;'>Table 'vehicles' checked/created successfully.</div>";
    
    // Add image column if it doesn't exist (for existing tables)
    $conn->query("ALTER TABLE vehicles ADD COLUMN image VARCHAR(255) DEFAULT NULL");
} else {
    die("Error creating table vehicles: " . $conn->error);
}

// 3. Insert default admin user if not exists
$check_user = "SELECT id FROM admin_users WHERE username = 'admin'";
$result = $conn->query($check_user);

if ($result->num_rows == 0) {
    // Hash the password securely
    $password_hash = password_hash('password123', PASSWORD_DEFAULT);
    $insert_sql = "INSERT INTO admin_users (username, password) VALUES ('admin', '$password_hash')";
    
    if ($conn->query($insert_sql) === TRUE) {
        echo "<div style='color:green;font-weight:bold;'>Default Admin User created successfully!</div>";
        echo "<div><b>Username:</b> admin<br><b>Password:</b> password123</div>";
    } else {
        echo "<div style='color:red;'>Error inserting user: " . $conn->error . "</div>";
    }
} else {
    echo "<div style='color:blue;'>Default admin user already exists in the database.</div>";
}

$conn->close();
?>

<br>
<a href="index.php" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;">Go to Login Page</a>
