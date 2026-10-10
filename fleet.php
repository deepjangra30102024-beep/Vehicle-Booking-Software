<?php
require_once 'admin/db.php';
$result = $conn->query("SELECT * FROM vehicles ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Fleet - AutoBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .page-header {
            padding: 8rem 5% 4rem;
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: white;
            text-align: center;
        }
        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .page-header p {
            color: #94A3B8;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="page-header">
        <h1>Our Premium Fleet</h1>
        <p>Choose from our wide range of meticulously maintained vehicles</p>
    </div>

    <!-- Vehicles Section -->
    <section class="vehicles-section" id="fleet">
        <div class="vehicles-grid">
            <?php
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <div class="vehicle-card">
                    <div class="status-badge status-<?php echo htmlspecialchars($row['status']); ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </div>
                    
                    <div class="vehicle-img-wrapper">
                        <?php if($row['image']): ?>
                            <img src="uploads/vehicles/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>" class="vehicle-img">
                        <?php else: ?>
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#e2e8f0; color:#94a3b8; font-size:4rem;">
                                <i class="fa-solid fa-car"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="vehicle-info">
                        <div class="vehicle-brand"><?php echo htmlspecialchars($row['brand']); ?></div>
                        <h3 class="vehicle-model"><?php echo htmlspecialchars($row['model']); ?></h3>
                        
                        <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 0.5rem;">
                            <i class="fa-regular fa-id-card"></i> <?php echo htmlspecialchars($row['plate_number']); ?>
                        </div>
                        
                        <div class="vehicle-meta">
                            <div class="vehicle-price">
                                ₹<?php echo htmlspecialchars($row['daily_rate']); ?> <span>/ day</span>
                            </div>
                            <?php if($row['status'] == 'Available'): ?>
                                <a href="book.php?id=<?php echo $row['id']; ?>" class="btn-book">Book Now</a>
                            <?php else: ?>
                                <button class="btn-book" style="opacity:0.5; cursor:not-allowed;" disabled>Unavailable</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background: #f8fafc; border-radius: 20px;">
                    <i class="fa-solid fa-car-tunnel" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h3 style="font-size: 1.5rem; color: var(--dark); margin-bottom: 0.5rem;">No Vehicles Available Yet</h3>
                    <p style="color: var(--gray);">Check back later or contact us for more information.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
