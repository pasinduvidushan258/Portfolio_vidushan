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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $role = trim($_POST['role']);
    $description = trim($_POST['description']);
    $skills = trim($_POST['skills']);

    $image_query_part = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_query_part = ", image_path = :image_path";
        } else {
            $error_msg = "Failed to upload image.";
        }
    }

    try {
        $sql = "UPDATE hero_section SET name = :name, role = :role, description = :description, skills = :skills" . $image_query_part . " WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':skills', $skills);
        if ($image_query_part != "") {
            $stmt->bindParam(':image_path', $image_name);
        }
        
        $stmt->execute();
        $success_msg = "Hero section updated successfully!";
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

try {
    $stmt = $conn->prepare("SELECT * FROM hero_section WHERE id = 1");
    $stmt->execute();
    $hero = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_msg = "Error fetching data: " . $e->getMessage();
}

// Include the dashboard header, sidebar, and stylesheet resources
include 'header.php'; 
?>

<div class="header">
    <div class="header-left">
        <h1>Manage Hero Section 🦸‍♂️</h1>
        <p>ඔබගේ වෙබ් අඩවියේ මුල් පිටුවේ පෙනෙන ප්‍රධාන විස්තර මෙතැනින් වෙනස් කරන්න.</p>
    </div>
</div>

<div class="form-container">
    <?php if($success_msg): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if($error_msg): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="input-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($hero['name'] ?? ''); ?>" required>
        </div>

        <div class="input-group">
            <label>Job Role (e.g. Full Stack Developer)</label>
            <input type="text" name="role" value="<?php echo htmlspecialchars($hero['role'] ?? ''); ?>" required>
        </div>

        <div class="input-group">
            <label>Short Description</label>
            <textarea name="description" required><?php echo htmlspecialchars($hero['description'] ?? ''); ?></textarea>
        </div>

        <div class="input-group">
            <label>Tech Skills (Comma separated - e.g. Java, Python, C#)</label>
            <input type="text" name="skills" value="<?php echo htmlspecialchars($hero['skills'] ?? ''); ?>" required>
        </div>

        <div class="input-group">
            <label>Profile Image</label>
            <input type="file" name="image" accept="image/*">
            <?php if(!empty($hero['image_path'])): ?>
                <div>
                    <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 5px;">Current Image:</p>
                    <img src="../uploads/<?php echo $hero['image_path']; ?>" alt="Hero Image" class="current-img">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Save Changes</button>
    </form>
</div>

<?php 
// Include the footer and close the page layout
include 'footer.php'; 
?>