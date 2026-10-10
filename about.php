<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - AutoBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .page-header {
            padding: 8rem 5% 4rem;
            background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%);
            color: white;
            text-align: center;
        }
        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .content-section {
            padding: 5rem 10%;
            max-width: 1200px;
            margin: 0 auto;
            line-height: 1.8;
            font-size: 1.1rem;
            color: var(--dark);
        }
        .content-section p {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="page-header">
        <h1>About Us</h1>
    </div>

    <section class="content-section">
        <h2>Welcome to AutoBook</h2>
        <p>AutoBook was founded with a simple goal: to make premium vehicle rentals accessible and hassle-free. We believe that the journey is just as important as the destination, which is why we offer a meticulously maintained fleet of top-tier vehicles.</p>
        <p>Whether you're looking for a luxury sedan for a business trip, a spacious SUV for a family vacation, or a sports car for a weekend thrill, our diverse selection caters to all your needs.</p>
        <p>Our commitment to excellence extends beyond our vehicles. Our dedicated customer support team is available 24/7 to ensure your experience is smooth from booking to drop-off.</p>
        <h3 style="margin-top: 2rem; color: var(--primary);">Our Mission</h3>
        <p>To redefine the car rental experience through exceptional service, unmatched reliability, and a passion for driving.</p>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
