<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/index.php");
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_SESSION['admin_id'];
    
    if (isset($_POST['update_profile'])) {
        $new_username = trim($_POST['username']);
        if (!empty($new_username)) {
            $stmt = $conn->prepare("UPDATE admin_users SET username = ? WHERE id = ?");
            $stmt->bind_param("si", $new_username, $admin_id);
            if ($stmt->execute()) {
                $_SESSION['admin_username'] = $new_username;
                $message = "Profile updated successfully!";
                $messageType = "success";
            } else {
                $message = "Failed to update profile.";
                $messageType = "error";
            }
        }
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Fetch current password hash
        $stmt = $conn->prepare("SELECT password FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if (password_verify($current_password, $row['password'])) {
                if ($new_password === $confirm_password && !empty($new_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $hashed_password, $admin_id);
                    if ($update_stmt->execute()) {
                        $message = "Password changed successfully!";
                        $messageType = "success";
                    } else {
                        $message = "Failed to change password.";
                        $messageType = "error";
                    }
                } else {
                    $message = "New passwords do not match or are empty.";
                    $messageType = "error";
                }
            } else {
                $message = "Incorrect current password.";
                $messageType = "error";
            }
        }
    } elseif (isset($_POST['update_contact'])) {
        $contact_address = $_POST['contact_address'];
        $contact_phone = $_POST['contact_phone'];
        $contact_email = $_POST['contact_email'];
        $contact_hours = $_POST['contact_hours'];
        
        $conn->query("UPDATE site_settings SET setting_value = '$contact_address' WHERE setting_key = 'contact_address'");
        $conn->query("UPDATE site_settings SET setting_value = '$contact_phone' WHERE setting_key = 'contact_phone'");
        $conn->query("UPDATE site_settings SET setting_value = '$contact_email' WHERE setting_key = 'contact_email'");
        $conn->query("UPDATE site_settings SET setting_value = '$contact_hours' WHERE setting_key = 'contact_hours'");
        
        $message = "Contact information updated successfully!";
        $messageType = "success";
    }
}

// Fetch site settings
$settings_result = $conn->query("SELECT setting_key, setting_value FROM site_settings");
$site_settings = [];
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $site_settings[$row['setting_key']] = $row['setting_value'];
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/style.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f1f5f9; }
        .settings-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        @media (max-width: 1024px) {
            .settings-container { grid-template-columns: 1fr; }
        }
        .settings-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .section-title {
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 25px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            color: #1e293b;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .btn-submit {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
            font-family: 'Outfit', sans-serif;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(79, 70, 229, 0.3);
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert.success { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
        .alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
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
            <li><a href="/admin/revenue.php"><i class="fa-solid fa-file-invoice-dollar"></i> Revenue</a></li>
            <li class="active"><a href="/admin/settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
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
                <h1>Settings</h1>
            </div>
            <div class="header-right">
                <div class="user-profile" onclick="window.location.href='settings.php';">
                    <div class="avatar"><i class="fa-solid fa-user-tie"></i></div>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </div>
        </header>
        
        <!-- Dashboard Content -->
        <div class="content-wrapper">
            <?php if ($message): ?>
                <div class="alert <?php echo $messageType; ?>">
                    <i class="fa-solid <?php echo $messageType == 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="settings-container">
                <!-- Profile Settings -->
                <div class="settings-card">
                    <h2 class="section-title"><i class="fa-solid fa-user" style="color: #6366f1;"></i> Profile Information</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label>Admin Username</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($_SESSION['admin_username']); ?>" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn-submit">Update Profile</button>
                    </form>
                </div>

                <!-- Password Settings -->
                <div class="settings-card">
                    <h2 class="section-title"><i class="fa-solid fa-lock" style="color: #6366f1;"></i> Change Password</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required placeholder="Enter current password">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required placeholder="Enter new password">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required placeholder="Confirm new password">
                        </div>
                        <button type="submit" name="update_password" class="btn-submit">Change Password</button>
                    </form>
                </div>
                
                <!-- Contact Settings -->
                <div class="settings-card" style="grid-column: 1 / -1;">
                    <h2 class="section-title"><i class="fa-solid fa-address-book" style="color: #6366f1;"></i> Contact Information</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="contact_address" value="<?php echo htmlspecialchars($site_settings['contact_address'] ?? ''); ?>" required placeholder="E.g., 123 Luxury Drive, New Delhi, India">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="contact_phone" value="<?php echo htmlspecialchars($site_settings['contact_phone'] ?? ''); ?>" required placeholder="E.g., +91 98765 43210">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="contact_email" value="<?php echo htmlspecialchars($site_settings['contact_email'] ?? ''); ?>" required placeholder="E.g., support@autobook.com">
                        </div>
                        <div class="form-group">
                            <label>Working Hours</label>
                            <input type="text" name="contact_hours" value="<?php echo htmlspecialchars($site_settings['contact_hours'] ?? ''); ?>" required placeholder="E.g., Mon - Sun: 24/7 Available">
                        </div>
                        <button type="submit" name="update_contact" class="btn-submit">Update Contact Info</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
