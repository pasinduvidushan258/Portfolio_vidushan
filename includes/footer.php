<?php
require_once 'includes/db_connect.php';
if (!isset($social)) {
    try {
        $social_stmt = $conn->query("SELECT * FROM social_links WHERE id=1");
        $social = $social_stmt->fetch(PDO::FETCH_ASSOC);
        if(!$social) {
            $social = ['github'=>'', 'linkedin'=>'', 'whatsapp'=>'', 'youtube'=>'', 'instagram'=>'', 'facebook'=>''];
        }
    } catch(PDOException $e) {
        $social = ['github'=>'', 'linkedin'=>'', 'whatsapp'=>'', 'youtube'=>'', 'instagram'=>'', 'facebook'=>''];
    }
}
?>
<link rel="stylesheet" href="assets/css/footer.css">

<footer class="site-footer">
    <div class="footer-container">
        
        <div class="footer-col">
            <img src="assets/images/logo.png" alt="Logo" class="footer-logo">
            <h2>Pasindu Vidushan</h2>
            <p>Full stack software Developer & Content<br>Creator based in Sri Lanka.</p>
            <div class="footer-social">
                <?php if(!empty($social['github'])): ?>
                    <a href="<?php echo htmlspecialchars($social['github']); ?>" target="_blank"><i class="fab fa-github"></i></a>
                <?php endif; ?>
                
                <?php if(!empty($social['linkedin'])): ?>
                    <a href="<?php echo htmlspecialchars($social['linkedin']); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                <?php endif; ?>
                
                <?php if(!empty($social['whatsapp'])): ?>
                    <a href="<?php echo htmlspecialchars($social['whatsapp']); ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                <?php endif; ?>
                
                <?php if(!empty($social['youtube'])): ?>
                    <a href="<?php echo htmlspecialchars($social['youtube']); ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                <?php endif; ?>
                
                <?php if(!empty($social['instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($social['instagram']); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                
                <?php if(!empty($social['facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($social['facebook']); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-col">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php#home">Home</a></li>
                <li><a href="index.php#about">About</a></li>
                <li><a href="index.php#projects">Projects</a></li>
                <li><a href="index.php#education">Education</a></li>
                <li><a href="index.php#contact">Contact</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Services</h3>
            <ul>
                <li><a href="#">Web Development</a></li>
                <li><a href="#">AI Solutions & Data Analysis</a></li>
                <li><a href="#">Content Creation</a></li>
                <li><a href="#">Social Media Marketing</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Contact Info</h3>
            <ul class="contact-info">
                <li><i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka</li>
                <li><i class="fas fa-phone-alt"></i> +94 76 643 7197</li>
                <li><i class="fas fa-envelope"></i> pasinduvidushan258@gmail.com</li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Pasindu Vidushan. All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>