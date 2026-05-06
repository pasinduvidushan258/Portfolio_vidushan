<?php
// Include the database connection (must be loaded at the beginning of the file)
require_once 'includes/db_connect.php';

// Retrieve services data from the database
try {
    $stmt = $conn->prepare("SELECT * FROM services ORDER BY id ASC");
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Handle any errors during data fetching
    die("Error fetching services.");
}
?>

<section id="services" class="page-section">
    <div class="section-wrapper">
        <div class="section-heading">
            <p class="sub-title">What I Do</p>
            <h2 class="main-title">Premium Services</h2>
        </div>
        
        <div class="service-grid">
            <?php if (!empty($services)): ?>
                <?php foreach($services as $service): ?>
                    <!-- Individual Service Card -->
                    <div class="premium-service-card">
                        <div class="service-icon">
                            <!-- Dynamically apply icon class retrieved from the database -->
                            <i class="<?php echo htmlspecialchars($service['icon_class']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Display message when no services are available -->
                <p style="color: #94a3b8; text-align: center; width: 100%;">No services added yet. Please add them from the Admin Panel.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .page-section {
        padding: 100px 0;
    }
    
    .section-wrapper {
        width: 80%;
        max-width: 1400px;
        margin: 0 auto;
    }

    .section-heading {
        text-align: center;
        margin-bottom: 50px;
    }

    /* Premium Service Cards Layout */
    .service-grid {
        display: grid;
        /* Defines a fixed 3-column layout */
        grid-template-columns: repeat(3, 1fr); 
        gap: 35px;
        margin-top: 40px;
    }

    .premium-service-card {
        background: rgba(30, 41, 59, 0.5); 
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 24px;
        padding: 45px 35px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-top: 1px solid rgba(129, 140, 248, 0.3); 
        position: relative;
        overflow: hidden;
        z-index: 1;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .premium-service-card::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        /* Decorative radial gradient overlay */
        background: radial-gradient(circle at top right, rgba(129, 140, 248, 0.15), transparent 60%);
        z-index: -1;
        transition: all 0.5s ease;
    }

    .premium-service-card:hover {
        transform: translateY(-12px);
        border-color: rgba(129, 140, 248, 0.5);
        box-shadow: 0 20px 45px rgba(129, 140, 248, 0.15);
    }

    .premium-service-card:hover::before {
        background: radial-gradient(circle at top right, rgba(129, 140, 248, 0.25), transparent 70%);
    }

    .service-icon {
        width: 85px;
        height: 85px;
        background: rgba(15, 23, 42, 0.7);
        color: #818cf8;
        border-radius: 24px; 
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin: 0 auto 25px;
        transition: all 0.4s ease;
        border: 1px solid rgba(129, 140, 248, 0.2);
        box-shadow: inset 0 0 20px rgba(129, 140, 248, 0.05);
    }

    .premium-service-card:hover .service-icon {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        transform: scale(1.1) rotate(5deg); 
        border-color: transparent;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
    }

    .premium-service-card h3 {
        color: #f8fafc;
        font-size: 1.4rem;
        margin-bottom: 15px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .premium-service-card p {
        color: #94a3b8;
        font-size: 0.95rem;
        line-height: 1.7;
    }
</style>