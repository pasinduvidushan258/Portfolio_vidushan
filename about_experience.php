<?php
require_once 'includes/db_connect.php';

$stmt_exp = $conn->prepare("SELECT * FROM experience ORDER BY id DESC");
$stmt_exp->execute();
$experiences = $stmt_exp->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="experience-section">
    <div class="skills-header" style="text-align: center; margin-bottom: 60px;">
        <p class="sub-title" style="color: #94a3b8; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 2px;">My Journey</p>
        <h2 class="main-title" style="font-size: 3.5rem; color: #818cf8; font-weight: 700; margin-top: 10px;">Work & Experience</h2>
    </div>

    <div class="snake-timeline">
        <?php 
        $count = 0; 
        foreach($experiences as $exp): 
            // Alternating sides: left and right
            $side_class = ($count % 2 == 0) ? 'left' : 'right';
        ?>
            <div class="timeline-row <?php echo $side_class; ?>">
                
                <div class="timeline-point">
                    <i class="fas fa-briefcase"></i>
                </div>
                
                <div class="timeline-box">
                    <div class="timeline-content">
                        <div class="exp-inner-flex">
                            
                            <?php if (!empty($exp['logo_path'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($exp['logo_path']); ?>" alt="Company Logo" class="exp-logo">
                            <?php else: ?>
                                <div class="exp-logo-placeholder"><i class="fas fa-building"></i></div>
                            <?php endif; ?>

                            <div class="exp-details">
                                <h3 class="exp-title"><?php echo htmlspecialchars($exp['title']); ?></h3>
                                
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                    <span class="exp-org"><?php echo htmlspecialchars($exp['organization']); ?></span>
                                    <?php if (!empty($exp['company_website'])): ?>
                                        <a href="<?php echo htmlspecialchars($exp['company_website']); ?>" target="_blank" class="exp-website-link"><i class="fas fa-external-link-alt"></i></a>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="exp-meta">
                                    <span><i class="far fa-calendar-alt"></i> <?php echo htmlspecialchars($exp['duration']); ?></span>
                                    <?php if (!empty($exp['location'])): ?>
                                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($exp['location']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty(trim($exp['description']))): ?>
                                    <div class="exp-desc-container">
                                        <div class="full-text" id="desc-<?php echo $exp['id']; ?>" style="display: none; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.05); margin-top: 10px;">
                                            <p class="exp-desc">
                                                <?php echo nl2br(htmlspecialchars($exp['description'])); ?>
                                            </p>
                                        </div>
                                        <button class="show-more-btn" onclick="toggleDescription(<?php echo $exp['id']; ?>)">
                                            Show More <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
        $count++;
        endforeach; 
        ?>
    </div>
</section>

<script>
    function toggleDescription(id) {
        var fullText = document.getElementById('desc-' + id);
        var btn = fullText.nextElementSibling;

        if (fullText.style.display === 'none') {
            fullText.style.display = 'block';
            btn.innerHTML = 'Show Less <i class="fas fa-chevron-up"></i>';
        } else {
            fullText.style.display = 'none';
            btn.innerHTML = 'Show More <i class="fas fa-chevron-down"></i>';
        }
    }
</script>

<style>
    .experience-section { padding: 80px 20px; background-color: #0b1120; color: #fff; font-family: 'Poppins', sans-serif; overflow: hidden; }
    
    /* Center Line Layout */
    .snake-timeline { position: relative; max-width: 1050px; margin: 0 auto; padding: 20px 0; }
    
    .snake-timeline::after {
        content: ''; position: absolute; width: 2px; background-color: #334155;
        top: 0; bottom: 0; left: 50%; margin-left: -1px; z-index: 0;
    }

    /* Flexbox Row for Alternating items */
    .timeline-row { display: flex; justify-content: space-between; align-items: flex-start; width: 100%; margin-bottom: 40px; position: relative; }
    .timeline-row.left { justify-content: flex-start; }
    .timeline-row.right { justify-content: flex-end; }

    /* The Box - 45% width to leave space for center line */
    .timeline-box { width: 45%; position: relative; z-index: 1; }

    /* The Point / Dot in the center */
    .timeline-point {
        position: absolute; left: 50%; top: 20px; transform: translateX(-50%);
        width: 32px; height: 32px; background-color: #3b82f6;
        border-radius: 50%; border: 4px solid #0b1120; z-index: 2;
        display: flex; justify-content: center; align-items: center; color: white; font-size: 12px;
    }

    /* Box Styling */
    .timeline-content {
        background-color: #1e293b; padding: 25px; border-radius: 12px; border: 1px solid #334155;
    }
    
    /* Inside Box Layout (Always Left Aligned) */
    .exp-inner-flex { display: flex; gap: 20px; align-items: flex-start; }
    
    .exp-logo { width: 60px; height: 60px; border-radius: 10px; background: #fff; padding: 3px; object-fit: cover; flex-shrink: 0; }
    .exp-logo-placeholder { width: 60px; height: 60px; border-radius: 10px; background: #f1f5f9; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; color: #94a3b8; flex-shrink: 0; }
    
    .exp-details { flex-grow: 1; text-align: left; }
    .exp-title { font-size: 1.25rem; color: #f8fafc; margin: 0 0 5px 0; font-weight: 600; line-height: 1.3; }
    
    .exp-org { color: #10b981; font-weight: 500; font-size: 0.95rem; }
    .exp-website-link { color: #94a3b8; font-size: 0.85rem; background: rgba(255,255,255,0.05); padding: 3px 8px; border-radius: 5px; text-decoration: none; transition: 0.3s; }
    .exp-website-link:hover { background: #3b82f6; color: #fff; }
    
    .exp-meta { display: flex; gap: 15px; color: #94a3b8; font-size: 0.85rem; margin-top: 5px; flex-wrap: wrap; }
    .exp-meta span i { margin-right: 5px; }

    .exp-desc { color: #cbd5e1; font-size: 0.95rem; line-height: 1.6; margin: 0; }
    
    /* Example style Show More Button */
    .show-more-btn { background: none; border: none; color: #10b981; cursor: pointer; padding: 0; font-size: 0.9rem; font-weight: 500; margin-top: 10px; display: inline-flex; align-items: center; gap: 5px; }
    .show-more-btn:hover { color: #059669; }

    /* Mobile Responsive view */
    @media screen and (max-width: 768px) {
        .snake-timeline::after { left: 20px; }
        .timeline-row { justify-content: flex-end; }
        .timeline-row.left, .timeline-row.right { justify-content: flex-end; }
        .timeline-box { width: calc(100% - 50px); }
        .timeline-point { left: 20px; }
    }
    @media screen and (max-width: 480px) {
        .exp-inner-flex { flex-direction: column; gap: 15px; }
    }
</style>