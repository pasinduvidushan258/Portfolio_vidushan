<?php
require_once 'includes/db_connect.php';

try {
    $social_stmt = $conn->query("SELECT * FROM social_links WHERE id=1");
    $social = $social_stmt->fetch(PDO::FETCH_ASSOC);
    if(!$social) {
        $social = ['github'=>'', 'linkedin'=>'', 'whatsapp'=>''];
    }
} catch(PDOException $e) {
    $social = ['github'=>'', 'linkedin'=>'', 'whatsapp'=>''];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasindu Vidushan | Portfolio</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/header.css?v=<?php echo time(); ?>">
</head>
<body>

<header>
    <div class="logo-area">
        <img src="assets/images/logo.png" alt="Logo" class="site-logo">
        <div class="logo-text">
            <h1>Pasindu Vidushan</h1>
            <span>BSc (UG) | University of Kelaniya 🎓</span>
        </div>
    </div>

    <div class="menu-toggle" id="mobile-menu">
        <i class="fas fa-bars"></i>
    </div>

    <nav id="nav-menu">
        <ul>
            <li><a href="index.php#home" class="nav-link active">Home</a></li>
            <li><a href="index.php#about" class="nav-link">About</a></li>
            <li><a href="index.php#services" class="nav-link">Services</a></li>
            <li><a href="index.php#projects" class="nav-link">Projects</a></li>
            <li><a href="index.php#education" class="nav-link">Education</a></li>
            <li><a href="index.php#contact" class="nav-link">Contact</a></li>
        </ul>
    </nav>

    <!-- New Overlay Element -->
    <div class="nav-overlay" id="nav-overlay"></div>

    <div class="social-links">
        <?php if(!empty($social['github'])): ?>
            <a href="<?php echo htmlspecialchars($social['github']); ?>" target="_blank" class="social-icon" title="GitHub">
                <i class="fab fa-github"></i>
            </a>
        <?php endif; ?>

        <?php if(!empty($social['linkedin'])): ?>
            <a href="<?php echo htmlspecialchars($social['linkedin']); ?>" target="_blank" class="social-icon" title="LinkedIn">
                <i class="fab fa-linkedin-in"></i>
            </a>
        <?php endif; ?>

        <?php if(!empty($social['whatsapp'])): ?>
            <a href="<?php echo htmlspecialchars($social['whatsapp']); ?>" target="_blank" class="social-icon" title="Whatsapp">
                <i class="fab fa-whatsapp"></i>
            </a>
        <?php endif; ?>
    </div>
</header>

<script>
    const mobileMenu = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');
    const navOverlay = document.getElementById('nav-overlay'); 
    const navLinks = document.querySelectorAll('#nav-menu ul li a');

    // Toggle menu and overlay
    function toggleMenu() {
        navMenu.classList.toggle('active');
        navOverlay.classList.toggle('active'); 
        
        const icon = mobileMenu.querySelector('i');
        if (navMenu.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times'); 
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');  
        }
    }

    mobileMenu.addEventListener('click', toggleMenu);
    navOverlay.addEventListener('click', toggleMenu);

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu.classList.contains('active')) {
                toggleMenu(); 
            }
            setActiveNavLink(link);
        });
    });

    function setActiveNavLink(activeLink) {
        navLinks.forEach(link => link.classList.remove('active'));
        activeLink.classList.add('active');
    }

    function updateActiveNavOnScroll() {
        const sections = document.querySelectorAll('main[id], section[id]');
        let scrollPosition = window.pageYOffset;

        sections.forEach(section => {
            const sectionTop = section.offsetTop - 120;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');

            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                const activeLink = document.querySelector(`#nav-menu ul li a[href$="#${sectionId}"]`);
                if (activeLink) {
                    setActiveNavLink(activeLink);
                }
            }
        });
    }

    window.addEventListener('scroll', updateActiveNavOnScroll);
    document.addEventListener('DOMContentLoaded', updateActiveNavOnScroll);
</script>