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
    $stmt = $conn->prepare("DELETE FROM certifications WHERE id = ?");
    if ($stmt->execute([$_GET['delete_id']])) { 
        $success_msg = "Certification record deleted successfully!"; 
    }
}

// 2. ADD OR UPDATE ACTION
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_cert'])) {
    $cert_id = $_POST['cert_id']; 
    $cert_title = trim($_POST['cert_title']);
    $provider = trim($_POST['provider']);
    $issue_date = trim($_POST['issue_date']);
    $credential_id = trim($_POST['credential_id']);
    $credential_url = trim($_POST['credential_url']);
    $description = trim($_POST['description']);
    $skills = trim($_POST['skills']);

    $logo_name = $_FILES['logo_path']['name'];
    $logo_updated = false;

    if (!empty($logo_name)) {
        $target_dir = "../uploads/certifications/";
        $target_file = $target_dir . basename($logo_name);
        move_uploaded_file($_FILES['logo_path']['tmp_name'], $target_file);
        $logo_updated = true;
    }

    try {
        if (!empty($cert_id)) {
            // ----- UPDATE -----
            if ($logo_updated) {
                $stmt = $conn->prepare("UPDATE certifications SET cert_title=?, provider=?, logo_path=?, issue_date=?, credential_id=?, credential_url=?, description=?, skills=? WHERE id=?");
                $stmt->execute([$cert_title, $provider, $logo_name, $issue_date, $credential_id, $credential_url, $description, $skills, $cert_id]);
            } else {
                $stmt = $conn->prepare("UPDATE certifications SET cert_title=?, provider=?, issue_date=?, credential_id=?, credential_url=?, description=?, skills=? WHERE id=?");
                $stmt->execute([$cert_title, $provider, $issue_date, $credential_id, $credential_url, $description, $skills, $cert_id]);
            }
            $success_msg = "Certification updated successfully!";
        } else {
            // ----- ADD NEW -----
            if ($logo_updated) {
                $stmt = $conn->prepare("INSERT INTO certifications (cert_title, provider, logo_path, issue_date, credential_id, credential_url, description, skills) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$cert_title, $provider, $logo_name, $issue_date, $credential_id, $credential_url, $description, $skills]);
                $success_msg = "Certification added successfully!";
            } else {
                $error_msg = "Provider Logo is required for new records!";
            }
        }
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
    }
}

