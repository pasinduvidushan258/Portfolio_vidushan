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

// 1. DELETE ACTION
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM education WHERE id = ?");
    if ($stmt->execute([$_GET['delete_id']])) { 
        $success_msg = "Education record deleted successfully!"; 
    }
}

// 2. ADD OR UPDATE ACTION
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_education'])) {
    $edu_id = $_POST['edu_id']; 
    $degree_title = trim($_POST['degree_title']);
    $institution = trim($_POST['institution']);
    $duration = trim($_POST['duration']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $skills = trim($_POST['skills']);

    $logo_name = $_FILES['logo_path']['name'];
    $logo_updated = false;

    if (!empty($logo_name)) {
        $target_dir = "../uploads/education/";
        $target_file = $target_dir . basename($logo_name);
        move_uploaded_file($_FILES['logo_path']['tmp_name'], $target_file);
        $logo_updated = true;
    }

    try {
        if (!empty($edu_id)) {
            // ----- UPDATE -----
            if ($logo_updated) {
                $stmt = $conn->prepare("UPDATE education SET degree_title=?, institution=?, logo_path=?, duration=?, location=?, description=?, skills=? WHERE id=?");
                $stmt->execute([$degree_title, $institution, $logo_name, $duration, $location, $description, $skills, $edu_id]);
            } else {
                $stmt = $conn->prepare("UPDATE education SET degree_title=?, institution=?, duration=?, location=?, description=?, skills=? WHERE id=?");
                $stmt->execute([$degree_title, $institution, $duration, $location, $description, $skills, $edu_id]);
            }
            $success_msg = "Education record updated successfully!";
        } else {
            // ----- ADD NEW -----
            if ($logo_updated) {
                $stmt = $conn->prepare("INSERT INTO education (degree_title, institution, logo_path, duration, location, description, skills) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$degree_title, $institution, $logo_name, $duration, $location, $description, $skills]);
                $success_msg = "Education record added successfully!";
            } else {
                $error_msg = "Institution Logo is required for new records!";
            }
        }
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
    }
}

// 3. EDIT MODE DATA FETCH
$edit_edu = null;
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM education WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_edu = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 4. FETCH ALL DATA
$all_education = $conn->query("SELECT * FROM education ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="header">
    <h1>Manage Education 🎓</h1>
</div>

<?php if($success_msg): ?>
    <div style="background: #10b981; color: #fff; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
    </div>
<?php endif; ?>
<?php if($error_msg): ?>
    <div style="background: #ef4444; color: #fff; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error_msg; ?>
    </div>
<?php endif; ?>

<div class="form-container" style="margin-bottom: 40px; border: <?php echo $edit_edu ? '2px solid #3b82f6' : 'none'; ?>;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom:20px;">
        <h2 style="color:#fff; margin:0;"><?php echo $edit_edu ? '✏️ Edit Education' : '🚀 Add New Education'; ?></h2>
        <?php if($edit_edu): ?>
            <a href="manage_education.php" style="background: #64748b; color: #fff; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9rem;">Cancel Edit</a>
        <?php endif; ?>
    </div>

    <form action="manage_education.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edu_id" value="<?php echo $edit_edu ? $edit_edu['id'] : ''; ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="input-group">
                <label>Degree / Course Title</label>
                <input type="text" name="degree_title" required placeholder="e.g. BSc (Hons) in Computer Science" value="<?php echo $edit_edu ? htmlspecialchars($edit_edu['degree_title']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>Institution Name</label>
                <input type="text" name="institution" required placeholder="e.g. University of Colombo" value="<?php echo $edit_edu ? htmlspecialchars($edit_edu['institution']) : ''; ?>">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="input-group">
                <label>Duration</label>
                <input type="text" name="duration" required placeholder="e.g. 2020 - 2024" value="<?php echo $edit_edu ? htmlspecialchars($edit_edu['duration']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>Location</label>
                <input type="text" name="location" required placeholder="e.g. Colombo, Sri Lanka" value="<?php echo $edit_edu ? htmlspecialchars($edit_edu['location']) : ''; ?>">
            </div>
        </div>

        <div class="input-group">
            <label>Institution Logo <?php echo $edit_edu ? '<span style="color:#f59e0b; font-size:0.8rem;">(Leave empty to keep current)</span>' : ''; ?></label>
            <input type="file" name="logo_path" <?php echo $edit_edu ? '' : 'required'; ?> style="color:#94a3b8; background: #0f172a; padding: 10px; border-radius: 8px;">
            <?php if($edit_edu && !empty($edit_edu['logo_path'])): ?>
                <div style="margin-top: 10px;"><img src="../uploads/education/<?php echo $edit_edu['logo_path']; ?>" width="60" style="border-radius: 5px; background: #fff; padding: 5px;"></div>
            <?php endif; ?>
        </div>

        <div class="input-group">
            <label>Description</label>
            <textarea name="description" rows="3" required placeholder="Briefly describe your studies..."><?php echo $edit_edu ? htmlspecialchars($edit_edu['description']) : ''; ?></textarea>
        </div>

        <div class="input-group">
            <label>Skills / Tags (Separate by commas)</label>
            <input type="text" name="skills" placeholder="e.g. Programming, Networking, Web Development" value="<?php echo $edit_edu ? htmlspecialchars($edit_edu['skills']) : ''; ?>">
        </div>

        <button type="submit" name="save_education" class="btn-submit" style="margin-top: 20px; background: <?php echo $edit_edu ? '#3b82f6' : '#10b981'; ?>;">
            <i class="fas <?php echo $edit_edu ? 'fa-save' : 'fa-plus'; ?>"></i> <?php echo $edit_edu ? 'Update Record' : 'Save Record'; ?>
        </button>
    </form>
</div>

<div class="form-container">
    <h2 style="color:#fff; margin-bottom:20px;">📋 Existing Education Records</h2>
    <div style="overflow-x: auto;">
        <table style="width: 100%; color: #fff; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #334155; text-align: left; background: #0f172a;">
                    <th style="padding:12px;">Logo</th>
                    <th style="padding:12px;">Degree & Institution</th>
                    <th style="padding:12px;">Duration</th>
                    <th style="padding:12px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($all_education as $edu): ?>
                <tr style="border-bottom: 1px solid #1e293b; background: <?php echo ($edit_edu && $edit_edu['id'] == $edu['id']) ? 'rgba(59, 130, 246, 0.05)' : 'transparent'; ?>;">
                    <td style="padding:12px;">
                        <img src="../uploads/education/<?php echo $edu['logo_path']; ?>" width="50" style="border-radius: 5px; background: #fff; padding: 5px; object-fit: contain;">
                    </td>
                    <td style="padding:12px;">
                        <div style="font-weight: bold; color: #818cf8;"><?php echo $edu['degree_title']; ?></div>
                        <span style="font-size: 0.85rem; color: #cbd5e1;"><?php echo $edu['institution']; ?></span>
                    </td>
                    <td style="padding:12px; font-size: 0.9rem;"><span style="background: rgba(255,255,255,0.1); padding: 4px 8px; border-radius: 4px;"><?php echo $edu['duration']; ?></span></td>
                    <td style="padding:12px;">
                        <div style="display: flex; gap: 10px;">
                            <a href="manage_education.php?edit_id=<?php echo $edu['id']; ?>" style="color:#3b82f6; background: rgba(59, 130, 246, 0.1); padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: bold;"><i class="fas fa-edit"></i> Edit</a>
                            <a href="?delete_id=<?php echo $edu['id']; ?>" style="color:#ef4444; background: rgba(239, 68, 68, 0.1); padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: bold;" onclick="return confirm('Delete this record?');"><i class="fas fa-trash"></i> Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($all_education)): ?>
                <tr><td colspan="4" style="text-align: center; padding: 20px; color: #94a3b8;">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>