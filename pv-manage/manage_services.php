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
$edit_service = null;

// Delete Service
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("DELETE FROM services WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $success_msg = "Service deleted successfully!";
    } catch (PDOException $e) {
        $error_msg = "Error deleting: " . $e->getMessage();
    }
}

// Edit Mode Load
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $edit_service = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Add New Service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $icon_class = ($_POST['icon_dropdown'] === 'custom') ? trim($_POST['custom_icon']) : trim($_POST['icon_dropdown']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (empty($icon_class)) {
        $error_msg = "Please select or enter an icon class.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO services (icon_class, title, description) VALUES (:icon_class, :title, :description)");
            $stmt->bindParam(':icon_class', $icon_class);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->execute();
            $success_msg = "New service added successfully!";
        } catch(PDOException $e) {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    }
}
// Update Existing Service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_service'])) {
    $id = intval($_POST['service_id']);
    $icon_class = ($_POST['icon_dropdown'] === 'custom') ? trim($_POST['custom_icon']) : trim($_POST['icon_dropdown']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (empty($icon_class)) {
        $error_msg = "Please select or enter an icon class.";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE services SET icon_class = :icon_class, title = :title, description = :description WHERE id = :id");
            $stmt->bindParam(':icon_class', $icon_class);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $success_msg = "Service updated successfully!";
            header("Location: manage_services.php");
            exit();
        } catch(PDOException $e) {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    }
}

// Fetch all services
$stmt = $conn->prepare("SELECT * FROM services ORDER BY id DESC");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// FontAwesome Icons List
$fa_icons = [
    'fas fa-laptop-code' => 'Web Development (Laptop Code)',
    'fas fa-robot' => 'AI / Machine Learning (Robot)',
    'fas fa-paint-brush' => 'Design / UI UX (Paint Brush)',
    'fas fa-bullhorn' => 'Marketing / SEO (Bullhorn)',
    'fas fa-database' => 'Database / SQL (Database)',
    'fas fa-server' => 'Server / Cloud (Server)',
    'fas fa-mobile-alt' => 'Mobile Apps (Mobile)',
    'fas fa-chart-line' => 'Data / Analytics (Chart Line)',
    'fas fa-shield-alt' => 'Cyber Security (Shield)',
    'fas fa-cogs' => 'Settings / DevOps (Gears)'
];

include 'header.php'; 
?>

<div class="header">
    <div class="header-left">
        <h1>Manage Services 🚀</h1>
        
    </div>
</div>

<?php if($success_msg): ?>
    <div class="form-container" style="padding: 15px; margin-bottom: 20px;"><div class="alert alert-success" style="margin: 0;"><i class="fas fa-check-circle"></i> <?php echo $success_msg; ?></div></div>
<?php endif; ?>
<?php if($error_msg): ?>
    <div class="form-container" style="padding: 15px; margin-bottom: 20px;"><div class="alert alert-danger" style="margin: 0;"><i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?></div></div>
<?php endif; ?>

