<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include sidebar and stylesheet resources for the dashboard
include 'header.php'; 
?>

<div class="header">
    <div class="header-left">
        <h1>Welcome Back, <?php echo $_SESSION['admin_user']; ?>! 🚀</h1>
        <p>Manage your entire portfolio dynamically from here.</p>
    </div>
    <div class="date-time">
        <p style="color: #94a3b8;"><i class="fas fa-calendar-alt"></i> <?php echo date('F d, Y'); ?></p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-project-diagram"></i>
        <h3>0</h3>
        <p>Total Projects</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-blog"></i>
        <h3>0</h3>
        <p>Blog Posts</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-envelope"></i>
        <h3>0</h3>
        <p>New Messages</p>
    </div>
</div>

<div class="welcome-banner">
    <h2>System Ready! 🛠️</h2>
    
</div>

<?php 
// Include footer markup and close the page layout
include 'footer.php'; 
?>