// 3. EDIT MODE DATA FETCH
$edit_cert = null;
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM certifications WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_cert = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 4. FETCH ALL DATA
$all_certs = $conn->query("SELECT * FROM certifications ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="header">
    <h1>Manage Certifications 🏆</h1>
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

<div class="form-container" style="margin-bottom: 40px; border: <?php echo $edit_cert ? '2px solid #3b82f6' : 'none'; ?>;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom:20px;">
        <h2 style="color:#fff; margin:0;"><?php echo $edit_cert ? '✏️ Edit Certification' : '🚀 Add New Certification'; ?></h2>
        <?php if($edit_cert): ?>
            <a href="manage_certifications.php" style="background: #64748b; color: #fff; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9rem;">Cancel Edit</a>
        <?php endif; ?>
    </div>

    <form action="manage_certifications.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="cert_id" value="<?php echo $edit_cert ? $edit_cert['id'] : ''; ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="input-group">
                <label>Certification Title</label>
                <input type="text" name="cert_title" required placeholder="e.g. AWS Certified Solutions Architect" value="<?php echo $edit_cert ? htmlspecialchars($edit_cert['cert_title']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>Provider / Organization</label>
                <input type="text" name="provider" required placeholder="e.g. Amazon Web Services" value="<?php echo $edit_cert ? htmlspecialchars($edit_cert['provider']) : ''; ?>">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="input-group">
                <label>Issue Date</label>
                <input type="text" name="issue_date" required placeholder="e.g. Issued Aug 2023" value="<?php echo $edit_cert ? htmlspecialchars($edit_cert['issue_date']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>Credential ID</label>
                <input type="text" name="credential_id" placeholder="e.g. AWS-12345678" value="<?php echo $edit_cert ? htmlspecialchars($edit_cert['credential_id']) : ''; ?>">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="input-group">
                <label>Provider Logo <?php echo $edit_cert ? '<span style="color:#f59e0b; font-size:0.8rem;">(Leave empty to keep current)</span>' : ''; ?></label>
                <input type="file" name="logo_path" <?php echo $edit_cert ? '' : 'required'; ?> style="color:#94a3b8; background: #0f172a; padding: 10px; border-radius: 8px;">
                <?php if($edit_cert && !empty($edit_cert['logo_path'])): ?>
                    <div style="margin-top: 10px;"><img src="../uploads/certifications/<?php echo $edit_cert['logo_path']; ?>" width="60" style="border-radius: 5px; background: #fff; padding: 5px;"></div>
                <?php endif; ?>
            </div>
            <div class="input-group">
                <label>Credential URL</label>
                <input type="url" name="credential_url" placeholder="https://verify.cert.com/..." value="<?php echo $edit_cert ? htmlspecialchars($edit_cert['credential_url']) : ''; ?>">
            </div>
        </div>

        <div class="input-group">
            <label>Description</label>
            <textarea name="description" rows="3" required placeholder="Briefly describe what you learned..."><?php echo $edit_cert ? htmlspecialchars($edit_cert['description']) : ''; ?></textarea>
        </div>

        <div class="input-group">
            <label>Skills / Tags (Separate by commas)</label>
            <input type="text" name="skills" placeholder="e.g. Cloud Computing, Architecture, Security" value="<?php echo $edit_cert ? htmlspecialchars($edit_cert['skills']) : ''; ?>">
        </div>

        <button type="submit" name="save_cert" class="btn-submit" style="margin-top: 20px; background: <?php echo $edit_cert ? '#3b82f6' : '#10b981'; ?>;">
            <i class="fas <?php echo $edit_cert ? 'fa-save' : 'fa-plus'; ?>"></i> <?php echo $edit_cert ? 'Update Certification' : 'Save Certification'; ?>
        </button>
    </form>
</div>

<div class="form-container">
    <h2 style="color:#fff; margin-bottom:20px;">📋 Existing Certifications</h2>
    <div style="overflow-x: auto;">
        <table style="width: 100%; color: #fff; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #334155; text-align: left; background: #0f172a;">
                    <th style="padding:12px;">Logo</th>
                    <th style="padding:12px;">Certification & Provider</th>
                    <th style="padding:12px;">Issue Date</th>
                    <th style="padding:12px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($all_certs as $cert): ?>
                <tr style="border-bottom: 1px solid #1e293b; background: <?php echo ($edit_cert && $edit_cert['id'] == $cert['id']) ? 'rgba(59, 130, 246, 0.05)' : 'transparent'; ?>;">
                    <td style="padding:12px;">
                        <img src="../uploads/certifications/<?php echo $cert['logo_path']; ?>" width="50" style="border-radius: 5px; background: #fff; padding: 5px; object-fit: contain;">
                    </td>
                    <td style="padding:12px;">
                        <div style="font-weight: bold; color: #818cf8;"><?php echo $cert['cert_title']; ?></div>
                        <span style="font-size: 0.85rem; color: #cbd5e1;"><?php echo $cert['provider']; ?></span>
                    </td>
                    <td style="padding:12px; font-size: 0.9rem;"><span style="background: rgba(255,255,255,0.1); padding: 4px 8px; border-radius: 4px;"><?php echo $cert['issue_date']; ?></span></td>
                    <td style="padding:12px;">
                        <div style="display: flex; gap: 10px;">
                            <a href="manage_certifications.php?edit_id=<?php echo $cert['id']; ?>" style="color:#3b82f6; background: rgba(59, 130, 246, 0.1); padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: bold;"><i class="fas fa-edit"></i> Edit</a>
                            <a href="?delete_id=<?php echo $cert['id']; ?>" style="color:#ef4444; background: rgba(239, 68, 68, 0.1); padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: bold;" onclick="return confirm('Delete this certification?');"><i class="fas fa-trash"></i> Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($all_certs)): ?>
                <tr><td colspan="4" style="text-align: center; padding: 20px; color: #94a3b8;">No certifications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>