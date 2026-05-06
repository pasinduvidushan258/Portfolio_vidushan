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
            <li><a href="index.php#home" class="nav-link active">Home</a></li>
            <li><a href="index.php#about" class="nav-link">About</a></li>
            <li><a href="index.php#services" class="nav-link">Services</a></li>
            <li><a href="index.php#projects" class="nav-link">Projects</a></li>
            <li><a href="index.php#education" class="nav-link">Education</a></li>
            <li><a href="index.php#contact" class="nav-link">Contact</a></li>
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
    const navLinks = document.querySelectorAll('#nav-menu ul li a');

    mobileMenu.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        const icon = mobileMenu.querySelector('i');
        if (navMenu.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                const icon = mobileMenu.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
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