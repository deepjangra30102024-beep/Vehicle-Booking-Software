<?php require_once 'admin/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoBook - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .hero {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('https://images.unsplash.com/photo-1485291571150-772bcfc10da5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
            color: white;
            min-height: 90vh;
        }
        .hero h1 {
            color: white;
            font-size: 4.5rem;
            text-shadow: 0 4px 6px rgba(0,0,0,0.3);
            background: none;
            -webkit-text-fill-color: white;
        }
        .hero p {
            color: #cbd5e1;
            font-size: 1.4rem;
        }
        .hero-btn {
            background: var(--primary);
            border: 2px solid var(--primary);
        }
        .hero-btn:hover {
            background: transparent;
            border-color: white;
        }
        .features {
            display: flex; gap: 2rem; justify-content: center; max-width: 1200px; margin: 0 auto; flex-wrap: wrap; padding: 5rem 5%;
        }
        .feature-box {
            text-align: center; padding: 3rem 2rem; background: white; border-radius: 20px; flex: 1; min-width: 300px;
            box-shadow: var(--shadow); transition: transform 0.3s;
        }
        .feature-box:hover {
            transform: translateY(-10px);
        }
        .feature-box i {
            font-size: 4rem; color: var(--primary); margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Experience The Drive</h1>
            <p>Premium car rentals at your fingertips. Choose from our luxury fleet and hit the road with confidence.</p>
            <div style="margin-top: 2rem;">
                <a href="fleet.php" class="hero-btn">View Our Fleet</a>
                <a href="about.php" class="hero-btn" style="background: transparent; border-color: white; margin-left: 1rem;">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Quick Features -->
    <section style="background: #f8fafc;">
        <div class="features">
            <div class="feature-box">
                <i class="fa-solid fa-tags"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Best Prices Guaranteed</h3>
                <p style="color: var(--gray);">We offer the most competitive daily rates in the market without compromising on quality.</p>
            </div>
            <div class="feature-box">
                <i class="fa-solid fa-shield-halved"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Fully Insured Rides</h3>
                <p style="color: var(--gray);">Drive with complete peace of mind knowing you and your passengers are fully covered.</p>
            </div>
            <div class="feature-box">
                <i class="fa-solid fa-headset"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">24/7 Roadside Support</h3>
                <p style="color: var(--gray);">Our dedicated customer support team is here to help you anytime, anywhere.</p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section style="background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%); padding: 6rem 5%; text-align: center; color: white;">
        <h2 style="font-size: 3rem; margin-bottom: 1.5rem;">Ready to start your journey?</h2>
        <p style="font-size: 1.2rem; margin-bottom: 2.5rem; color: #e0e7ff;">Browse our fleet and book your perfect car today.</p>
        <a href="fleet.php" style="background: white; color: #4F46E5; padding: 1rem 3rem; border-radius: 50px; font-weight: bold; font-size: 1.2rem; text-decoration: none; display: inline-block;">Book a Car Now</a>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
