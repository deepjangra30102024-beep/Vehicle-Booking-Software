<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/index.php");
    exit;
}

// Fetch total revenue
$revenue_query = "
    SELECT SUM(v.daily_rate * GREATEST(DATEDIFF(b.return_date, b.pickup_date), 1)) as total_revenue 
    FROM bookings b 
    JOIN vehicles v ON b.vehicle_id = v.id 
    WHERE b.status = 'Completed'
";
$total_revenue = $conn->query($revenue_query)->fetch_assoc()['total_revenue'] ?? 0;

// Fetch individual revenue records (completed bookings)
$records_query = "
    SELECT 
        b.id, 
        b.customer_name, 
        b.pickup_date, 
        b.return_date, 
        v.brand, 
        v.model, 
        v.daily_rate,
        GREATEST(DATEDIFF(b.return_date, b.pickup_date), 1) as days_booked,
        (v.daily_rate * GREATEST(DATEDIFF(b.return_date, b.pickup_date), 1)) as booking_revenue
    FROM bookings b 
    JOIN vehicles v ON b.vehicle_id = v.id 
    WHERE b.status = 'Completed'
    ORDER BY b.return_date DESC
";
$result = $conn->query($records_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Management - Admin Panel</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/style.css">
    <style>
        /* Premium Enhancements */
        body { font-family: 'Outfit', sans-serif; background-color: #f1f5f9; }
        
        .revenue-overview {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            padding: 40px;
            color: white;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .revenue-overview .text-block h2 {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .revenue-overview .text-block h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .revenue-overview .icon-block i {
            font-size: 5rem;
            opacity: 0.2;
        }
        
        .table-container {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            margin-top: 30px;
            overflow-x: auto;
        }
        
        .section-title {
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        th, td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        th:first-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        th:last-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }

        tbody tr {
            transition: all 0.3s ease;
        }
        
        tbody tr:hover {
            background-color: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }

        td {
            color: #334155;
            font-size: 0.95rem;
        }
        
        .revenue-amount {
            color: #059669;
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 15px;
        }
    </style>
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
            <li><a href="/admin/dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="/admin/vehicles.php"><i class="fa-solid fa-car"></i> Manage Vehicles</a></li>
            <li><a href="/admin/bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
            <li><a href="/admin/customers.php"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li><a href="/admin/messages.php"><i class="fa-solid fa-envelope"></i> Messages</a></li>
            <li class="active"><a href="/admin/revenue.php"><i class="fa-solid fa-file-invoice-dollar"></i> Revenue</a></li>
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
                <h1>Revenue</h1>
            </div>
            <div class="header-right">
                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <div class="user-profile" onclick="window.location.href='settings.php';">
                    <div class="avatar"><i class="fa-solid fa-user-tie"></i></div>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                </div>
            </div>
        </header>
        
        <!-- Dashboard Content -->
        <div class="content-wrapper">
            
            <div class="revenue-overview">
                <div class="text-block">
                    <h2>Total Generated Revenue</h2>
                    <h1>₹<?php echo number_format($total_revenue, 2); ?></h1>
                </div>
                <div class="icon-block">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>

            <div class="table-container">
                <h2 class="section-title"><i class="fa-solid fa-receipt" style="color: #059669;"></i> Completed Transactions</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Vehicle</th>
                            <th>Duration (Days)</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong style="color: #64748b;">#<?php echo $row['id']; ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($row['customer_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?> <br> <small style="color: #94a3b8;">Rate: ₹<?php echo $row['daily_rate']; ?>/day</small></td>
                                    <td><?php echo $row['days_booked']; ?> Days <br> <small style="color: #94a3b8;"><?php echo date('M d', strtotime($row['pickup_date'])); ?> - <?php echo date('M d', strtotime($row['return_date'])); ?></small></td>
                                    <td class="revenue-amount">+₹<?php echo number_format($row['booking_revenue'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                        <h3>No Revenue Yet</h3>
                                        <p>You haven't completed any bookings to generate revenue.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
