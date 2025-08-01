<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Le Coin de Clem - Ventes d\'occasion et services'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul class="nav-links nav-left">
                <li><a href="projets.php" <?php echo (isset($current_page) && $current_page === 'projets') ? 'class="active"' : ''; ?>>Projets</a></li>
                <li><a href="services.php" <?php echo (isset($current_page) && $current_page === 'services') ? 'class="active"' : ''; ?>>Services</a></li>
            </ul>
            
            <a href="index.php" class="logo">Le Coin de Clem</a>
            
            <ul class="nav-links nav-right">
                <li><a href="ventes.php" <?php echo (isset($current_page) && $current_page === 'ventes') ? 'class="active"' : ''; ?>>Ventes</a></li>
                <li><a href="contact.php" <?php echo (isset($current_page) && $current_page === 'contact') ? 'class="active"' : ''; ?>>Contact</a></li>
            </ul>

            <div class="burger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>

        <!-- Menu mobile séparé -->
        <div class="mobile-nav">
            <a href="ventes.php" <?php echo (isset($current_page) && $current_page === 'ventes') ? 'class="active"' : ''; ?>>Ventes</a>
            <a href="services.php" <?php echo (isset($current_page) && $current_page === 'services') ? 'class="active"' : ''; ?>>Services</a>
            <a href="projets.php" <?php echo (isset($current_page) && $current_page === 'projets') ? 'class="active"' : ''; ?>>Projets</a>
            <a href="contact.php" <?php echo (isset($current_page) && $current_page === 'contact') ? 'class="active"' : ''; ?>>Contact</a>
        </div>
    </header>