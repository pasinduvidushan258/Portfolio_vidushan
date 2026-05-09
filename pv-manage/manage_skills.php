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
$categories = [];
$skills = [];
$edit_category = null;
$edit_skill = null;

// Delete category
if (isset($_GET['action']) && $_GET['action'] === 'delete_category' && isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("DELETE FROM skill_categories WHERE id = :id");
        $stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        $success_msg = "Category deleted successfully!";
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Load category for editing
if (isset($_GET['action']) && $_GET['action'] === 'edit_category' && isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("SELECT * FROM skill_categories WHERE id = :id");
        $stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Delete skill
if (isset($_GET['action']) && $_GET['action'] === 'delete_skill' && isset($_GET['id'])) {
    $skill_id = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("DELETE FROM skills WHERE id = :id");
        $stmt->bindParam(':id', $skill_id, PDO::PARAM_INT);
        $stmt->execute();
        $success_msg = "Skill deleted successfully!";
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Load skill for editing
if (isset($_GET['action']) && $_GET['action'] === 'edit_skill' && isset($_GET['id'])) {
    $skill_id = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("SELECT * FROM skills WHERE id = :id");
        $stmt->bindParam(':id', $skill_id, PDO::PARAM_INT);
        $stmt->execute();
        $edit_skill = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Update an existing category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_category'])) {
    $category_id = intval($_POST['category_id']);
    $category_name = trim($_POST['category_name']);

    try {
        $stmt = $conn->prepare("UPDATE skill_categories SET category_name = :category_name WHERE id = :id");
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        $success_msg = "Category updated successfully!";
        header("Location: manage_skills.php");
        exit();
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Update an existing skill
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_skill'])) {
    $skill_id = intval($_POST['skill_id']);
    $category_id = intval($_POST['category_id']);
    $skill_name = trim($_POST['skill_name']);
    $icon_slug = strtolower(trim($_POST['icon_slug']));

    try {
        $stmt = $conn->prepare("UPDATE skills SET category_id = :category_id, skill_name = :skill_name, icon_slug = :icon_slug WHERE id = :id");
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':skill_name', $skill_name);
        $stmt->bindParam(':icon_slug', $icon_slug);
        $stmt->bindParam(':id', $skill_id, PDO::PARAM_INT);
        $stmt->execute();
        $success_msg = "Skill updated successfully!";
        header("Location: manage_skills.php");
        exit();
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// 1. Add new category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);

    try {
        $sql = "INSERT INTO skill_categories (category_name) VALUES (:category_name)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':category_name', $category_name);
        $stmt->execute();
        $success_msg = "Category added successfully!";
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// 2. Add new skill
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_skill'])) {
    $category_id = $_POST['category_id'];
    $skill_name = trim($_POST['skill_name']);
    $icon_slug = strtolower(trim($_POST['icon_slug']));

    try {
        $sql = "INSERT INTO skills (category_id, skill_name, icon_slug) VALUES (:category_id, :skill_name, :icon_slug)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':skill_name', $skill_name);
        $stmt->bindParam(':icon_slug', $icon_slug);
        
        $stmt->execute();
        $success_msg = "Skill added successfully!";
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}

// Fetch all categories and skills for display
try {
    $stmt = $conn->prepare("SELECT * FROM skill_categories ORDER BY id ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt_skills = $conn->prepare("SELECT s.*, c.category_name FROM skills s LEFT JOIN skill_categories c ON s.category_id = c.id ORDER BY s.id ASC");
    $stmt_skills->execute();
    $skills = $stmt_skills->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_msg = "Error fetching categories: " . $e->getMessage();
}

// Include the dashboard header
include 'header.php'; 
?>

<!-- Header Section -->
<div class="header">
    <div class="header-left">
        <h1>Manage Skills & Categories 🚀</h1>
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

<!-- Section 1: Add/Edit Category Form -->
<div class="form-container" style="margin-bottom: 30px;">
    <?php if ($edit_category): ?>
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Edit Skill Category</h2>
        <form action="" method="POST">
            <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($edit_category['id']); ?>">
            <div class="input-group">
                <label>Category Name</label>
                <input type="text" name="category_name" value="<?php echo htmlspecialchars($edit_category['category_name']); ?>" required>
            </div>
            <button type="submit" name="update_category" class="btn-submit" style="background: #8b5cf6;"><i class="fas fa-save"></i> Update Category</button>
            <a href="manage_skills.php" class="btn-submit" style="background: #64748b; margin-left: 10px; text-decoration: none; display: inline-block;">Cancel</a>
        </form>
    <?php else: ?>
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Add New Skill Category</h2>
        <form action="" method="POST">
            <div class="input-group">
                <label>Category Name</label>
                <input type="text" name="category_name" placeholder="e.g. Frontend, Backend, Tools" required>
            </div>
            <button type="submit" name="add_category" class="btn-submit" style="background: #8b5cf6;"><i class="fas fa-layer-group"></i> Add Category</button>
        </form>
    <?php endif; ?>
</div>

<!-- Section 2: Add/Edit Skill Form -->
<div class="form-container">
    <?php if ($edit_skill): ?>
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Edit Tech Skill</h2>
        <form action="" method="POST">
            <input type="hidden" name="skill_id" value="<?php echo htmlspecialchars($edit_skill['id']); ?>">
            <div class="input-group">
                <label>Select Category</label>
                <select name="category_id" required style="width: 100%; padding: 10px; border-radius: 5px; background: #1e293b; color: #fff; border: 1px solid #334155; margin-top: 5px;">
                    <option value="">-- Choose Category --</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_skill['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Skill Name</label>
                <input type="text" name="skill_name" value="<?php echo htmlspecialchars($edit_skill['skill_name']); ?>" required>
            </div>
            <div class="input-group">
                <label>Skill Icon Code</label>
                <input type="text" name="icon_slug" value="<?php echo htmlspecialchars($edit_skill['icon_slug']); ?>" required>
                <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 5px;">* skillicons.dev හි ඇති කෙටි නම ලබා දෙන්න (උදා: csharp, cpp, angular).</p>
            </div>
            <button type="submit" name="update_skill" class="btn-submit" style="background: #10b981;"><i class="fas fa-save"></i> Update Skill</button>
            <a href="manage_skills.php" class="btn-submit" style="background: #64748b; margin-left: 10px; text-decoration: none; display: inline-block;">Cancel</a>
        </form>
    <?php else: ?>
        <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Add New Tech Skill</h2>
        <form action="" method="POST">
            <div class="input-group">
                <label>Select Category</label>
                <select name="category_id" required style="width: 100%; padding: 10px; border-radius: 5px; background: #1e293b; color: #fff; border: 1px solid #334155; margin-top: 5px;">
                    <option value="">-- Choose Category --</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <label>Skill Name</label>
                <input type="text" name="skill_name" placeholder="e.g. React.js, Python, Figma" required>
            </div>

            <div class="input-group">
                <label>Skill Icon Code</label>
                <input type="text" name="icon_slug" placeholder="e.g. python, react, php, mysql" required>
                <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 5px;">* skillicons.dev හි ඇති කෙටි නම ලබා දෙන්න (උදා: csharp, cpp, angular).</p>
            </div>

            <button type="submit" name="add_skill" class="btn-submit" style="background: #10b981;"><i class="fas fa-plus"></i> Add Skill</button>
        </form>
    <?php endif; ?>
</div>

<div class="form-container" style="margin-top: 30px; overflow-x: auto;">
    <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Saved Skill Categories</h2>
    <?php if (!empty($categories)): ?>
        <table style="width: 100%; border-collapse: collapse; color: #fff;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <th style="padding: 12px 10px; text-align: left;">Category Name</th>
                    <th style="padding: 12px 10px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($category['category_name']); ?></td>
                        <td style="padding: 12px 10px;">
                            <a href="manage_skills.php?action=edit_category&id=<?php echo $category['id']; ?>" style="color: #8b5cf6; margin-right: 15px;">Edit</a>
                            <a href="manage_skills.php?action=delete_category&id=<?php echo $category['id']; ?>" style="color: #ef4444;" onclick="return confirm('Delete this category?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #cbd5e1;">No categories found yet.</p>
    <?php endif; ?>
</div>

<div class="form-container" style="margin-top: 30px; overflow-x: auto;">
    <h2 style="color: #fff; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 1px solid #2d3748; padding-bottom: 10px;">Saved Skills</h2>
    <?php if (!empty($skills)): ?>
        <table style="width: 100%; border-collapse: collapse; color: #fff;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <th style="padding: 12px 10px; text-align: left;">Skill Name</th>
                    <th style="padding: 12px 10px; text-align: left;">Category</th>
                    <th style="padding: 12px 10px; text-align: left;">Icon Slug</th>
                    <th style="padding: 12px 10px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($skills as $skill): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($skill['skill_name']); ?></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($skill['category_name'] ?? 'Uncategorized'); ?></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($skill['icon_slug']); ?></td>
                        <td style="padding: 12px 10px;">
                            <a href="manage_skills.php?action=edit_skill&id=<?php echo $skill['id']; ?>" style="color: #8b5cf6; margin-right: 15px;">Edit</a>
                            <a href="manage_skills.php?action=delete_skill&id=<?php echo $skill['id']; ?>" style="color: #ef4444;" onclick="return confirm('Delete this skill?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #cbd5e1;">No skills found yet.</p>
    <?php endif; ?>
</div>

<?php 
// Include the footer
include 'footer.php'; 
?>