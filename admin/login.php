<?php
// Initialize output buffering and start the session for authentication handling
ob_start();
session_start();

// Load the PDO database connection
require_once '../includes/db_connect.php'; 

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Use prepared statements to securely validate the supplied user credentials
        $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = :user");
        $stmt->bindParam(':user', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the provided password against the stored credential
            if ($password == $user['password']) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_user'] = $user['username'];
                
                // Redirect authenticated user to the dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error_msg = "Invalid Password!";
            }
        } else {
            $error_msg = "User not found!";
        }
    } catch(PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Pasindu Vidushan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #0f172a; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            padding: 20px; /* Ensure spacing does not feel cramped on mobile devices */
        }

        .background-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.3), transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        .login-box {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 50px 40px; /* Increase vertical and horizontal spacing for the login form */
            border-radius: 20px;
            width: 100%;
            max-width: 450px; /* Use a larger maximum width for improved readability */
            z-index: 2;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            text-align: center;
        }

        .login-box h2 {
            color: #ffffff;
            font-size: 2rem;
            margin-bottom: 35px;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Error display container */
        .error-msg {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            display: <?php echo ($error_msg == "") ? 'none' : 'block'; ?>;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
            text-align: left;
        }

        /* Icon positioned on the left side of the input */
        .icon-left {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
        }

        /* Icon positioned on the right side of the input */
        .icon-right {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            font-size: 1.1rem;
            transition: 0.3s;
        }

        .icon-right:hover {
            color: #ffffff;
        }

        .input-group input {
            width: 100%;
            padding: 15px 45px; /* Reserve space for both left and right input icons */
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1.05rem;
            outline: none;
            transition: 0.3s;
        }

        .input-group input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.3);
            background: rgba(15, 23, 42, 0.9);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.5);
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .back-link:hover {
            color: #ffffff;
            transform: translateX(-5px);
        }
    </style>
</head>
<body>

    <div class="background-glow"></div>

    <div class="login-box">
        <h2>Admin Portal</h2>

        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="input-group">
                <i class="fas fa-user icon-left"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock icon-left"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <i class="fas fa-eye icon-right" id="togglePassword"></i>
            </div>
            
            <button type="submit" name="login_submit" class="login-btn">Login to Dashboard</button>
        </form>

        <a href="../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Website</a>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>