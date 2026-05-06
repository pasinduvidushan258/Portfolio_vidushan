<?php
global $conn;
$stmt_id = $conn->prepare("SELECT * FROM about_identity WHERE id = 1");
$stmt_id->execute();
$identity = $stmt_id->fetch(PDO::FETCH_ASSOC);

if (!$identity) {
    $identity = ['title' => '', 'description' => '', 'education_info' => '', 'languages' => '', 'image_path' => '', 'cv_path' => ''];
}
?>

<section id="about" class="about-me-section">
    <div class="about-container">
        
       
        <div class="about-left">
            <div class="profile-card floating-card">
                <div class="card-top">
                    <span class="icon-badge">PV</span>
                    
                </div>
                
                <div class="image-wrapper">
                    <?php $img = !empty($identity['image_path']) ? 'uploads/' . $identity['image_path'] : 'assets/images/default.jpg'; ?>
                    <img src="<?php echo $img; ?>" alt="Pasindu Vidushan">
                    <span class="status-badge"><i class="fas fa-circle"></i> ACTIVE</span>
                </div>
                
                <h3>Pasindu Vidushan</h3>
                <p class="role-text">BSc Undergraduate Student</p>
                
                <div class="card-bottom">
                    <p><i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka</p>
                    <p><i class="fas fa-graduation-cap"></i> University of Kelaniya</p>
                </div>
            </div>
        </div>

        
        <div class="about-right">
            <span class="section-badge">GET TO KNOW ME</span>
            <h2 class="about-title"><?php echo htmlspecialchars($identity['title']); ?></h2>
            <p class="about-desc"><?php echo nl2br(htmlspecialchars($identity['description'])); ?></p>

            <h3 class="list-heading">What I Bring to the Table:</h3>
            <ul class="custom-list">
                <?php 
                
                $edu_lines = explode("\n", $identity['education_info']);
                foreach($edu_lines as $line) {
                    if(trim($line) != '') {
                        echo '<li><i class="fas fa-check-circle"></i> <span>' . htmlspecialchars(trim($line)) . '</span></li>';
                    }
                }
                ?>
            </ul>

            <h3 class="list-heading">Languages Known:</h3>
            <ul class="custom-list">
                <?php 
               
                $lang_lines = explode(",", $identity['languages']);
                foreach($lang_lines as $lang) {
                    if(trim($lang) != '') {
                        echo '<li><i class="fas fa-language" style="color: #a855f7;"></i> <span>' . htmlspecialchars(trim($lang)) . '</span></li>';
                    }
                }
                ?>
            </ul>

            <div class="about-buttons">
                <?php if(!empty($identity['cv_path'])): ?>
                    <!-- Download Resume Button -->
                    <a href="uploads/cv/<?php echo $identity['cv_path']; ?>" download class="btn-download">
                        <i class="fas fa-download"></i> Download Resume
                    </a>
                <?php endif; ?>
                <!-- Let's Talk Button (WhatsApp) -->
                <a href="https://wa.me/+94766437197" target="_blank" class="btn-talk">
                    <i class="fas fa-envelope"></i> Let's Talk
                </a>
            </div>
        </div>
    </div>
</section>


<style>
    .about-me-section {
        padding: 80px 20px;
        background-color: #0b1120;
        color: #cbd5e1;
        font-family: 'Poppins', sans-serif;
    }
    .about-container {
        display: flex;
        flex-wrap: wrap;
        width: 80%;
        max-width: 1400px;
        margin: 0 auto;
        gap: 50px;
        align-items: center;
    }
    .about-left {
        flex: 1;
        min-width: 300px;
        display: flex;
        justify-content: center;
    }
    .about-right {
        flex: 1.5;
        min-width: 300px;
    }
    
    
    .profile-card {
        background: #1e293b;
        border-radius: 20px;
        padding: 25px;
        width: 100%;
        max-width: 320px;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        position: relative;
    }
    
    .floating-card {
        animation: floatCard 4s ease-in-out infinite;
    }
    @keyframes floatCard {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }

    .card-top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .icon-badge {
        background: #818cf8;
        color: #fff;
        padding: 5px 10px;
        border-radius: 8px;
    }
    
    .image-wrapper {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 20px;
        border-radius: 20px;
        border: 3px solid #3b82f6;
        padding: 5px;
    }
    .image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 15px;
    }
    .status-badge {
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        background: #10b981;
        color: #fff;
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: bold;
        white-space: nowrap;
    }
    .status-badge i { font-size: 0.5rem; margin-right: 3px; }
    
    .profile-card h3 {
        color: #fff;
        margin: 10px 0 5px;
        font-size: 1.3rem;
    }
    .role-text {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-bottom: 20px;
    }
    .card-bottom {
        text-align: left;
        background: #0f172a;
        padding: 15px;
        border-radius: 12px;
        font-size: 0.85rem;
    }
    .card-bottom p { margin: 5px 0; }
    .card-bottom i { color: #3b82f6; margin-right: 10px; width: 15px; }

    
    .section-badge {
        background: rgba(59, 130, 246, 0.1);
        color: #60a5fa;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 1px;
    }
    .about-title {
        color: #fff;
        font-size: 3.5rem;
        margin: 15px 0;
    }
    .about-desc {
        line-height: 1.8;
        margin-bottom: 30px;
        font-size: 0.95rem;
    }
    .list-heading {
        color: #fff;
        font-size: 1.2rem;
        margin-bottom: 15px;
        border-left: 3px solid #3b82f6;
        padding-left: 10px;
    }
    .custom-list {
        list-style: none;
        padding: 0;
        margin-bottom: 30px;
    }
    .custom-list li {
        display: flex;
        align-items: flex-start;
        margin-bottom: 12px;
        font-size: 0.95rem;
    }
    .custom-list i {
        color: #3b82f6; 
        margin-top: 4px;
        margin-right: 12px;
        font-size: 1.1rem;
    }

    
    .about-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 20px;
    }
    .btn-download {
        background: #fff;
        color: #0f172a;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-download:hover { background: #e2e8f0; }
    
    .btn-talk {
        background: transparent;
        color: #3b82f6;
        border: 2px solid #3b82f6;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-talk:hover {
        background: rgba(59, 130, 246, 0.1);
    }
</style>