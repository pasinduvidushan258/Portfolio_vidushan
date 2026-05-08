<?php

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Pasindu Vidushan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Global Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #0f172a; color: white; display: flex; min-height: 100vh; }
        
        /* Sidebar Styles */
        .sidebar { width: 280px; background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); border-right: 1px solid rgba(255, 255, 255, 0.1); padding: 30px 20px; display: flex; flex-direction: column; overflow-y: auto; }
        .sidebar h2 { font-size: 1.5rem; margin-bottom: 30px; text-align: center; color: #6366f1; font-weight: 600; }
        .nav-links { list-style: none; flex-grow: 1; }
        .nav-links li { margin-bottom: 10px; }
        .nav-links a { text-decoration: none; color: #94a3b8; display: flex; align-items: center; padding: 12px 15px; border-radius: 10px; transition: 0.3s; font-size: 0.95rem; }
        .nav-links a i { margin-right: 15px; width: 20px; text-align: center; font-size: 1.1rem; }
        .nav-links a:hover, .nav-links a.active { background: rgba(99, 102, 241, 0.1); color: #ffffff; border-left: 4px solid #6366f1; }
        .logout-btn { color: #ef4444; text-decoration: none; padding: 12px 15px; border-radius: 10px; display: flex; align-items: center; transition: 0.3s; margin-top: 20px; }
        .logout-btn:hover { background: rgba(239, 68, 68, 0.1); color: #f87171;}
        
        /* Main Content Global */
        .main-content { flex-grow: 1; padding: 40px; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; background: rgba(30, 41, 59, 0.5); padding: 20px 30px; border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.05); }
        .header-left h1 { font-size: 1.8rem; margin-bottom: 5px; color: #ffffff; }
        .header-left p { color: #94a3b8; font-size: 0.95rem; }

        /* Dashboard Specific Styles */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: linear-gradient(145deg, rgba(30, 41, 59, 0.6), rgba(15, 23, 42, 0.8)); border: 1px solid rgba(255, 255, 255, 0.05); padding: 25px; border-radius: 15px; text-align: left; position: relative; overflow: hidden; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); border-color: rgba(99, 102, 241, 0.3); }
        .stat-card i { font-size: 2.5rem; color: rgba(99, 102, 241, 0.2); position: absolute; right: 20px; top: 25px; }
        .stat-card h3 { font-size: 2rem; margin-bottom: 5px; color: #ffffff; }
        .stat-card p { color: #94a3b8; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        .welcome-banner { background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1)); border: 1px solid rgba(99, 102, 241, 0.2); padding: 30px; border-radius: 15px; line-height: 1.6; color: #cbd5e1; }
        .welcome-banner h2 { color: #ffffff; margin-bottom: 15px; }
        .welcome-banner span { color: #8b5cf6; font-weight: 500; }

        /* Form Styles (For manage_hero.php and future forms) */
        .form-container { background: rgba(30, 41, 59, 0.6); padding: 30px; border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.05); max-width: 800px; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
        .alert-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; color: #cbd5e1; font-weight: 500; font-size: 0.95rem; }
        .input-group input[type="text"], .input-group textarea { width: 100%; padding: 12px 15px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: white; font-size: 1rem; outline: none; }
        .input-group input:focus, .input-group textarea:focus { border-color: #6366f1; }
        .input-group textarea { height: 100px; resize: none; }
        .input-group input[type="file"] { background: transparent; padding: 10px 0; }
        .current-img { margin-top: 10px; width: 150px; border-radius: 10px; border: 2px solid rgba(99, 102, 241, 0.5); }
        .btn-submit { background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; padding: 12px 25px; border-radius: 8px; color: white; font-size: 1rem; cursor: pointer; transition: 0.3s; font-weight: 500; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4); }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Pasindu's Panel</h2>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="manage_hero.php" class="<?php echo ($current_page == 'manage_hero.php') ? 'active' : ''; ?>"><i class="fas fa-home"></i>Home & Hero</a></li>
            
            <li><a href="manage_about.php" class="<?php echo ($current_page == 'manage_about.php') ? 'active' : ''; ?>"><i class="fas fa-user-clock"></i>About & Bio</a></li>
            <li><a href="manage_work_experience.php" class="<?php echo ($current_page == 'manage_work_experience.php') ? 'active' : ''; ?>"><i class="fas fa-briefcase"></i>Work Experience</a></li>
            <li><a href="manage_services.php" class="<?php echo ($current_page == 'manage_services.php') ? 'active' : ''; ?>"><i class="fas fa-concierge-bell"></i>Services</a></li>
            <li><a href="manage_projects.php" class="<?php echo ($current_page == 'manage_projects.php') ? 'active' : ''; ?>"><i class="fas fa-project-diagram"></i>Projects</a></li>
            <li><a href="manage_skills.php" class="<?php echo ($current_page == 'manage_skills.php') ? 'active' : ''; ?>"><i class="fas fa-award"></i>Skills & Achievements</a></li>
            <li><a href="manage_education.php" class="<?php echo ($current_page == 'manage_education.php') ? 'active' : ''; ?>"><i class="fas fa-graduation-cap"></i>Education</a></li>
            <li><a href="manage_certifications.php" class="<?php echo ($current_page == 'manage_certifications.php') ? 'active' : ''; ?>"><i class="fas fa-certificate"></i>Certifications</a></li>
            <li><a href="manage_messages.php" class="<?php echo ($current_page == 'manage_messages.php') ? 'active' : ''; ?>"><i class="fas fa-envelope"></i>Messages</a></li>
        </ul>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">