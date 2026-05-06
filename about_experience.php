<?php
require_once 'includes/db_connect.php';

$stmt_exp = $conn->prepare("SELECT * FROM experience ORDER BY id DESC");
$stmt_exp->execute();
$experiences = $stmt_exp->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="experience-section">
    <div class="skills-header" style="text-align: center; margin-bottom: 40px;">
        <p class="sub-title" style="color: #94a3b8; font-size: 1rem;">My Journey</p>
        <h2 class="main-title" style="font-size: 2.5rem; color: #818cf8; font-weight: 600;">Work & Experience</h2>
    </div>

    <div class="timeline-container">
        <?php foreach($experiences as $exp): ?>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="exp-header">
                        <h3 class="exp-title">
                            <?php echo htmlspecialchars($exp['title']); ?> 
                            <span class="exp-org">@ <?php echo htmlspecialchars($exp['organization']); ?></span>
                        </h3>
                        <span class="exp-duration"><i class="far fa-calendar-alt"></i> <?php echo htmlspecialchars($exp['duration']); ?></span>
                    </div>
                    <p class="exp-desc"><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
    .experience-section {
        padding: 50px 20px;
        background-color: #0b1120;
        color: #fff;
        font-family: 'Poppins', sans-serif;
    }
    .timeline-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
        padding-left: 20px;
    }
    
    .timeline-container::before {
        content: '';
        position: absolute;
        left: 26px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #334155;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        padding-left: 40px;
    }
    .timeline-dot {
        position: absolute;
        left: 0;
        top: 5px;
        width: 14px;
        height: 14px;
        background-color: #818cf8;
        border-radius: 50%;
        border: 4px solid #0b1120;
        box-shadow: 0 0 0 2px #818cf8;
        z-index: 1;
    }
    .timeline-content {
        background-color: #1e293b;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
    }
    .timeline-content:hover {
        transform: translateY(-5px);
    }
    .exp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 10px;
        border-bottom: 1px solid #334155;
        padding-bottom: 10px;
    }
    .exp-title {
        font-size: 1.2rem;
        color: #f8fafc;
        margin: 0;
    }
    .exp-org {
        color: #818cf8;
        font-weight: 500;
    }
    .exp-duration {
        color: #94a3b8;
        font-size: 0.9rem;
        background: #0f172a;
        padding: 5px 12px;
        border-radius: 20px;
    }
    .exp-desc {
        color: #cbd5e1;
        font-size: 1rem;
        line-height: 1.6;
        margin: 0;
    }
</style>