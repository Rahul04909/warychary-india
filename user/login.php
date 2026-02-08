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
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a; /* Deep Navy */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: #1e293b; /* Slate */
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .brand-logo img {
            height: 50px;
        }
        
        .form-control {
            background-color: #334155;
            border: 1px solid #475569;
            color: #fff;
            padding: 12px;
        }
        
        .form-control:focus {
            background-color: #334155;
            border-color: #10b981; /* Emerald */
            color: #fff;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }
        
        .btn-login {
            background: #10b981;
            color: white;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: #059669;
            transform: translateY(-2px);
        }
        
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: #f87171;
            font-size: 14px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link a:hover {
            color: #fff;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <img src="../assets/logo/logo.png" alt="WaryChary">
        <h4 class="mt-3">Welcome Back</h4>
        <p class="text-muted small">Login to your account</p>
    </div>

    <?php if(!empty($message)): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" required placeholder="Enter your email">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control" required placeholder="Enter your password">
            </div>
        </div>

        <button type="submit" class="btn btn-login">Login</button>
    </form>
    
    <div class="back-link">
        <a href="../index.php"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
        <span class="mx-2 text-muted">|</span>
        <a href="../register.php">Create Account</a>
    </div>
</div>

</body>
</html>