<div class="form-container" style="margin-bottom: 30px;">
    <?php if ($edit_service): ?>
        <!-- ================= EDIT MODE ================= -->
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Edit Service</h2>
        <form action="" method="POST">
            <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($edit_service['id']); ?>">
            
            <div class="input-group">
                <label>Service Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_service['title']); ?>" required>
            </div>
            
            <?php 
                $is_custom_edit = !array_key_exists($edit_service['icon_class'], $fa_icons);
            ?>

            <div class="input-group">
                <label>Select Icon <i id="edit-icon-preview" class="<?php echo htmlspecialchars($edit_service['icon_class']); ?>" style="margin-left: 10px; color: #818cf8; font-size: 1.2rem;"></i></label>
                <select name="icon_dropdown" id="edit-icon-dropdown" style="width: 100%; padding: 12px; background-color: #0f172a; color: #fff; border: 1px solid #334155; border-radius: 8px; font-size: 1rem; cursor: pointer;">
                    <?php foreach($fa_icons as $class => $label): ?>
                        <option value="<?php echo $class; ?>" <?php echo (!$is_custom_edit && $edit_service['icon_class'] == $class) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="custom" style="color: #10b981; font-weight: bold;" <?php echo ($is_custom_edit) ? 'selected' : ''; ?>>
                        ✏️ Type Custom Icon Code...
                    </option>
                </select>
            </div>

            <!-- Custom Icon Input -->
            <div class="input-group" id="edit-custom-icon-box" style="display: <?php echo ($is_custom_edit) ? 'block' : 'none'; ?>; background: rgba(16, 185, 129, 0.05); padding: 15px; border-radius: 8px; border-left: 3px solid #10b981; margin-top: 10px;">
                <label style="color: #10b981;">Type FontAwesome Class (e.g., fas fa-laptop-code)</label>
                <input type="text" name="custom_icon" id="edit-custom-input" value="<?php echo ($is_custom_edit) ? htmlspecialchars($edit_service['icon_class']) : ''; ?>" placeholder="fas fa-code">
            </div>

            <div class="input-group" style="margin-top: 20px;">
                <label>Service Description</label>
                <textarea name="description" rows="3" required><?php echo htmlspecialchars($edit_service['description']); ?></textarea>
            </div>
            <button type="submit" name="update_service" class="btn-submit" style="background: #10b981;"><i class="fas fa-save"></i> Update Service</button>
            <a href="manage_services.php" class="btn-submit" style="background: #64748b; margin-left: 10px; text-decoration: none; display: inline-block;">Cancel</a>
        </form>

    <?php else: ?>
        <!-- ================= ADD MODE ================= -->
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Add New Service</h2>
        <form action="" method="POST">
            <div class="input-group">
                <label>Service Title</label>
                <input type="text" name="title" placeholder="e.g. Web Development" required>
            </div>
            
            <div class="input-group">
                <label>Select Icon <i id="add-icon-preview" class="fas fa-laptop-code" style="margin-left: 10px; color: #818cf8; font-size: 1.2rem;"></i></label>
                <select name="icon_dropdown" id="add-icon-dropdown" style="width: 100%; padding: 12px; background-color: #0f172a; color: #fff; border: 1px solid #334155; border-radius: 8px; font-size: 1rem; cursor: pointer;">
                    <?php foreach($fa_icons as $class => $label): ?>
                        <option value="<?php echo $class; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                    <option value="custom" style="color: #10b981; font-weight: bold;">✏️ Type Custom Icon Code...</option>
                </select>
            </div>

            <!-- Custom Icon Input -->
            <div class="input-group" id="add-custom-icon-box" style="display: none; background: rgba(16, 185, 129, 0.05); padding: 15px; border-radius: 8px; border-left: 3px solid #10b981; margin-top: 10px;">
                <label style="color: #10b981;">Type FontAwesome Class (e.g., fas fa-laptop-code)</label>
                <input type="text" name="custom_icon" id="add-custom-input" placeholder="fas fa-code">
            </div>

            <div class="input-group" style="margin-top: 20px;">
                <label>Service Description</label>
                <textarea name="description" rows="3" placeholder="Brief description of the service..." required></textarea>
            </div>
            <button type="submit" name="add_service" class="btn-submit"><i class="fas fa-plus"></i> Add Service</button>
        </form>
    <?php endif; ?>
</div>

<div class="form-container" style="overflow-x: auto;">
    <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Current Services</h2>
    <?php if (!empty($services)): ?>
        <table style="width: 100%; border-collapse: collapse; color: #fff;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <th style="padding: 12px 10px; text-align: left;">Icon</th>
                    <th style="padding: 12px 10px; text-align: left;">Title</th>
                    <th style="padding: 12px 10px; text-align: left;">Description</th>
                    <th style="padding: 12px 10px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <td style="padding: 12px 10px; font-size: 1.2rem; color: #818cf8;"><i class="<?php echo htmlspecialchars($service['icon_class']); ?>"></i></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($service['title']); ?></td>
                        <td style="padding: 12px 10px; font-size: 0.9rem; color: #94a3b8;"><?php echo htmlspecialchars(substr($service['description'], 0, 50)) . '...'; ?></td>
                        <td style="padding: 12px 10px;">
                            <a href="manage_services.php?action=edit&id=<?php echo $service['id']; ?>" style="color: #8b5cf6; margin-right: 15px;">Edit</a>
                            <a href="manage_services.php?action=delete&id=<?php echo $service['id']; ?>" style="color: #ef4444;" onclick="return confirm('Delete this service?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #cbd5e1;">No services added yet.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Icon Selection Logic for both Add and Edit 
        function setupIconSelection(dropdownId, inputBoxId, previewId, customInputId) {
            const dropdown = document.getElementById(dropdownId);
            const inputBox = document.getElementById(inputBoxId);
            const preview = document.getElementById(previewId);
            const customInput = document.getElementById(customInputId);
            
            if (dropdown && inputBox && preview && customInput) {
                
                // change event for dropdown
                dropdown.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        inputBox.style.display = 'block'; 
                        preview.className = customInput.value || 'fas fa-question'; 
                    } else {
                        inputBox.style.display = 'none'; 
                        preview.className = this.value; 
                    }
                });

                // input event for custom icon input
                customInput.addEventListener('input', function() {
                    if (dropdown.value === 'custom') {
                        preview.className = this.value;
                    }
                });
            }
        }

        // Initialize icon selection for both add and edit forms
        setupIconSelection('add-icon-dropdown', 'add-custom-icon-box', 'add-icon-preview', 'add-custom-input');
        setupIconSelection('edit-icon-dropdown', 'edit-custom-icon-box', 'edit-icon-preview', 'edit-custom-input');
    });
</script>

<?php include 'footer.php'; ?>