<!DOCTYPE html>
<?php $url_prefix = ''; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warychary Care</title>
    <!-- FontAwesome (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/topbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/hero.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/standards.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/referral.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/products.css?v=<?php echo time(); ?>">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <?php include 'components/hero.php'; ?>
        <?php include 'components/standards.php'; ?>
        <?php include 'components/referral-program.php'; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
