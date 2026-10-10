<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/index.php");
    exit;
}

// Fetch stats
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'] ?? 0;
$available_vehicles = $conn->query("SELECT COUNT(*) as count FROM vehicles WHERE status = 'Available'")->fetch_assoc()['count'] ?? 0;
$total_customers = $conn->query("SELECT COUNT(DISTINCT customer_email) as count FROM bookings")->fetch_assoc()['count'] ?? 0;

$revenue_query = "
    SELECT SUM(v.daily_rate * GREATEST(DATEDIFF(b.return_date, b.pickup_date), 1)) as revenue 
    FROM bookings b 
    JOIN vehicles v ON b.vehicle_id = v.id 
    WHERE b.status = 'Completed'
";
$revenue = $conn->query($revenue_query)->fetch_assoc()['revenue'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Premium Panel</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/style.css">
</head>
<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle-small">
                <i class="fa-solid fa-car-side"></i>
            </div>
            <h2>AutoBook</h2>
        </div>
        <ul class="sidebar-menu">
            <li class="active"><a href="/admin/dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="/admin/vehicles.php"><i class="fa-solid fa-car"></i> Manage Vehicles</a></li>
            <li><a href="/admin/bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
            <li><a href="/admin/customers.php"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li><a href="/admin/messages.php"><i class="fa-solid fa-envelope"></i> Messages</a></li>
            <li><a href="/admin/revenue.php"><i class="fa-solid fa-file-invoice-dollar"></i> Revenue</a></li>
            <li><a href="/admin/settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="/admin/logout.php" class="btn-logout-sidebar"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <h1>Overview</h1>
            </div>
            <div class="header-right">
                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="user-profile" onclick="window.location.href='settings.php';">
                    <div class="avatar"><i class="fa-solid fa-user-tie"></i></div>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </div>
        </header>
        
        <!-- Dashboard Content -->
        <div class="content-wrapper">
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! 👋</h2>
                    <p>Here is what's happening with your vehicle bookings today.</p>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(102, 126, 234, 0.2); color: #667eea;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Bookings</h3>
                        <p class="stat-number"><?php echo $total_bookings; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">
                        <i class="fa-solid fa-car"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Available Vehicles</h3>
                        <p class="stat-number"><?php echo $available_vehicles; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Customers</h3>
                        <p class="stat-number"><?php echo $total_customers; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(236, 72, 153, 0.2); color: #ec4899;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Revenue</h3>
                        <p class="stat-number">₹<?php echo number_format($revenue, 2); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Additional Dashboard Sections could go here -->
        </div>
    </main>
</body>
</html>
