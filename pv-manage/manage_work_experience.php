<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$msg = "";

// Add or Edit Experience
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_experience'])) {
    $id = $_POST['exp_id'] ?? '';
    $title = $_POST['title'];
    $organization = $_POST['organization'];
    $duration = $_POST['duration'];
    $location = $_POST['location']; 
    $description = $_POST['description'];
    $company_website = $_POST['company_website'];
    
    $logo_path = $_POST['existing_logo'] ?? '';

    // Image Upload Logic
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $upload_dir = '../uploads/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_extension = pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION);
        $new_filename = time() . "_logo." . $file_extension;
        
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $upload_dir . $new_filename)) {
            $logo_path = $new_filename;
        }
    }

    if (empty($id)) {
        // Insert New (Location එකත් එක්ක)
        $stmt = $conn->prepare("INSERT INTO experience (title, organization, duration, location, description, logo_path, company_website) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $organization, $duration, $location, $description, $logo_path, $company_website]);
        $msg = "Experience added successfully!";
    } else {
        // Update Existing (Location එකත් එක්ක)
        $stmt = $conn->prepare("UPDATE experience SET title=?, organization=?, duration=?, location=?, description=?, logo_path=?, company_website=? WHERE id=?");
        $stmt->execute([$title, $organization, $duration, $location, $description, $logo_path, $company_website, $id]);
        $msg = "Experience updated successfully!";
    }
}

// Delete Experience
if (isset($_GET['delete_id'])) {
    $conn->prepare("DELETE FROM experience WHERE id=?")->execute([$_GET['delete_id']]);
    header("Location: manage_work_experience.php");
    exit();
}

// Fetch for Edit
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM experience WHERE id=?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_data = $stmt->fetch();
}

$experiences = $conn->query("SELECT * FROM experience ORDER BY id DESC")->fetchAll();
include 'header.php';
?>

<div class="header">
    <h1>Manage Work Experience 💼</h1>
</div>

<?php if($msg): ?><div style="background: #10b981; color: #fff; padding: 15px; border-radius: 8px; margin-bottom: 20px;"><?php echo $msg; ?></div><?php endif; ?>

<div class="form-container" style="max-width: 100%; box-sizing: border-box; margin-bottom: 40px; background: #1e293b; padding: 30px; border-radius: 12px;">
    <h3 style="color: #f8fafc; margin-bottom: 20px;"><?php echo $edit_data ? "Edit Experience" : "Add New Experience"; ?></h3>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="exp_id" value="<?php echo $edit_data['id'] ?? ''; ?>">
        <input type="hidden" name="existing_logo" value="<?php echo $edit_data['logo_path'] ?? ''; ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
            <div>
                <label style="color: #cbd5e1; display:block; margin-bottom:5px;">Job Title</label>
                <input type="text" name="title" value="<?php echo $edit_data['title'] ?? ''; ?>" required style="width: 100%; padding: 10px; border-radius: 8px; background: #0f172a; border: 1px solid #334155; color: #fff;">
            </div>
            <div>
                <label style="color: #cbd5e1; display:block; margin-bottom:5px;">Company/Organization</label>
                <input type="text" name="organization" value="<?php echo $edit_data['organization'] ?? ''; ?>" required style="width: 100%; padding: 10px; border-radius: 8px; background: #0f172a; border: 1px solid #334155; color: #fff;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
            <div>
                <label style="color: #cbd5e1; display:block; margin-bottom:5px;">Duration (e.g. Jan 2023 - Present)</label>
                <input type="text" name="duration" value="<?php echo $edit_data['duration'] ?? ''; ?>" required style="width: 100%; padding: 10px; border-radius: 8px; background: #0f172a; border: 1px solid #334155; color: #fff;">
            </div>
            <div>
                <label style="color: #cbd5e1; display:block; margin-bottom:5px;">Location (e.g. Colombo, Sri Lanka)</label>
                <input type="text" name="location" value="<?php echo $edit_data['location'] ?? ''; ?>" style="width: 100%; padding: 10px; border-radius: 8px; background: #0f172a; border: 1px solid #334155; color: #fff;">
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="color: #cbd5e1; display:block; margin-bottom:5px;">Company Website URL</label>
            <input type="url" name="company_website" value="<?php echo $edit_data['company_website'] ?? ''; ?>" placeholder="https://..." style="width: 100%; padding: 10px; border-radius: 8px; background: #0f172a; border: 1px solid #334155; color: #fff;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="color: #cbd5e1; display:block; margin-bottom:5px;">Description</label>
            <textarea name="description" rows="4" required style="width: 100%; padding: 10px; border-radius: 8px; background: #0f172a; border: 1px solid #334155; color: #fff;"><?php echo $edit_data['description'] ?? ''; ?></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="color: #cbd5e1; display:block; margin-bottom:5px;">Company Logo (Square aspect ratio recommended)</label>
            <input type="file" name="logo" accept="image/*" style="color: #fff;">
            <?php if(!empty($edit_data['logo_path'])): ?>
                <p style="color: #10b981; font-size: 0.8rem; margin-top: 5px;">Current Logo: <?php echo $edit_data['logo_path']; ?></p>
            <?php endif; ?>
        </div>

        <button type="submit" name="save_experience" style="background: #6366f1; color: #fff; padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
            <?php echo $edit_data ? "Update Experience" : "Add Experience"; ?>
        </button>
        <?php if($edit_data): ?>
            <a href="manage_work_experience.php" style="color: #ef4444; margin-left: 15px; text-decoration: none;">Cancel Edit</a>
        <?php endif; ?>
    </form>
</div>

<div class="form-container" style="max-width: 100%; width: 100%; box-sizing: border-box;">
    <table style="width:100%; color:#fff; border-collapse:collapse; background: #1e293b; border-radius: 12px; overflow: hidden;">
        <tr style="background:#0f172a; text-align:left;">
            <th style="padding:15px;">Logo</th>
            <th style="padding:15px;">Role & Company</th>
            <th style="padding:15px;">Duration & Location</th>
            <th style="padding:15px; text-align: center;">Action</th>
        </tr>
        <?php foreach($experiences as $exp): ?>
        <tr style="border-bottom:1px solid #334155;">
            <td style="padding:15px;">
                <?php if($exp['logo_path']): ?>
                    <img src="../uploads/<?php echo $exp['logo_path']; ?>" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 50px; height: 50px; border-radius: 8px; background: #334155; display: flex; align-items: center; justify-content: center;"><i class="fas fa-image"></i></div>
                <?php endif; ?>
            </td>
            <td style="padding:15px;">
                <b><?php echo $exp['title']; ?></b><br>
                <span style="color: #10b981;"><?php echo $exp['organization']; ?></span>
            </td>
            <td style="padding:15px; color: #94a3b8; font-size: 0.9rem;">
                <i class="far fa-calendar-alt"></i> <?php echo $exp['duration']; ?><br>
                <?php if(!empty($exp['location'])): ?>
                    <span style="color: #f59e0b; margin-top: 5px; display: inline-block;"><i class="fas fa-map-marker-alt"></i> <?php echo $exp['location']; ?></span>
                <?php endif; ?>
            </td>
            <td style="padding:15px; text-align: center;">
                <a href="?edit_id=<?php echo $exp['id']; ?>" style="color:#3b82f6; margin-right: 15px;"><i class="fas fa-edit"></i></a>
                <a href="?delete_id=<?php echo $exp['id']; ?>" style="color:#ef4444;" onclick="return confirm('Delete this record?')"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include 'footer.php'; ?>