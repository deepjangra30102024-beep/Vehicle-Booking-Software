<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/index.php");
    exit;
}

// Handle status update
if (isset($_POST['update_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    $stmt->execute();
    header("Location: bookings.php");
    exit;
}

// Fetch all bookings
$query = "
    SELECT b.*, v.brand, v.model, v.plate_number 
    FROM bookings b
    JOIN vehicles v ON b.vehicle_id = v.id
    ORDER BY b.created_at DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Panel</title>
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
        
        .info-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .info-block strong { color: #0f172a; font-size: 1.05rem; }
        .info-block small { color: #64748b; display: flex; align-items: center; gap: 6px; }
        
        .status-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .status-Pending { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .status-Approved { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
        .status-Rejected { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .status-Completed { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
        
        .action-form { 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .action-select { 
            padding: 8px 12px; 
            border-radius: 8px; 
            border: 1px solid #e2e8f0; 
            font-family: 'Outfit', sans-serif;
            color: #475569;
            background-color: #f8fafc;
            outline: none;
            transition: border-color 0.3s;
        }
        
        .action-select:focus {
            border-color: #6366f1;
        }
        
        .btn-update { 
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); 
            color: white; 
            border: none; 
            padding: 8px 16px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 500;
            font-family: 'Outfit', sans-serif;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
        }
        
        .btn-update:hover { 
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); 
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(79, 70, 229, 0.3);
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
            <li class="active"><a href="/admin/bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
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
                <h1>Manage Bookings</h1>
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
            <div class="table-container">
                <h2 class="section-title"><i class="fa-solid fa-list-check" style="color: #6366f1;"></i> Recent Bookings</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Details</th>
                            <th>Vehicle Information</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong style="color: #6366f1;">#<?php echo $row['id']; ?></strong></td>
                                    <td>
                                        <div class="info-block">
                                            <strong><?php echo htmlspecialchars($row['customer_name']); ?></strong>
                                            <small><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($row['customer_phone']); ?></small>
                                            <small><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($row['customer_email']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="info-block">
                                            <strong><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?></strong>
                                            <small><i class="fa-solid fa-id-card"></i> <?php echo htmlspecialchars($row['plate_number']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="info-block">
                                            <small><strong style="color: #1e293b; width: 40px;">From:</strong> <?php echo date('M d, Y', strtotime($row['pickup_date'])); ?></small>
                                            <small><strong style="color: #1e293b; width: 40px;">To:</strong> <?php echo date('M d, Y', strtotime($row['return_date'])); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo htmlspecialchars($row['status']); ?>">
                                            <i class="fa-solid <?php 
                                                if($row['status'] == 'Pending') echo 'fa-clock';
                                                elseif($row['status'] == 'Approved') echo 'fa-check';
                                                elseif($row['status'] == 'Rejected') echo 'fa-xmark';
                                                elseif($row['status'] == 'Completed') echo 'fa-flag-checkered';
                                            ?>"></i>
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="action-form">
                                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                            <select name="status" class="action-select">
                                                <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Approved" <?php echo $row['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                                <option value="Rejected" <?php echo $row['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                <option value="Completed" <?php echo $row['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn-update">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-folder-open"></i>
                                        <h3>No Bookings Found</h3>
                                        <p>You don't have any vehicle bookings yet.</p>
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
