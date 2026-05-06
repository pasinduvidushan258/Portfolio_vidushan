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
$edit_experience = null;

// Delete an existing experience entry
if (isset($_GET['action']) && $_GET['action'] === 'delete_experience' && isset($_GET['id'])) {
    $exp_id = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("DELETE FROM experience WHERE id = :id");
        $stmt->bindParam(':id', $exp_id, PDO::PARAM_INT);
        $stmt->execute();
        $success_msg = "Experience entry deleted successfully!";
    } catch (PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Load experience entry for editing
if (isset($_GET['action']) && $_GET['action'] === 'edit_experience' && isset($_GET['id'])) {
    $exp_id = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("SELECT * FROM experience WHERE id = :id");
        $stmt->bindParam(':id', $exp_id, PDO::PARAM_INT);
        $stmt->execute();
        $edit_experience = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Update an existing experience entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_experience'])) {
    $experience_id = intval($_POST['experience_id']);
    $exp_title = trim($_POST['exp_title']);
    $organization = trim($_POST['organization']);
    $duration = trim($_POST['duration']);
    $exp_description = trim($_POST['exp_description']);

    $sql = "UPDATE experience SET title = :title, organization = :organization, duration = :duration, description = :description";
    $params = [
        ':title' => $exp_title,
        ':organization' => $organization,
        ':duration' => $duration,
        ':description' => $exp_description,
        ':id' => $experience_id
    ];

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "../uploads/";
        $image_name = time() . "_logo_" . basename($_FILES["logo"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            $sql .= ", logo_path = :logo_path";
            $params[':logo_path'] = $image_name;
        } else {
            $error_msg = "Failed to upload logo.";
        }
    }

    if (empty($error_msg)) {
        try {
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $success_msg = "Experience updated successfully!";
            header("Location: manage_about.php");
            exit();
        } catch (PDOException $e) {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    }
}


// 1. Identity & Bio Update කිරීම
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

// 2. Add New Experience Entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_experience'])) {
    $exp_title = trim($_POST['exp_title']);
    $organization = trim($_POST['organization']);
    $duration = trim($_POST['duration']);
    $exp_description = trim($_POST['exp_description']);

    $image_query_part_cols = "";
    $image_query_part_vals = "";
    $image_name = null;

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "../uploads/";
        $image_name = time() . "_logo_" . basename($_FILES["logo"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            $image_query_part_cols = ", logo_path";
            $image_query_part_vals = ", :logo_path";
        } else {
            $error_msg = "Failed to upload logo.";
        }
    }

    if (empty($error_msg)) {
        try {
            $sql = "INSERT INTO experience (title, organization, duration, description" . $image_query_part_cols . ") 
                    VALUES (:title, :organization, :duration, :description" . $image_query_part_vals . ")";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':title', $exp_title);
            $stmt->bindParam(':organization', $organization);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':description', $exp_description);
            if ($image_name) {
                $stmt->bindParam(':logo_path', $image_name);
            }
            
            $stmt->execute();
            $success_msg = "Experience added successfully!";
        } catch(PDOException $e) {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    }
}

// Fetch existing identity and experience data for display
try {
    $stmt = $conn->prepare("SELECT * FROM about_identity WHERE id = 1");
    $stmt->execute();
    $identity = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$identity) {
        $identity = ['title' => '', 'description' => '', 'education_info' => '', 'languages' => ''];
    }

    $stmt_exp = $conn->prepare("SELECT * FROM experience ORDER BY id DESC");
    $stmt_exp->execute();
    $experiences = $stmt_exp->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_msg = "Error fetching data: " . $e->getMessage();
}

// Include the dashboard header
include 'header.php'; 
?>

<!-- Header Section -->
<div class="header">
    <div class="header-left">
        <h1>Manage About & Experience 🧑‍🎓</h1>
        <p>ඔබගේ පෞද්ගලික තොරතුරු සහ වෘත්තීය අත්දැකීම් මෙතැනින් කළමනාකරණය කරන්න.</p>
    </div>
