<?php
// Include database connection
require_once 'includes/db_connect.php';

try {
    // Fetch hero section data (main profile content)
    $stmt = $conn->prepare("SELECT * FROM hero_section WHERE id = 1");
    $stmt->execute();
    $hero = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retrieve CV file path from about_identity table
    $cv_stmt = $conn->prepare("SELECT cv_path FROM about_identity WHERE id = 1");
    $cv_stmt->execute();
    $cv_data = $cv_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Generate CV download link if available, otherwise fallback to placeholder (#)
    $cv_link = !empty($cv_data['cv_path']) ? "uploads/cv/" . $cv_data['cv_path'] : "#";

    // Convert comma-separated skills string into an array
    $skills_array = [];
    if(!empty($hero['skills'])) {
        $skills_array = array_map('trim', explode(',', $hero['skills']));
    }
} catch(PDOException $e) {
    // Handle database-related errors
    die("Error fetching data.");
}
?>

<!-- Hero section specific styles -->
<link rel="stylesheet" href="assets/css/hero.css">

<main id="home" class="hero-section">
    <div class="hero-glow"></div>
    
    <!-- Left section: textual introduction content -->
    <div class="hero-text-content">
        <div class="hero-badge">
            <span class="badge-dot"></span> Welcome to my portfolio
        </div>
        
        <!-- Dynamic user name -->
        <h1 class="gradient-text">Hi, I'm <?php echo htmlspecialchars($hero['name'] ?? 'Pasindu Vidushan'); ?></h1>
        
        <!-- Typewriter animation text container -->
        <div class="typewriter-text" id="typewriter"></div>
        
        <!-- Short description / tagline -->
        <p class="hero-subtitle">
            <?php echo htmlspecialchars($hero['description'] ?? ''); ?>
        </p>
        
        <!-- Primary action buttons -->
        <div class="hero-buttons">
            <a href="index.php#projects" class="btn btn-primary">View Projects</a>
            
            <!-- CV download button (enabled only if CV exists) -->
            <a href="<?php echo htmlspecialchars($cv_link); ?>" class="btn btn-secondary" <?php echo ($cv_link !== '#') ? 'download' : ''; ?> target="_blank">Download CV</a>
        </div>
    </div>

    <!-- Right section: profile image -->
    <div class="hero-image-content">
        <div class="hero-img-wrapper">
            <?php if(!empty($hero['image_path'])): ?>
                <!-- Display uploaded profile image -->
                <img src="uploads/<?php echo $hero['image_path']; ?>" alt="Profile">
            <?php else: ?>
                <!-- Fallback placeholder image -->
                <img src="https://via.placeholder.com/350" alt="Placeholder">
            <?php endif; ?>
        </div>
    </div>

    <!-- Scrolling skills marquee section -->
    <div class="marquee-box">
        <!-- Continuous scrolling track -->
        <div class="marquee-track">
            
            <!-- First set of skills -->
            <div class="marquee-content">
                <?php foreach($skills_array as $skill): ?>
                    <span class="skill-item"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($skill); ?></span>
                <?php endforeach; ?>
            </div>
            
            <!-- Duplicate set for seamless infinite scrolling -->
            <div class="marquee-content">
                <?php foreach($skills_array as $skill): ?>
                    <span class="skill-item"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($skill); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Retrieve role text for typewriter effect
        const roleText = "<?php echo htmlspecialchars($hero['role'] ?? 'Full Stack Developer'); ?>";
        const typewriterElement = document.getElementById('typewriter');
        
        // Exit if element is not found
        if (!typewriterElement) return;

        let i = 0;
        let isDeleting = false;

        // Typewriter animation logic
        function typeWriter() {
            let currentText = roleText.substring(0, i);
            typewriterElement.innerHTML = currentText + "<span style='color:white;'>|</span>";

            let typingSpeed = isDeleting ? 50 : 150;

            // Pause when full text is typed, then start deleting
            if (!isDeleting && i === roleText.length) {
                typingSpeed = 2000;
                isDeleting = true;
            } 
            // Restart typing after deletion completes
            else if (isDeleting && i === 0) {
                isDeleting = false;
                typingSpeed = 500;
            }

            // Update index for typing or deleting
            if (isDeleting) {
                i--;
            } else {
                i++;
            }

            setTimeout(typeWriter, typingSpeed);
        }

        // Initial delay before animation starts
        setTimeout(typeWriter, 1000);
    });
</script>