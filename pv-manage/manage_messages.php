<?php
session_start();
require_once '../includes/db_connect.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

// Delete logic
if (isset($_GET['delete_id'])) {
    $conn->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$_GET['delete_id']]);
}

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
include 'header.php';
?>

<div class="header"><h1>Contact Messages 📩</h1></div>

<div class="form-container" style="max-width: 100%; width: 100%; box-sizing: border-box;">
    <table style="width:100%; color:#fff; border-collapse:collapse; background: #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <tr style="background:#0f172a; text-align:left;">
            <th style="padding:15px; width: 15%;">Date & Time</th>
            <th style="padding:15px; width: 25%;">Sender Details</th>
            <th style="padding:15px; width: 50%;">Message</th>
            <th style="padding:15px; width: 10%; text-align: center;">Action</th>
        </tr>
        
        <?php foreach($messages as $m): ?>
        <tr style="border-bottom:1px solid #334155;">
            
            <td style="padding:15px; vertical-align: top; color: #94a3b8; font-size: 0.85rem;">
                <i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($m['created_at'])); ?><br>
                <i class="far fa-clock" style="margin-top: 5px;"></i> <?php echo date('h:i A', strtotime($m['created_at'])); ?>
            </td>
            
            <td style="padding:15px; vertical-align: top;">
                <b style="color: #f8fafc; font-size: 1.05rem;"><?php echo htmlspecialchars($m['name']); ?></b><br>
                <div style="margin-top: 8px;">
                    <span style="color: #818cf8; font-size: 0.85rem; display: block; margin-bottom: 3px;">
                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($m['email']); ?>
                    </span>
                    <span style="color: #10b981; font-size: 0.85rem; display: block;">
                        <i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($m['mobile'] ?? 'No Number'); ?>
                    </span>
                </div>
            </td>
            
            <td style="padding:15px; vertical-align: top;">
                <b style="color: #e2e8f0; display: block; margin-bottom: 8px; font-size: 0.95rem;">
                    Subject: <?php echo htmlspecialchars($m['subject']); ?>
                </b>
                <div class="msg-scroll-box">
                    <?php echo nl2br(htmlspecialchars($m['message'])); ?>
                </div>
            </td>
            
            <td style="padding:15px; vertical-align: top; text-align: center;">
                <a href="?delete_id=<?php echo $m['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this message?')">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
            
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<style>
    /* Message Box Scrollbar Design */
    .msg-scroll-box {
        max-height: 80px; 
        overflow-y: auto; 
        color: #cbd5e1; 
        font-size: 0.9rem; 
        line-height: 1.6; 
        padding-right: 10px;
        background: rgba(15, 23, 42, 0.3);
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #334155;
    }
    
    .msg-scroll-box::-webkit-scrollbar { width: 5px; }
    .msg-scroll-box::-webkit-scrollbar-track { background: #0f172a; border-radius: 10px; }
    .msg-scroll-box::-webkit-scrollbar-thumb { background: #475569; border-radius: 10px; }
    .msg-scroll-box::-webkit-scrollbar-thumb:hover { background: #818cf8; }

    .delete-btn {
        color: #ef4444; 
        background: rgba(239, 68, 68, 0.1); 
        padding: 10px 12px; 
        border-radius: 8px; 
        display: inline-block; 
        transition: 0.3s;
    }
    .delete-btn:hover {
        background: rgba(239, 68, 68, 0.2);
        transform: scale(1.1);
    }
</style>

<?php include 'footer.php'; ?>