</div>

<!-- Alerts -->
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

<!-- Section 1: Identity Form -->
<div class="form-container" style="margin-bottom: 30px;">
    <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Identity & Bio</h2>
    
    <!-- Identity Update Form -->
    <form action="" method="POST" enctype="multipart/form-data">
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

<!-- Section 2: Experience Form -->
<div class="form-container">
    <?php if ($edit_experience): ?>
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Edit Experience</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="experience_id" value="<?php echo htmlspecialchars($edit_experience['id']); ?>">
            <div class="input-group">
                <label>Job / Role Title</label>
                <input type="text" name="exp_title" value="<?php echo htmlspecialchars($edit_experience['title']); ?>" required>
            </div>

            <div class="input-group">
                <label>Organization / Company</label>
                <input type="text" name="organization" value="<?php echo htmlspecialchars($edit_experience['organization']); ?>" required>
            </div>

            <div class="input-group">
                <label>Duration / Date</label>
                <input type="text" name="duration" value="<?php echo htmlspecialchars($edit_experience['duration']); ?>" required>
            </div>

            <div class="input-group">
                <label>Experience Description</label>
                <textarea name="exp_description" rows="3" required><?php echo htmlspecialchars($edit_experience['description']); ?></textarea>
            </div>

            <div class="input-group">
                <label>Organization Logo (Optional)</label>
                <input type="file" name="logo" accept="image/*">
                <?php if (!empty($edit_experience['logo_path'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($edit_experience['logo_path']); ?>" alt="Current Logo" class="current-img">
                <?php endif; ?>
            </div>

            <button type="submit" name="update_experience" class="btn-submit" style="background: #10b981;"><i class="fas fa-save"></i> Update Experience</button>
            <a href="manage_about.php" class="btn-submit" style="background: #64748b; margin-left: 10px; text-decoration: none; display: inline-block;">Cancel</a>
        </form>
    <?php else: ?>
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Add New Experience</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>Job / Role Title</label>
                <input type="text" name="exp_title" placeholder="e.g. Social Media Marketer" required>
            </div>

            <div class="input-group">
                <label>Organization / Company</label>
                <input type="text" name="organization" placeholder="e.g. PSSF" required>
            </div>

            <div class="input-group">
                <label>Duration / Date</label>
                <input type="text" name="duration" placeholder="e.g. 2025 Jan - Present" required>
            </div>

            <div class="input-group">
                <label>Experience Description</label>
                <textarea name="exp_description" rows="3" placeholder="What did you do there?" required></textarea>
            </div>

            <div class="input-group">
                <label>Organization Logo (Optional)</label>
                <input type="file" name="logo" accept="image/*">
            </div>

            <button type="submit" name="add_experience" class="btn-submit" style="background: #10b981;"><i class="fas fa-plus"></i> Add Experience</button>
        </form>
    <?php endif; ?>
</div>

<div class="form-container" style="margin-top: 30px; overflow-x: auto;">
    <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Saved Experience Entries</h2>
    <?php if (!empty($experiences)): ?>
        <table style="width: 100%; border-collapse: collapse; color: #fff;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <th style="padding: 12px 10px; text-align: left;">Title</th>
                    <th style="padding: 12px 10px; text-align: left;">Organization</th>
                    <th style="padding: 12px 10px; text-align: left;">Duration</th>
                    <th style="padding: 12px 10px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($experiences as $experience): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($experience['title']); ?></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($experience['organization']); ?></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($experience['duration']); ?></td>
                        <td style="padding: 12px 10px;">
                            <a href="manage_about.php?action=edit_experience&id=<?php echo $experience['id']; ?>" style="color: #8b5cf6; margin-right: 15px;">Edit</a>
                            <a href="manage_about.php?action=delete_experience&id=<?php echo $experience['id']; ?>" style="color: #ef4444;" onclick="return confirm('Delete this experience entry?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #cbd5e1;">No experience records found yet.</p>
    <?php endif; ?>
</div>

<?php 
// Include the footer
include 'footer.php'; 
?>