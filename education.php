<?php
require_once 'includes/db_connect.php';

// Fetch education records from the database
$edu_stmt = $conn->query("SELECT * FROM education ORDER BY id DESC");
$educations = $edu_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch certification records from the database
$cert_stmt = $conn->query("SELECT * FROM certifications ORDER BY id DESC");
$certifications = $cert_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="education" class="page-section">
    <div class="section-wrapper">
        
        <div class="section-heading">
            <p class="sub-title">My Academic & Professional Journey</p>
            <h2 class="main-title">Education & Certifications</h2>
        </div>

        <div class="timeline-section">
            <div class="timeline-header">
                <div class="t-icon"><i class="fas fa-graduation-cap"></i></div>
                <div>
                    <h3>Educational Background</h3>
                    <p>My academic journey and achievements</p>
                </div>
            </div>

            <div class="timeline">
                <?php foreach($educations as $edu): ?>
                <div class="timeline-item">
                    <div class="timeline-dot"><i class="fas fa-university"></i></div>
                    <div class="timeline-content">
                        <div class="t-card-header">
                            <img src="uploads/education/<?php echo $edu['logo_path']; ?>" alt="Logo" class="t-logo">
                            <div class="t-title-area">
                                <h4><?php echo htmlspecialchars($edu['degree_title']); ?></h4>
                                <span><?php echo htmlspecialchars($edu['institution']); ?></span>
                            </div>
                        </div>
                        
                        <div class="t-meta">
                            <span><i class="far fa-calendar-alt"></i> <?php echo htmlspecialchars($edu['duration']); ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($edu['location']); ?></span>
                        </div>

                        <button class="t-show-more" onclick="toggleTimelineDetails(this)">
                            <span>Show More</span> <i class="fas fa-chevron-down"></i>
                        </button>

                        <div class="t-details" style="display: none;">
                            <p><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                            <?php if(!empty($edu['skills'])): ?>
                                <div class="t-skills">
                                    <?php 
                                    $skills = explode(',', $edu['skills']);
                                    foreach($skills as $skill) {
                                        echo '<span class="t-skill-tag">'.trim($skill).'</span>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="height: 60px;"></div> 
        
        <div class="timeline-section">
            <div class="timeline-header">
                <div class="t-icon" style="background: #175eb0; color: #fff;"><i class="fas fa-certificate"></i></div>
                <div>
                    <h3>Professional Certifications</h3>
                    <p>Industry-recognized credentials</p>
                </div>
            </div>

            <div class="timeline">
                <?php foreach($certifications as $cert): ?>
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);"><i class="fas fa-certificate"></i></div>
                    <div class="timeline-content">
                        <div class="t-card-header">
                            <img src="uploads/certifications/<?php echo $cert['logo_path']; ?>" alt="Logo" class="t-logo">
                            <div class="t-title-area">
                                <h4><?php echo htmlspecialchars($cert['cert_title']); ?></h4>
                                <span><?php echo htmlspecialchars($cert['provider']); ?></span>
                            </div>
                        </div>
                        
                        <div class="t-meta">
                            <span><i class="far fa-calendar-check"></i> <?php echo htmlspecialchars($cert['issue_date']); ?></span>
                            <?php if(!empty($cert['credential_id'])): ?>
                                <span><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($cert['credential_id']); ?></span>
                            <?php endif; ?>
                        </div>

                        <button class="t-show-more" onclick="toggleTimelineDetails(this)">
                            <span>Show More</span> <i class="fas fa-chevron-down"></i>
                        </button>

                        <div class="t-details" style="display: none;">
                            <p><?php echo nl2br(htmlspecialchars($cert['description'])); ?></p>
                            <?php if(!empty($cert['skills'])): ?>
                                <div class="t-skills">
                                    <?php 
                                    $skills = explode(',', $cert['skills']);
                                    foreach($skills as $skill) {
                                        echo '<span class="t-skill-tag">'.trim($skill).'</span>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($cert['credential_url'])): ?>
                                <a href="<?php echo $cert['credential_url']; ?>" target="_blank" class="verify-btn">
                                    Verify Certificate <i class="fas fa-external-link-alt"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>

<style>
    /* Section Basics */
    .page-section { padding: 100px 0; background: #0b1120; }
    .section-wrapper { width: 90%; max-width: 1100px; margin: 0 auto; }
    
    .timeline-header { display: flex; align-items: center; gap: 20px; margin-bottom: 50px; }
    .t-icon { width: 50px; height: 50px; background: rgba(129, 140, 248, 0.1); color: #818cf8; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; }
    .timeline-header h3 { color: #f8fafc; font-size: 1.8rem; margin: 0; }
    .timeline-header p { color: #94a3b8; margin: 5px 0 0; }

    /* Timeline Structure */
    .timeline { position: relative; max-width: 1000px; margin: 0 auto; }
    .timeline::after {
        content: ''; position: absolute; width: 3px; background: rgba(59, 130, 246, 0.2);
        top: 0; bottom: 0; left: 50%; transform: translateX(-50%); border-radius: 2px;
    }

    /* Timeline Item Container */
    .timeline-item { padding: 10px 40px; position: relative; width: 50%; box-sizing: border-box; opacity: 0; transition: all 0.8s ease; }
    
    .timeline-item:nth-child(odd) { left: 0; text-align: right; transform: translateX(-50px); }
    .timeline-item:nth-child(even) { left: 50%; text-align: left; transform: translateX(50px); }
    
    .timeline-item.show-item { opacity: 1; transform: translateX(0); }

    /* Timeline Node Indicator */
    .timeline-dot {
        position: absolute; width: 24px; height: 24px; background: #3b82f6;
        border-radius: 50%; top: 25px; display: flex; justify-content: center; align-items: center;
        color: #fff; font-size: 0.6rem; z-index: 10; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }
    .timeline-item:nth-child(odd) .timeline-dot { right: -12px; }
    .timeline-item:nth-child(even) .timeline-dot { left: -12px; }

    /* Floating Animation Keyframes */
    @keyframes smoothFloat {
        0% { transform: translate3d(0px, 0px, 0px); }
        25% { transform: translate3d(3px, -4px, 0px); }
        50% { transform: translate3d(-3px, 2px, 0px); }
        75% { transform: translate3d(2px, -2px, 0px); }
        100% { transform: translate3d(0px, 0px, 0px); }
    }

    /* Card Design & Interactive Effects */
    .timeline-content {
        padding: 25px; background: #1e293b; border-radius: 16px; border: 1px solid #334155;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; text-align: left;
        transition: border-color 0.4s, box-shadow 0.4s, transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        
        /* Optimization for GPU acceleration to prevent font blurring during animations */
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-font-smoothing: subpixel-antialiased;
        transform: translateZ(0);
        will-change: transform;

        animation: smoothFloat 8s ease-in-out infinite; 
    }

    .timeline-item:nth-child(even) .timeline-content { animation-delay: -4s; }
    .timeline-item:nth-child(3n) .timeline-content { animation-delay: -2s; }

    /* Hover State: Primary Accent Glow */
    .timeline-content:hover { 
        animation: none; 
        transform: translate3d(0, -8px, 0) scale(1.02); /* Maintain sharpness with 3D transform */
        border-color: #818cf8; 
        box-shadow: 0 10px 40px -10px rgba(129, 140, 248, 0.5), 0 0 15px -2px rgba(129, 140, 248, 0.3); 
    }

    /* Hover State: Secondary (Certification) Accent Glow */
    .timeline-section:nth-of-type(2) .timeline-content:hover {
        border-color: #10b981;
        box-shadow: 0 10px 40px -10px rgba(16, 185, 129, 0.5), 0 0 15px -2px rgba(16, 185, 129, 0.3); 
    }

    /* Logo Styling */
    .t-card-header { display: flex; align-items: center; gap: 18px; margin-bottom: 15px; }
    
    .t-logo { 
        width: 55px; height: 55px; border-radius: 12px; object-fit: contain; 
        background: #fff; padding: 6px; border: 2px solid #e2e8f0; 
        transition: all 0.3s ease; 
    }
    .timeline-content:hover .t-logo { border-color: #818cf8; }
    .timeline-section:nth-of-type(2) .timeline-content:hover .t-logo { border-color: #10b981; }

    /* Typography */
    .t-title-area h4 { color: #f8fafc; font-size: 1.2rem; margin: 0 0 5px; font-weight: 700; line-height: 1.3; }
    .t-title-area span { color: #818cf8; font-size: 0.95rem; font-weight: 600; }
    .timeline-section:nth-of-type(2) .t-title-area span { color: #10b981; }

    .t-meta { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 15px; }
    .t-meta span { color: #94a3b8; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }

    /* Interaction Toggle */
    .t-show-more { background: none; border: none; color: #818cf8; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 0; transition: 0.3s; }
    .t-show-more:hover { color: #fff; }
    .timeline-section:nth-of-type(2) .t-show-more { color: #10b981; }
    .timeline-section:nth-of-type(2) .t-show-more:hover { color: #fff; }

    /* Content Expansion Area */
    .t-details { margin-top: 15px; padding-top: 15px; border-top: 1px dashed #334155; }
    .t-details p { color: #cbd5e1; font-size: 0.9rem; line-height: 1.7; margin-bottom: 15px; }
    
    /* Taxonomy/Skills Tags */
    .t-skills { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 15px; }
    .t-skill-tag { 
        background: rgba(129, 140, 248, 0.05); color: #cbd5e1; 
        padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 500;
        border: 1px solid rgba(129, 140, 248, 0.15); transition: 0.3s; 
    }
    .t-skill-tag:hover { background: rgba(129, 140, 248, 0.15); border-color: #818cf8; color: #fff; }
    
    .timeline-section:nth-of-type(2) .t-skill-tag { background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.15); }
    .timeline-section:nth-of-type(2) .t-skill-tag:hover { background: rgba(16, 185, 129, 0.15); border-color: #10b981; color: #fff; }

    /* Verification Call-to-Action */
    .verify-btn { display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: bold; border: 1px solid rgba(16, 185, 129, 0.3); transition: 0.3s; }
    .verify-btn:hover { background: rgba(16, 185, 129, 0.2); border-color: #10b981; }

    /* Mobile Responsive Overrides */
    @media screen and (max-width: 768px) {
        .timeline::after { left: 20px; transform: translateX(0); }
        .timeline-item { width: 100%; padding-left: 60px; padding-right: 0; }
        .timeline-item:nth-child(odd), .timeline-item:nth-child(even) { left: 0; text-align: left; transform: translateY(30px); }
        .timeline-item:nth-child(odd) .timeline-dot, .timeline-item:nth-child(even) .timeline-dot { left: 8px; right: auto; }
        .timeline-item.show-item { transform: translateY(0); }
    }
</style>

<script>
    /**
     * Toggles visibility of additional details within a timeline card.
     */
    function toggleTimelineDetails(btn) {
        const content = btn.nextElementSibling;
        const textSpan = btn.querySelector('span');
        const icon = btn.querySelector('i');

        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
            textSpan.textContent = "Show Less";
            icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
        } else {
            content.style.display = "none";
            textSpan.textContent = "Show More";
            icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
        }
    }

    /**
     * Initializes scroll-triggered animations using Intersection Observer API.
     */
    document.addEventListener("DOMContentLoaded", function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show-item'); // Apply visibility class upon viewport entry
                }
            });
        }, { threshold: 0.1 });

        const items = document.querySelectorAll('.timeline-item');
        items.forEach(item => observer.observe(item));
    });
</script>