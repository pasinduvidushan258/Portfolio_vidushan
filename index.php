<?php 
// Initialize database connection (required for dynamic content across included sections)
require_once 'includes/db_connect.php'; 
?>

<?php 
// Include global header (navigation, meta tags, styles, etc.)
include 'includes/header.php'; 
?>

<?php 
// Hero section (landing introduction area)
include 'hero.php'; 
?>

<?php 
// About section - personal identity and bio
include 'about_identity.php'; 

// About section - technical and professional skills
include 'about_skills.php'; 

// About section - work experience timeline
include 'about_experience.php'; 
?>

<?php 
// Services section - list of offered services
include 'services.php'; 
?>

<?php 
// Projects section - portfolio and featured work
include 'projects.php'; 
?>

<?php 
// Education section - academic background
include 'education.php'; 
?>

<?php 
// Contact section - user communication form and details
include 'contact.php'; 
?>

<?php 
// Include global footer (scripts, closing tags, etc.)
include 'includes/footer.php'; 
?>