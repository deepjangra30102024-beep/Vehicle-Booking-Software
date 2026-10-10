<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/index.php");
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $plate_number = $_POST['plate_number'];
    $daily_rate = $_POST['daily_rate'];
    $status = $_POST['status'];
    
    // Handle Image Upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/vehicles/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $image = time() . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['image']['name']));
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
    }

    if (isset($_POST['add_vehicle'])) {
        $stmt = $conn->prepare("INSERT INTO vehicles (brand, model, plate_number, daily_rate, status, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $brand, $model, $plate_number, $daily_rate, $status, $image);
        $stmt->execute();
        header("Location: /admin/vehicles.php");
        exit;
    } elseif (isset($_POST['update_vehicle'])) {
        $id = $_POST['id'];
        if ($image) {
            $stmt = $conn->prepare("UPDATE vehicles SET brand=?, model=?, plate_number=?, daily_rate=?, status=?, image=? WHERE id=?");
            $stmt->bind_param("ssssssi", $brand, $model, $plate_number, $daily_rate, $status, $image, $id);
        } else {
            $stmt = $conn->prepare("UPDATE vehicles SET brand=?, model=?, plate_number=?, daily_rate=?, status=? WHERE id=?");
            $stmt->bind_param("sssssi", $brand, $model, $plate_number, $daily_rate, $status, $id);
        }
        $stmt->execute();
        header("Location: /admin/vehicles.php");
        exit;
    }
}

// Handle Delete
if ($action == 'delete' && $edit_id > 0) {
    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    header("Location: /admin/vehicles.php");
    exit;
}

// Fetch Vehicle for Edit
$edit_vehicle = null;
if ($action == 'edit' && $edit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_vehicle = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles - Admin Panel</title>
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
            <li><a href="/admin/dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li class="active"><a href="/admin/vehicles.php"><i class="fa-solid fa-car"></i> Manage Vehicles</a></li>
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
                <h1>Manage Vehicles</h1>
            </div>
            <div class="header-right">
                <div class="user-profile" onclick="window.location.href='settings.php';">
                    <div class="avatar"><i class="fa-solid fa-user-tie"></i></div>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <div class="content-wrapper">
            
            <!-- Vehicle Form -->
            <div class="crud-form-container">
                <h2><?php echo $edit_vehicle ? 'Edit Vehicle' : 'Add New Vehicle'; ?></h2>
                <form action="/admin/vehicles.php" method="POST" class="crud-form" enctype="multipart/form-data">
                    <?php if ($edit_vehicle): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_vehicle['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group-crud">
                            <label>Brand</label>
                            <input type="text" name="brand" value="<?php echo $edit_vehicle ? htmlspecialchars($edit_vehicle['brand']) : ''; ?>" required>
                        </div>
                        <div class="form-group-crud">
                            <label>Model</label>
                            <input type="text" name="model" value="<?php echo $edit_vehicle ? htmlspecialchars($edit_vehicle['model']) : ''; ?>" required>
                        </div>
                        <div class="form-group-crud">
                            <label>Plate Number</label>
                            <input type="text" name="plate_number" value="<?php echo $edit_vehicle ? htmlspecialchars($edit_vehicle['plate_number']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group-crud">
                            <label>Daily Rate (₹)</label>
                            <input type="number" step="0.01" name="daily_rate" value="<?php echo $edit_vehicle ? htmlspecialchars($edit_vehicle['daily_rate']) : ''; ?>" required>
                        </div>
                        <div class="form-group-crud">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="Available" <?php echo ($edit_vehicle && $edit_vehicle['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                                <option value="Booked" <?php echo ($edit_vehicle && $edit_vehicle['status'] == 'Booked') ? 'selected' : ''; ?>>Booked</option>
                                <option value="Maintenance" <?php echo ($edit_vehicle && $edit_vehicle['status'] == 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                            </select>
                        </div>
                        <div class="form-group-crud">
                            <label>Vehicle Image (Optional)</label>
                            <input type="file" name="image" accept="image/*">
                            <?php if($edit_vehicle && $edit_vehicle['image']): ?>
                                <small style="color: #94a3b8; margin-top: 5px; display: block;">Current image exists. Upload new to replace.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <?php if ($edit_vehicle): ?>
                            <button type="submit" name="update_vehicle" class="btn-submit">Update Vehicle</button>
                            <a href="/admin/vehicles.php" class="btn-cancel">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_vehicle" class="btn-submit"><i class="fa-solid fa-plus"></i> Add Vehicle</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Vehicle List -->
            <div class="crud-list-container">
                <h2>Vehicle Inventory</h2>
                <div class="table-responsive">
                    <table class="crud-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>ID</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Plate #</th>
                                <th>Daily Rate</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM vehicles ORDER BY id DESC");
                            if ($result->num_rows > 0):
                                while ($row = $result->fetch_assoc()):
                            ?>
                                <tr>
                                    <td>
                                        <?php if($row['image']): ?>
                                            <img src="/uploads/vehicles/<?php echo htmlspecialchars($row['image']); ?>" alt="Vehicle" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #94a3b8;"><i class="fa-solid fa-car"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['brand']); ?></td>
                                    <td><?php echo htmlspecialchars($row['model']); ?></td>
                                    <td><span class="badge badge-dark"><?php echo htmlspecialchars($row['plate_number']); ?></span></td>
                                    <td>₹<?php echo htmlspecialchars($row['daily_rate']); ?></td>
                                    <td>
                                        <?php 
                                            $status_class = '';
                                            if($row['status'] == 'Available') $status_class = 'badge-success';
                                            if($row['status'] == 'Booked') $status_class = 'badge-warning';
                                            if($row['status'] == 'Maintenance') $status_class = 'badge-danger';
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                    </td>
                                    <td class="action-btns">
                                        <a href="?action=edit&id=<?php echo $row['id']; ?>" class="btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                        <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this vehicle?');"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center">No vehicles found. Add one above!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </main>
</body>
</html>
