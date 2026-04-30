<?php
require_once 'includes/db_connect.php';

try {
    $stmt = $conn->prepare("SELECT * FROM hero_section WHERE id = 1");
    $stmt->execute();
    $hero = $stmt->fetch(PDO::FETCH_ASSOC);

    $skills_array = [];
    if(!empty($hero['skills'])) {
        $skills_array = array_map('trim', explode(',', $hero['skills']));
    }
} catch(PDOException $e) {
    die("Error fetching data.");
}
?>

<link rel="stylesheet" href="assets/css/hero.css">

<main class="hero-section">
    <div class="hero-glow"></div>
    
    <!-- Left Side: Text Content -->
    <div class="hero-text-content">
        <div class="hero-badge">
            <span class="badge-dot"></span> Welcome to my portfolio
        </div>
        
        <h1 class="gradient-text">Hi, I'm <?php echo htmlspecialchars($hero['name'] ?? 'Pasindu Vidushan'); ?></h1>
        
        <div class="typewriter-text" id="typewriter"></div>
        
        <p class="hero-subtitle">
            <?php echo htmlspecialchars($hero['description'] ?? ''); ?>
        </p>
        
        <div class="hero-buttons">
            <a href="projects.php" class="btn btn-primary">View Projects</a>
            <a href="assets/cv.pdf" class="btn btn-secondary" target="_blank">Download CV</a>
        </div>
    </div>

    <!-- Right Side: Floating Image -->
    <div class="hero-image-content">
        <div class="hero-img-wrapper">
            <?php if(!empty($hero['image_path'])): ?>
                <img src="uploads/<?php echo $hero['image_path']; ?>" alt="Profile">
            <?php else: ?>
                <img src="https://via.placeholder.com/350" alt="Placeholder">
            <?php endif; ?>
        </div>
    </div>

    <!-- Skills marquee section -->
    <div class="marquee-box">
        <!-- Scrolling marquee track -->
        <div class="marquee-track">
            <!-- First set of skill items -->
            <div class="marquee-content">
                <?php foreach($skills_array as $skill): ?>
                    <span class="skill-item"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($skill); ?></span>
                <?php endforeach; ?>
            </div>
            
            <!-- Second set of skill items to support seamless scrolling -->
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
        const roleText = "<?php echo htmlspecialchars($hero['role'] ?? 'Full Stack Developer'); ?>";
        const typewriterElement = document.getElementById('typewriter');
        
        if (!typewriterElement) return;

        let i = 0;
        let isDeleting = false;

        function typeWriter() {
            let currentText = roleText.substring(0, i);
            typewriterElement.innerHTML = currentText + "<span style='color:white;'>|</span>";

            let typingSpeed = isDeleting ? 50 : 150;

            if (!isDeleting && i === roleText.length) {
                typingSpeed = 2000;
                isDeleting = true;
            } else if (isDeleting && i === 0) {
                isDeleting = false;
                typingSpeed = 500;
            }

            if (isDeleting) {
                i--;
            } else {
                i++;
            }

            setTimeout(typeWriter, typingSpeed);
        }

        setTimeout(typeWriter, 1000);
    });
</script>