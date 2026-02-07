<?php
include_once __DIR__ . '/../auth_check.php';
if (!isset($url_prefix)) $url_prefix = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Dashboard - WaryChary</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Admin CSS (Reused) -->
    <link rel="stylesheet" href="<?php echo $url_prefix; ?>../admin/assets/css/admin.css?v=<?php echo time(); ?>">
    
    <style>
        /* Partner specific color override */
        :root {
            --sidebar-bg: #1e1b4b; /* Deep Indigo */
            --sidebar-active: #6366f1; /* Indigo */
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
        }
        .admin-sidebar {
            background-color: var(--sidebar-bg);
        }
    </style>
</head>
<body>

<div class="admin-wrapper">
    
    <!-- Sidebar Include -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="header-right">
                <div class="header-icon">
                    <i class="fas fa-bell"></i>
                    <span class="badge-dot"></span>
                </div>
                
                <div class="user-profile">
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($_SESSION['partner_name']); ?></span>
                        <small>Partner</small>
                    </div>
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </header>

        <div class="admin-content">
