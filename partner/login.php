<?php
session_start();
include_once '../database/db_config.php';

if (isset($_SESSION['p_logged_in']) && $_SESSION['p_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $query = "SELECT id, username, password FROM partners WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $_SESSION['p_logged_in'] = true;
                $_SESSION['p_id'] = $row['id'];
                $_SESSION['p_username'] = $row['username'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid username.";
        }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header img { height: 50px; margin-bottom: 15px; }
        .login-header h2 { font-size: 24px; color: #1d2327; }
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #3c434a; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px 15px; border: 1px solid #dcdcde; border-radius: 4px; font-size: 14px; transition: border-color 0.2s; }
        .form-group input:focus { outline: none; border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
        .password-toggle { position: absolute; right: 15px; top: 38px; color: #646970; cursor: pointer; font-size: 14px; }
        .btn-login { width: 100%; padding: 12px; background-color: #2271b1; color: white; border: none; border-radius: 4px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background-color 0.2s; }
        .btn-login:hover { background-color: #135e96; }
        .error-msg { background-color: #fce8e8; color: #d63638; padding: 10px; border-radius: 4px; font-size: 13px; margin-bottom: 20px; text-align: center; }
        .back-link { display: block; text-align: center; margin-top: 20px; font-size: 13px; color: #646970; text-decoration: none; }
        .back-link:hover { color: #2271b1; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="../assets/logo/logo.png" alt="WaryChary">
        <h2>Partner Login</h2>
    </div>

    <?php if(!empty($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
        </div>

        <button type="submit" class="btn-login">Log In</button>
    </form>
    
    <a href="../index.php" class="back-link">← Back to Website</a>
</div>

<script>
    document.querySelector('#togglePassword').addEventListener('click', function (e) {
        const password = document.querySelector('#password');
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
</script>
</body>
</html>
