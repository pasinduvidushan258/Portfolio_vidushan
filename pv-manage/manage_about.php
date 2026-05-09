<?php
ob_start();
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

// 1. Identity & Bio Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_identity'])) {
    $check_stmt = $conn->query("SELECT COUNT(*) FROM about_identity WHERE id = 1");
    if($check_stmt->fetchColumn() == 0) {
        $conn->query("INSERT INTO about_identity (id, title, description, education_info, languages) VALUES (1, '', '', '', '')");
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $education_info = trim($_POST['education_info']);
    $languages = trim($_POST['languages']);

    $img_query_part = "";
    $cv_query_part = "";

    // Profile Image Upload
    if (isset($_FILES['about_img']) && $_FILES['about_img']['error'] == 0) {
        $img_name = time() . "_" . basename($_FILES["about_img"]["name"]);
        if (move_uploaded_file($_FILES["about_img"]["tmp_name"], "../uploads/" . $img_name)) {
            $img_query_part = ", image_path = '$img_name'";
        }
    }

    // CV / Resume Upload
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] == 0) {
        $cv_dir = "../uploads/cv/";
        if (!is_dir($cv_dir)) mkdir($cv_dir, 0777, true);
        
        $cv_name = time() . "_" . basename($_FILES["cv_file"]["name"]);
        if (move_uploaded_file($_FILES["cv_file"]["tmp_name"], $cv_dir . $cv_name)) {
            $cv_query_part = ", cv_path = '$cv_name'";
        }
    }

    try {
        $sql = "UPDATE about_identity SET title = :title, description = :description, education_info = :education_info, languages = :languages" . $img_query_part . $cv_query_part . " WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':education_info', $education_info);
        $stmt->bindParam(':languages', $languages);
        $stmt->execute();
        $success_msg = "About Identity updated successfully!";
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Fetch existing identity data for display
try {
    $stmt = $conn->prepare("SELECT * FROM about_identity WHERE id = 1");
    $stmt->execute();
    $identity = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$identity) {
        $identity = ['title' => '', 'description' => '', 'education_info' => '', 'languages' => '', 'image_path' => ''];
    }
} catch(PDOException $e) {
    $error_msg = "Error fetching data: " . $e->getMessage();
}

// Include the dashboard header
include 'header.php'; 
?>

<div class="header">
    <div class="header-left">
        <h1>Manage About & Bio 🧑‍🎓</h1>
    </div>
</div>

<?php if($success_msg): ?>
    <div class="form-container" style="padding: 15px; margin-bottom: 20px;">
        <div class="alert alert-success" style="margin: 0;"><i class="fas fa-check-circle"></i> <?php echo $success_msg; ?></div>
    </div>
<?php endif; ?>
<?php if($error_msg): ?>
    <div class="form-container" style="padding: 15px; margin-bottom: 20px;">
        <div class="alert alert-danger" style="margin: 0;"><i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?></div>
    </div>
<?php endif; ?>

<div class="form-container" style="margin-bottom: 30px;">
    <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Identity & Bio</h2>
    
    <form action="" method="POST" enctype="multipart/form-data" id="editForm">
        <div class="input-group">
            <label>About Section Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($identity['title'] ?? ''); ?>" placeholder="e.g. About Pasindu Vidushan" required>
        </div>

        <div class="input-group">
            <label>Professional Bio (Description)</label>
            <textarea name="description" rows="4" required><?php echo htmlspecialchars($identity['description'] ?? ''); ?></textarea>
        </div>

        <div class="input-group">
            <label>Education Details</label>
            <textarea name="education_info" rows="3" required><?php echo htmlspecialchars($identity['education_info'] ?? ''); ?></textarea>
        </div>

        <div class="input-group">
            <label>Languages Known</label>
            <input type="text" name="languages" value="<?php echo htmlspecialchars($identity['languages'] ?? ''); ?>" placeholder="e.g. Sinhala, English" required>
        </div>

        <div class="input-group">
            <label>About Profile Image</label>
            <input type="file" name="about_img" accept="image/*">
            <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 5px;">* ලස්සන පෙනුමක් ගන්න හතරැස් (Square) පින්තූරයක් දාන්න.</p>
        </div>

        <div class="input-group">
            <label>Upload CV / Resume (PDF)</label>
            <input type="file" name="cv_file" accept=".pdf,.doc,.docx">
        </div>
        
        <button type="submit" name="update_identity" class="btn-submit"><i class="fas fa-save"></i> Save Identity</button>
    </form>
</div>

<div class="form-container" style="overflow-x: auto;">
    <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Current Saved Info</h2>
    
    <table style="width:100%; color:#fff; border-collapse:collapse; background: #1e293b; border-radius: 12px; overflow: hidden;">
        <tr style="background:#0f172a; text-align:left;">
            <th style="padding:15px; width: 15%;">Profile Image</th>
            <th style="padding:15px; width: 25%;">Title & Languages</th>
            <th style="padding:15px; width: 45%;">Bio Description</th>
            <th style="padding:15px; width: 15%; text-align: center;">Action</th>
        </tr>
        <tr style="border-bottom:1px solid #334155;">
            <td style="padding:15px; vertical-align: top;">
                <?php if(!empty($identity['image_path'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($identity['image_path']); ?>" style="width: 70px; height: 70px; border-radius: 8px; object-fit: cover; border: 2px solid #334155;">
                <?php else: ?>
                    <div style="width: 70px; height: 70px; border-radius: 8px; background: #334155; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;"><i class="fas fa-user"></i></div>
                <?php endif; ?>
            </td>
            <td style="padding:15px; vertical-align: top;">
                <b style="color: #f8fafc; font-size: 1.05rem;"><?php echo htmlspecialchars($identity['title']); ?></b><br>
                <div style="margin-top: 8px;">
                    <span style="color: #10b981; font-size: 0.9rem; display: block;"><i class="fas fa-language"></i> <?php echo htmlspecialchars($identity['languages']); ?></span>
                </div>
            </td>
            <td style="padding:15px; vertical-align: top;">
                <div style="max-height: 80px; overflow-y: auto; color: #cbd5e1; font-size: 0.9rem; line-height: 1.6; padding-right: 10px;">
                    <?php echo nl2br(htmlspecialchars($identity['description'])); ?>
                </div>
            </td>
            <td style="padding:15px; text-align: center; vertical-align: middle;">
                <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); document.querySelector('input[name=\'title\']').focus(); return false;" style="color:#3b82f6; background: rgba(59, 130, 246, 0.1); padding: 10px 15px; border-radius: 8px; text-decoration: none; transition: 0.3s; display: inline-block;">
                    <i class="fas fa-edit"></i> Edit Info
                </a>
            </td>
        </tr>
    </table>
</div>

<?php 
// Include the footer
include 'footer.php'; 
?>