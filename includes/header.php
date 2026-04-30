<?php
// Retrieve the current page name for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasindu Vidushan | Portfolio</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
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
            <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a></li>
            <li><a href="services.php" class="<?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">Services</a></li>
            <li><a href="projects.php" class="<?php echo ($current_page == 'projects.php') ? 'active' : ''; ?>">Projects</a></li>
            <li><a href="education.php" class="<?php echo ($current_page == 'education.php') ? 'active' : ''; ?>">Education</a></li>
            <li><a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
        </ul>
    </nav>

    <div class="social-links">
        <a href="https://github.com/pasinduvidushan258" target="_blank" class="social-icon" title="GitHub">
            <i class="fab fa-github"></i>
        </a>
        <a href="https://www.linkedin.com/in/pasindu-vidushan-b34a6a389?utm_source=share_via&utm_content=profile&utm_medium=member_ios" target="_blank" class="social-icon" title="LinkedIn">
            <i class="fab fa-linkedin-in"></i>
        </a>
        <a href="https://wa.me/766437197" target="_blank" class="social-icon" title="Whatsapp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
</header>

<script>
    const mobileMenu = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-menu');

    mobileMenu.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        
        // Toggle the menu icon between bars and close (X) when the menu is opened or closed
        const icon = mobileMenu.querySelector('i');
        if (navMenu.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
</script>