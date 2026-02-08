<?php
session_start();
require_once '../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

$message = "";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id, name, email, password, image, status FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $row['password'])) {
            if ($row['status'] == 'active') {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_image'] = $row['image'];
                header("Location: index.php");
                exit;
            } else {
                $message = "Your account is inactive. Please contact support.";
            }
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - WaryChary</title>
    
    <!-- Dependencies -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0f172a; /* Deep Navy */
            --accent-color: #6366f1; /* Soft Purple/Indigo */
            --bg-color: #f7f9fc;
            --input-border: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1);
            padding: 40px;
            border: 1px solid var(--input-border);
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo img {
            height: 50px;
            margin-bottom: 15px;
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .form-subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-group-text {
            background-color: #f8fafc;
            border-color: var(--input-border);
            color: var(--text-muted);
        }

        .form-control {
            background-color: #ffffff;
            border: 1px solid var(--input-border);
            color: var(--text-dark);
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--accent-color);
            color: var(--text-dark);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-login {
            background: var(--accent-color);
            color: white;
            font-weight: 600;
            padding: 14px;
            border-radius: 10px;
            border: none;
            width: 100%;
            font-size: 16px;
            transition: all 0.2s;
            margin-top: 20px;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);
        }

        .btn-login:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: var(--text-muted);
            font-size: 13px;
        }
        
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--input-border);
        }
        
        .divider span {
            padding: 0 10px;
        }

        .footer-links {
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
        }

        .footer-links a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: none;
            font-size: 14px;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 25px;
        }

        .password-toggle {
            cursor: pointer;
            z-index: 10;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <!-- Assuming logo path matches relative path from user/ -->
        <img src="../assets/logo/logo.png" alt="WaryChary">
        <h1 class="form-title">Welcome Back</h1>
        <p class="form-subtitle">Login to access your dashboard</p>
    </div>

    <?php if(!empty($message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" required placeholder="name@example.com">
            </div>
        </div>

        <div class="mb-1">
            <label class="form-label d-flex justify-content-between">
                Password
                <a href="#" class="text-decoration-none small" style="color: var(--text-muted);">Forgot Password?</a>
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Enter your password">
                <span class="input-group-text password-toggle" onclick="togglePassword()">
                    <i class="far fa-eye" id="toggleIcon"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn btn-login">
            Login to Dashboard <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </form>
    
    <div class="divider">
        <span>New to WaryChary?</span>
    </div>
    
    <div class="footer-links">
        <a href="../register.php">Create an Account</a>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>
