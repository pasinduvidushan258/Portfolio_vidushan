<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$msg = "";

// 1. Logic for updating social media integration links
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_social'])) {
    $github = trim($_POST['github']);
    $linkedin = trim($_POST['linkedin']);
    $whatsapp = trim($_POST['whatsapp']);
    $youtube = trim($_POST['youtube']);
    $instagram = trim($_POST['instagram']);
    $facebook = trim($_POST['facebook']);

    try {
        $stmt = $conn->prepare("UPDATE social_links SET github=?, linkedin=?, whatsapp=?, youtube=?, instagram=?, facebook=? WHERE id=1");
        $stmt->execute([$github, $linkedin, $whatsapp, $youtube, $instagram, $facebook]);
        $msg = "Social Media Links updated successfully!";
    } catch(PDOException $e) {
        $msg = "Error updating links!";
    }
}

// 2. Data aggregation for dashboard statistics
try {
    // Count total projects (Adjust table name if your schema differs)
    $total_projects = $conn->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    
    // Aggregate total contact message count
    $total_messages = $conn->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    
    // Fetch count of new messages received within the current date
    $new_messages = $conn->query("SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()")->fetchColumn();

    // Retrieve current social media link configurations
    $social = $conn->query("SELECT * FROM social_links WHERE id=1")->fetch(PDO::FETCH_ASSOC);
    if(!$social) {
        $social = ['github'=>'', 'linkedin'=>'', 'whatsapp'=>'', 'youtube'=>'', 'instagram'=>'', 'facebook'=>''];
    }

} catch(PDOException $e) {
    $total_projects = 0; $total_messages = 0; $new_messages = 0;
}

// Include sidebar and stylesheet resources for the dashboard
include 'header.php'; 
?>

<div class="header">
    <div class="header-left">
        <h1>Welcome Vidushan! 🚀</h1>
        <p>Manage your entire portfolio dynamically from here.</p>
    </div>
    <div class="date-time">
        <p style="color: #94a3b8;"><i class="fas fa-calendar-alt"></i> <?php echo date('F d, Y'); ?></p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-project-diagram"></i>
        <h3><?php echo $total_projects; ?></h3>
        <p>Total Projects</p>
    </div>
    
    <div class="stat-card" style="position: relative;">
        <?php if($new_messages > 0): ?>
            <div class="new-message-badge">
                <?php echo $new_messages; ?> New
            </div>
        <?php endif; ?>
        
        <i class="fas fa-envelope"></i>
        <h3><?php echo $total_messages; ?></h3>
        <p>Total Messages</p>
    </div>
</div>

<?php if($msg): ?>
    <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(16, 185, 129, 0.2);">
        <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="form-container" style="max-width: 100%; width: 100%; box-sizing: border-box; background: #1e293b; padding: 30px; border-radius: 15px; border: 1px solid #334155; margin-bottom: 40px;">
    <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">
        <i class="fas fa-share-alt"></i> Manage Social Media Links
    </h2>
    
    <form action="" method="POST">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="input-group">
                <label><i class="fab fa-github"></i> GitHub URL</label>
                <input type="url" name="github" value="<?php echo htmlspecialchars($social['github']); ?>" placeholder="https://github.com/...">
            </div>
            <div class="input-group">
                <label><i class="fab fa-linkedin" style="color: #0a66c2;"></i> LinkedIn URL</label>
                <input type="url" name="linkedin" value="<?php echo htmlspecialchars($social['linkedin']); ?>" placeholder="https://linkedin.com/in/...">
            </div>
            <div class="input-group">
                <label><i class="fab fa-whatsapp" style="color: #25d366;"></i> WhatsApp Link</label>
                <input type="url" name="whatsapp" value="<?php echo htmlspecialchars($social['whatsapp']); ?>" placeholder="https://wa.me/...">
            </div>
            <div class="input-group">
                <label><i class="fab fa-youtube" style="color: #ff0000;"></i> YouTube URL</label>
                <input type="url" name="youtube" value="<?php echo htmlspecialchars($social['youtube']); ?>" placeholder="https://youtube.com/...">
            </div>
            <div class="input-group">
                <label><i class="fab fa-instagram" style="color: #e1306c;"></i> Instagram URL</label>
                <input type="url" name="instagram" value="<?php echo htmlspecialchars($social['instagram']); ?>" placeholder="https://instagram.com/...">
            </div>
            <div class="input-group">
                <label><i class="fab fa-facebook" style="color: #1877f2;"></i> Facebook URL</label>
                <input type="url" name="facebook" value="<?php echo htmlspecialchars($social['facebook']); ?>" placeholder="https://facebook.com/...">
            </div>
        </div>
        
        <button type="submit" name="update_social" class="btn-submit" style="margin-top: 20px; width: auto;"><i class="fas fa-save"></i> Save Links</button>
    </form>
</div>

<style>
    /* Notification UI: New message alert badge styling */
    .new-message-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #ef4444;
        color: #fff;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    /* Form Layout: Input group iconography and spacing */
    .input-group label i { margin-right: 8px; font-size: 1.1rem; }
</style>

<?php 
// Include footer markup and close the page layout
include 'footer.php'; 
?>