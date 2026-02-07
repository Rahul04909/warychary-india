<?php
session_start();
include_once '../database/db_config.php';

if (isset($_SESSION['partner_id'])) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id, name, email, password, status FROM partners WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $row['password'])) {
            if ($row['status'] == 'active') {
                $_SESSION['partner_id'] = $row['id'];
                $_SESSION['partner_email'] = $row['email'];
                $_SESSION['partner_name'] = $row['name'];
                header("Location: index.php");
                exit;
            } else {
                $message = "Your account is inactive. Please contact admin.";
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
    <title>Partner Login - WaryChary</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .brand-logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .brand-logo img {
            max-height: 50px;
        }
        .btn-primary {
            background-color: #6366f1;
            border-color: #6366f1;
        }
        .btn-primary:hover {
            background-color: #4f46e5;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <img src="../assets/logo/logo.png" alt="WaryChary">
        <h5 class="mt-3">Partner Login</h5>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="Enter your email">
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    
    <div class="text-center mt-3">
        <a href="../become-a-partner.php" class="text-decoration-none text-muted small">Not a partner? Register here</a>
    </div>
</div>

</body>
</html>
