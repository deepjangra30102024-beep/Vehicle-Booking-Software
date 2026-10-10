<?php
require_once 'admin/db.php';

$vehicle = null;
$message = '';
$messageType = '';

if (isset($_GET['id'])) {
    $vehicle_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $vehicle = $stmt->get_result()->fetch_assoc();
}

if (!$vehicle) {
    header("Location: fleet.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['customer_name'];
    $email = $_POST['customer_email'];
    $phone = $_POST['customer_phone'];
    $pickup = $_POST['pickup_date'];
    $return = $_POST['return_date'];

    $stmt = $conn->prepare("INSERT INTO bookings (vehicle_id, customer_name, customer_email, customer_phone, pickup_date, return_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $vehicle_id, $name, $email, $phone, $pickup, $return);
    
    if ($stmt->execute()) {
        $message = "Booking successful! We will contact you shortly.";
        $messageType = "success";
    } else {
        $message = "Something went wrong. Please try again.";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .booking-container {
            max-width: 800px;
            margin: 5rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        .btn-submit {
            background: #4F46E5;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
        }
        .btn-submit:hover {
            background: #3730A3;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="booking-container">
        <h2>Book <?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?></h2>
        <p style="margin-bottom: 2rem; color: #666;">Rate: ₹<?php echo htmlspecialchars($vehicle['daily_rate']); ?> / day</p>

        <?php if ($message): ?>
            <div class="alert <?php echo $messageType; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="customer_name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="customer_email" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="customer_phone" required>
            </div>
            <div class="form-group">
                <label>Pickup Date</label>
                <input type="date" name="pickup_date" required>
            </div>
            <div class="form-group">
                <label>Return Date</label>
                <input type="date" name="return_date" required>
            </div>
            <button type="submit" class="btn-submit">Confirm Booking</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
