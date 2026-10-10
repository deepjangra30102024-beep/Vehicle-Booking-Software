<?php
require_once 'admin/db.php';
$settings_result = $conn->query("SELECT setting_key, setting_value FROM site_settings");
$site_settings = [];
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $site_settings[$row['setting_key']] = $row['setting_value'];
    }
}
$contact_address = $site_settings['contact_address'] ?? '123 Luxury Drive, New Delhi, India';
$contact_phone = $site_settings['contact_phone'] ?? '+91 98765 43210';
$contact_email = $site_settings['contact_email'] ?? 'support@autobook.com';
$contact_hours = $site_settings['contact_hours'] ?? 'Mon - Sun: 24/7 Available';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_contact'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $msg = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $msg);
    if ($stmt->execute()) {
        $message = "Your message has been sent successfully!";
        $messageType = "success";
    } else {
        $message = "Failed to send message. Please try again.";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - AutoBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .page-header {
            padding: 8rem 5% 4rem;
            background: linear-gradient(135deg, #10B981 0%, #047857 100%);
            color: white;
            text-align: center;
        }
        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .contact-container {
            display: flex;
            gap: 3rem;
            padding: 5rem 10%;
            max-width: 1200px;
            margin: 0 auto;
            flex-wrap: wrap;
        }
        .contact-info {
            flex: 1;
            min-width: 300px;
        }
        .contact-form {
            flex: 2;
            min-width: 300px;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
        }
        .contact-info h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }
        .contact-info p {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: var(--gray);
        }
        .contact-info i {
            color: var(--primary);
            font-size: 1.2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: inherit;
        }
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="page-header">
        <h1>Contact Us</h1>
    </div>

    <div class="contact-container">
        <div class="contact-info">
            <h3>Get in Touch</h3>
            <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($contact_address); ?></p>
            <p><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($contact_phone); ?></p>
            <p><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($contact_email); ?></p>
            <p><i class="fa-solid fa-clock"></i> <?php echo htmlspecialchars($contact_hours); ?></p>
        </div>
        <div class="contact-form">
            <h3 style="margin-bottom: 1.5rem;">Send us a message</h3>
            <?php if ($message): ?>
                <div style="padding: 1rem; margin-bottom: 1rem; border-radius: 8px; background: <?php echo $messageType == 'success' ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo $messageType == 'success' ? '#065f46' : '#991b1b'; ?>;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" placeholder="How can we help you?" required></textarea>
                </div>
                <button type="submit" name="submit_contact" class="btn-admin" style="border:none; cursor:pointer; font-size:1rem; width:100%;">Send Message</button>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
