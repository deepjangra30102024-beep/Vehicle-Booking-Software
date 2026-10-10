<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/index.php");
    exit;
}

$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - Admin Panel</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/style.css">
    <style>
        /* Premium Enhancements */
        body { font-family: 'Outfit', sans-serif; background-color: #f1f5f9; }
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
        
        .customer-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .customer-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }
        
        .info-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .info-block strong { color: #0f172a; font-size: 1.05rem; }
        .info-block small { color: #64748b; display: flex; align-items: center; gap: 6px; }
        
        .stats-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            background: #e0e7ff; 
            color: #4338ca; 
            border: 1px solid #c7d2fe;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
            <li class="active"><a href="/admin/messages.php"><i class="fa-solid fa-envelope"></i> Messages</a></li>
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
                <h1>Manage Messages</h1>
            </div>
            <div class="header-right">
                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search messages...">
                </div>
                <div class="user-profile" onclick="window.location.href='settings.php';">
                    <div class="avatar"><i class="fa-solid fa-user-tie"></i></div>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                </div>
            </div>
        </header>
        
        <!-- Dashboard Content -->
        <div class="content-wrapper">
            <div class="table-container">
                <h2 class="section-title"><i class="fa-solid fa-envelope" style="color: #10b981;"></i> Contact Messages</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="customer-info">
                                            <div class="customer-avatar">
                                                <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                            </div>
                                            <div class="info-block">
                                                <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="info-block">
                                            <small><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                    </td>
                                    <td>
                                        <div class="info-block">
                                            <small><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y, h:i A', strtotime($row['created_at'])); ?></small>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-envelope-open"></i>
                                        <h3>No Messages Found</h3>
                                        <p>You don't have any contact messages yet.</p>
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
