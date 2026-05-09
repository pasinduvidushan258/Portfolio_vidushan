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

// Category Delete
if (isset($_GET['delete_cat'])) {
    $stmt = $conn->prepare("DELETE FROM project_categories WHERE id = ?");
    if ($stmt->execute([$_GET['delete_cat']])) { $success_msg = "Category deleted successfully!"; }
}

// Language Delete
if (isset($_GET['delete_lang'])) {
    $stmt = $conn->prepare("DELETE FROM languages WHERE id = ?");
    if ($stmt->execute([$_GET['delete_lang']])) { $success_msg = "Language deleted successfully!"; }
}

// Project Delete
if (isset($_GET['delete_proj'])) {
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    if ($stmt->execute([$_GET['delete_proj']])) { $success_msg = "Project deleted successfully!"; }
}

// ADD & UPDATE ACTIONS
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Add Category
    if (isset($_POST['add_category'])) {
        $category_name = trim($_POST['category_name']);
        $stmt = $conn->prepare("INSERT INTO project_categories (category_name) VALUES (?)");
        if ($stmt->execute([$category_name])) { $success_msg = "Category added!"; }
    }

    // Add Language
    if (isset($_POST['add_language'])) {
        $lang_name = trim($_POST['lang_name']);
        $icon_slug = trim($_POST['icon_slug']);
        $stmt = $conn->prepare("INSERT INTO languages (lang_name, icon_slug) VALUES (?, ?)");
        if ($stmt->execute([$lang_name, $icon_slug])) { $success_msg = "Language added!"; }
    }

    // Add / Update Project
    if (isset($_POST['add_project'])) {
        $project_id = $_POST['project_id']; 
        
        $category_id = $_POST['category_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $project_link = $_POST['project_link'];
        $github_link = $_POST['github_link'];
        $status = $_POST['status'];
        $key_features = $_POST['key_features'];
        $selected_langs = $_POST['languages'] ?? []; 

        $image_name = $_FILES['project_image']['name'];
        $image_updated = false;

        // Update the image only if a new one is uploaded. This way, when editing, the admin can choose to keep the existing image by leaving the file input empty.
        if (!empty($image_name)) {
            $target_dir = "../uploads/projects/";
            $target_file = $target_dir . basename($image_name);
            move_uploaded_file($_FILES['project_image']['tmp_name'], $target_file);
            $image_updated = true;
        }

        try {
            $conn->beginTransaction();

            if (!empty($project_id)) {
                // ----- UPDATE PROJECT -----
                if ($image_updated) {
                    $stmt = $conn->prepare("UPDATE projects SET category_id=?, title=?, description=?, image_path=?, project_link=?, github_link=?, status=?, key_features=? WHERE id=?");
                    $stmt->execute([$category_id, $title, $description, $image_name, $project_link, $github_link, $status, $key_features, $project_id]);
                } else {
                    $stmt = $conn->prepare("UPDATE projects SET category_id=?, title=?, description=?, project_link=?, github_link=?, status=?, key_features=? WHERE id=?");
                    $stmt->execute([$category_id, $title, $description, $project_link, $github_link, $status, $key_features, $project_id]);
                }

                // Update project languages: First delete existing ones, then insert the new selections
                $conn->prepare("DELETE FROM project_languages WHERE project_id = ?")->execute([$project_id]);
                foreach ($selected_langs as $lang_id) {
                    $conn->prepare("INSERT INTO project_languages (project_id, language_id) VALUES (?, ?)")->execute([$project_id, $lang_id]);
                }
                
                $success_msg = "Project updated successfully!";
                
            } else {
                // ----- ADD NEW PROJECT -----
                if ($image_updated) {
                    $stmt = $conn->prepare("INSERT INTO projects (category_id, title, description, image_path, project_link, github_link, status, key_features) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$category_id, $title, $description, $image_name, $project_link, $github_link, $status, $key_features]);
                    
                    $new_project_id = $conn->lastInsertId();

                    foreach ($selected_langs as $lang_id) {
                        $conn->prepare("INSERT INTO project_languages (project_id, language_id) VALUES (?, ?)")->execute([$new_project_id, $lang_id]);
                    }
                    $success_msg = "Project added successfully!";
                } else {
                    $error_msg = "Failed to upload image. Image is required for new projects.";
                }
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            $error_msg = "Error: " . $e->getMessage();
        }
    }
}


// EDIT MODE DATA FETCH
$edit_proj = null;
$edit_langs = [];

if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_proj = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt_lang = $conn->prepare("SELECT language_id FROM project_languages WHERE project_id = ?");
    $stmt_lang->execute([$_GET['edit_id']]);
    $edit_langs = $stmt_lang->fetchAll(PDO::FETCH_COLUMN); 
}

// FETCH DATA FOR DISPLAY
$categories = $conn->query("SELECT * FROM project_categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$all_languages = $conn->query("SELECT * FROM languages ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$projects = $conn->query("SELECT p.*, pc.category_name FROM projects p JOIN project_categories pc ON p.category_id = pc.id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="header">
    <h1>Manage Projects & Resources 🛠️</h1>
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

<!-- ==========================================
     TOP SECTION: CATEGORIES & LANGUAGES
=========================================== -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 40px;">
    
    <!-- CATEGORIES BLOCK -->
    <div class="form-container" style="margin-bottom: 0;">
        <h2 style="color:#fff; margin-bottom:15px; font-size: 1.2rem;">📂 Manage Categories</h2>
        <form action="manage_projects.php" method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <input type="text" name="category_name" required placeholder="e.g. Web Apps" style="flex: 1; padding: 10px; background: #0f172a; color: #fff; border: 1px solid #334155; border-radius: 5px;">
            <button type="submit" name="add_category" class="btn-submit" style="width: auto; padding: 0 15px; background: #3b82f6;">Add</button>
        </form>
        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #334155; border-radius: 5px;">
            <table style="width: 100%; color: #cbd5e1; border-collapse: collapse; font-size: 0.9rem;">
                <?php foreach($categories as $cat): ?>
                <tr style="border-bottom: 1px solid #1e293b;">
                    <td style="padding:8px 10px;"><?php echo $cat['category_name']; ?></td>
                    <td style="padding:8px 10px; text-align: right;">
                        <a href="?delete_cat=<?php echo $cat['id']; ?>" style="color:#ef4444;" onclick="return confirm('Delete this category?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- LANGUAGES BLOCK -->
    <div class="form-container" style="margin-bottom: 0;">
        <h2 style="color:#fff; margin-bottom:15px; font-size: 1.2rem;">💻 Manage Languages</h2>
        <form action="manage_projects.php" method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <input type="text" name="lang_name" required placeholder="Name (e.g. PHP)" style="flex: 1; padding: 10px; background: #0f172a; color: #fff; border: 1px solid #334155; border-radius: 5px;">
            <input type="text" name="icon_slug" required placeholder="Slug (e.g. php)" style="flex: 1; padding: 10px; background: #0f172a; color: #fff; border: 1px solid #334155; border-radius: 5px;">
            <button type="submit" name="add_language" class="btn-submit" style="width: auto; padding: 0 15px; background: #8b5cf6;">Add</button>
        </form>
        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #334155; border-radius: 5px;">
            <table style="width: 100%; color: #cbd5e1; border-collapse: collapse; font-size: 0.9rem;">
                <?php foreach($all_languages as $lang): ?>
                <tr style="border-bottom: 1px solid #1e293b;">
                    <td style="padding:8px 10px;"><img src="https://skillicons.dev/icons?i=<?php echo $lang['icon_slug']; ?>" width="20" style="vertical-align: middle; border-radius: 3px;"></td>
                    <td style="padding:8px 10px;"><?php echo $lang['lang_name']; ?></td>
                    <td style="padding:8px 10px; text-align: right;">
                        <a href="?delete_lang=<?php echo $lang['id']; ?>" style="color:#ef4444;" onclick="return confirm('Delete this language?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</div>

<!-- ==========================================
     MAIN SECTION: ADD / EDIT PROJECT
=========================================== -->
<div class="form-container" style="margin-bottom: 40px; border: <?php echo $edit_proj ? '2px solid #3b82f6' : 'none'; ?>;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom:20px;">
        <h2 style="color:#fff; margin:0;">
            <?php echo $edit_proj ? '✏️ Edit Project' : '🚀 Add New Project'; ?>
        </h2>
        <?php if($edit_proj): ?>
            <a href="manage_projects.php" style="background: #64748b; color: #fff; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9rem;">Cancel Edit</a>
        <?php endif; ?>
    </div>

    <form action="manage_projects.php" method="POST" enctype="multipart/form-data">
        
        <!-- Project ID (for Edit Mode) -->
        <input type="hidden" name="project_id" value="<?php echo $edit_proj ? $edit_proj['id'] : ''; ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="input-group">
                <label>Project Title</label>
                <input type="text" name="title" required placeholder="e.g. VoteSphere" value="<?php echo $edit_proj ? htmlspecialchars($edit_proj['title']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>Category</label>
                <select name="category_id" required style="width:100%; padding:12px; background:#0f172a; color:#fff; border:1px solid #334155; border-radius:8px;">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_proj && $edit_proj['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo $cat['category_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="input-group">
            <label>Description</label>
            <textarea name="description" rows="3" required placeholder="Briefly describe your project..."><?php echo $edit_proj ? htmlspecialchars($edit_proj['description']) : ''; ?></textarea>
        </div>

        <div class="input-group">
            <label>Key Features (Separate by commas)</label>
            <textarea name="key_features" rows="2" placeholder="Feature 1, Feature 2, Feature 3"><?php echo $edit_proj ? htmlspecialchars($edit_proj['key_features']) : ''; ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="input-group">
                <label>Status</label>
                <input type="text" name="status" value="<?php echo $edit_proj ? htmlspecialchars($edit_proj['status']) : 'COMPLETED'; ?>">
            </div>
            <div class="input-group">
                <label>Project Image <?php echo $edit_proj ? '<span style="color:#f59e0b; font-size:0.8rem;">(Leave empty to keep current)</span>' : ''; ?></label>
                <input type="file" name="project_image" <?php echo $edit_proj ? '' : 'required'; ?> style="color:#94a3b8; background: #0f172a; padding: 10px; border-radius: 8px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="input-group">
                <label>GitHub Link</label>
                <input type="url" name="github_link" placeholder="https://github.com/..." value="<?php echo $edit_proj ? htmlspecialchars($edit_proj['github_link']) : ''; ?>">
            </div>
            <div class="input-group">
                <label>Live Demo Link</label>
                <input type="url" name="project_link" placeholder="https://demo-site.com" value="<?php echo $edit_proj ? htmlspecialchars($edit_proj['project_link']) : ''; ?>">
            </div>
        </div>

        <div class="input-group">
            <label style="margin-bottom: 15px; display: block;">Select Technologies Used:</label>
            <div style="display: flex; flex-wrap: wrap; gap: 15px; background: #0f172a; padding: 15px; border-radius: 8px; border: 1px solid #334155;">
                <?php if(empty($all_languages)): ?>
                    <span style="color: #ef4444; font-size: 0.9rem;">No languages added yet. Please add them from the top section first.</span>
                <?php endif; ?>
                
                <?php foreach($all_languages as $lang): ?>
                    <?php $is_checked = in_array($lang['id'], $edit_langs) ? 'checked' : ''; ?>
                    <label style="color: #cbd5e1; cursor: pointer; display: flex; align-items: center; gap: 8px; background: #1e293b; padding: 5px 10px; border-radius: 5px;">
                        <input type="checkbox" name="languages[]" value="<?php echo $lang['id']; ?>" <?php echo $is_checked; ?>>
                        <img src="https://skillicons.dev/icons?i=<?php echo $lang['icon_slug']; ?>" width="16" style="border-radius: 2px;">
                        <?php echo $lang['lang_name']; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" name="add_project" class="btn-submit" style="margin-top: 20px; background: <?php echo $edit_proj ? '#3b82f6' : '#10b981'; ?>;">
            <i class="fas <?php echo $edit_proj ? 'fa-save' : 'fa-plus'; ?>"></i> 
            <?php echo $edit_proj ? 'Update Project' : 'Save Project'; ?>
        </button>
    </form>
</div>

<!-- ==========================================
     BOTTOM SECTION: EXISTING PROJECTS
=========================================== -->
<div class="form-container">
    <h2 style="color:#fff; margin-bottom:20px;">📋 Existing Projects</h2>
    <div style="overflow-x: auto;">
        <table style="width: 100%; color: #fff; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #334155; text-align: left; background: #0f172a;">
                    <th style="padding:12px;">Image</th>
                    <th style="padding:12px;">Project Details</th>
                    <th style="padding:12px;">Status & Links</th>
                    <th style="padding:12px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($projects as $proj): ?>
                <tr style="border-bottom: 1px solid #1e293b;">
                    
                    <!-- Image -->
                    <td style="padding:12px;">
                        <img src="../uploads/projects/<?php echo $proj['image_path']; ?>" width="90" style="border-radius: 8px; border: 1px solid #334155; object-fit: cover; aspect-ratio: 16/9;">
                    </td>
                    
                    <!-- Title and Category -->
                    <td style="padding:12px;">
                        <div style="font-weight: bold; color: #818cf8; font-size: 1.1rem; margin-bottom: 6px;"><?php echo $proj['title']; ?></div>
                        <span style="background: rgba(255,255,255,0.1); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; color: #cbd5e1;"><?php echo $proj['category_name']; ?></span>
                    </td>
                    
                    <!-- Status and GitHub/Demo Links -->
                    <td style="padding:12px; font-size: 0.9rem;">
                        <div style="margin-bottom: 8px;">
                            <span style="color: #10b981; font-weight: bold; font-size: 0.75rem; border: 1px solid #10b981; padding: 3px 8px; border-radius: 20px;"><?php echo $proj['status']; ?></span>
                        </div>
                        <?php if($proj['github_link']): ?>
                            <a href="<?php echo $proj['github_link']; ?>" target="_blank" style="color: #94a3b8; text-decoration: none; margin-right: 12px; display: inline-block;"><i class="fab fa-github"></i> Code</a>
                        <?php endif; ?>
                        <?php if($proj['project_link']): ?>
                            <a href="<?php echo $proj['project_link']; ?>" target="_blank" style="color: #94a3b8; text-decoration: none; display: inline-block;"><i class="fas fa-external-link-alt"></i> Demo</a>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Actions -->
                    <td style="padding:12px;">
                        <div style="display: flex; gap: 10px;">
                            <!-- Edit Button (Changed link to ?edit_id=) -->
                            <a href="manage_projects.php?edit_id=<?php echo $proj['id']; ?>" style="color:#3b82f6; background: rgba(59, 130, 246, 0.1); padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: bold;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <!-- Delete Button -->
                            <a href="?delete_proj=<?php echo $proj['id']; ?>" style="color:#ef4444; background: rgba(239, 68, 68, 0.1); padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: bold;" onclick="return confirm('Are you sure you want to delete this project?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </td>

                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($projects)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">No projects found. Add your first project above!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>