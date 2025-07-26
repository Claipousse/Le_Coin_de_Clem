<?php
require_once 'config/config.php';

// Récupération des statistiques pour l'accueil
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_annonces FROM annonces WHERE statut = 'disponible'");
    $stats = $stmt->fetch();
    $total_annonces = $stats['total_annonces'];
    
    $stmt = $pdo->query("SELECT categorie, COUNT(*) as count FROM annonces WHERE statut = 'disponible' GROUP BY categorie");
    $categories_stats = $stmt->fetchAll();
} catch (PDOException $e) {
    $total_annonces = 0;
    $categories_stats = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Coin de Clem - Ventes d'occasion et services</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-purple: #8b5cf6;
            --light-purple: #a78bfa;
            --dark-purple: #7c3aed;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --border-light: #e2e8f0;
            --white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Header */
        header {
            background: var(--white);
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            align-items: center;
            gap: 40px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary-purple);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-purple);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-purple);
            text-decoration: none;
            order: 2;
        }

        .nav-left {
            order: 1;
        }

        .nav-right {
            order: 3;
        }

        /* Menu burger mobile */
        .burger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 4px;
        }

        .burger span {
            width: 25px;
            height: 3px;
            background: var(--text-dark);
            transition: 0.3s;
        }

        /* Hero Section avec image de fond */
        .hero {
            background: url('assets/images/background.png') center center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            color: white;
            padding: 140px 20px 80px;
            text-align: center;
            min-height: 70vh;
        }

        /* Overlay pour garder le texte lisible */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(139, 92, 246, 0.7); /* Overlay violet semi-transparent */
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
            animation: fadeInUp 1s ease;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 60px;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 60px;
            opacity: 0.95;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Sections principales */
        .main-sections {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
        }

        .section-card {
            background: var(--white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .section-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-purple), var(--light-purple));
        }

        .section-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .section-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-purple), var(--light-purple));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            font-size: 24px;
            color: white;
        }

        .section-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        .section-card p {
            color: var(--text-gray);
            margin-bottom: 25px;
            line-height: 1.7;
        }

        .btn {
            display: inline-block;
            background: var(--primary-purple);
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: var(--dark-purple);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: var(--primary-purple);
            color: white;
            border: 2px solid var(--primary-purple);
        }

        .btn-outline:hover {
            background: var(--dark-purple);
            border-color: var(--dark-purple);
        }

        /* Footer */
        footer {
            background: var(--text-dark);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        footer p {
            opacity: 0.8;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Menu mobile */
        .mobile-nav {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            width: 100%;
            background: var(--white);
            flex-direction: column;
            padding: 20px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            box-shadow: var(--shadow);
            z-index: 999;
        }

        .mobile-nav.active {
            display: flex;
            transform: translateX(0);
        }

        .mobile-nav a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-light);
            transition: color 0.3s ease;
        }

        .mobile-nav a:hover {
            color: var(--primary-purple);
        }

        .mobile-nav a:last-child {
            border-bottom: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav {
                justify-content: space-between;
            }

            .nav-links {
                display: none;
            }

            .logo {
                order: 1;
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
            }

            .burger {
                display: flex;
                order: 2;
                margin-left: auto;
            }

            .hero {
                background-attachment: scroll;
                min-height: 60vh;
                padding: 140px 20px 60px;
            }

            .hero h1 {
                font-size: 2.5rem;
                margin-bottom: 40px;
            }

            .hero p {
                font-size: 1.1rem;
                margin-bottom: 40px;
            }

            .stats {
                flex-direction: column;
                gap: 30px;
            }

            .main-sections {
                grid-template-columns: 1fr;
                padding: 60px 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul class="nav-links nav-left">
                <li><a href="projets.php">Projets</a></li>
                <li><a href="services.php">Services</a></li>
            </ul>
            
            <a href="index.php" class="logo">Le Coin de Clem</a>
            
            <ul class="nav-links nav-right">
                <li><a href="ventes.php">Ventes</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>

            <div class="burger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>

        <!-- Menu mobile séparé -->
        <div class="mobile-nav">
            <a href="ventes.php">Ventes</a>
            <a href="services.php">Services</a>
            <a href="projets.php">Projets</a>
            <a href="contact.php">Contact</a>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Le Coin de Clem</h1>
            <p>Découvrez mes services informatiques personnalisés ainsi que mes ventes d'objets d'occasions</p>
            
            <div class="stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $total_annonces; ?></span>
                    <span class="stat-label">Annonces</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">4</span>
                    <span class="stat-label">Services</span>
                </div>
            </div>
        </div>
    </section>

    <section class="main-sections">
        <div class="section-card">
            <div class="section-icon">🛒</div>
            <h3>Ventes d'occasion</h3>
            <p>Explorez une liste diverses d'objets d'occasion dont je souhaite me débarrasser et qui n'attendent qu'un nouveau propriétaire.</p>
            <a href="ventes.php" class="btn">Voir les annonces</a>
        </div>

        <div class="section-card">
            <div class="section-icon">⚙️</div>
            <h3>Services</h3>
            <p>Besoin d'aide avec votre matériel ? Je propose des services de dépannage informatique, d'optimisation PC et plus encore.</p>
            <a href="services.php" class="btn">Découvrir les services</a>
        </div>

        <div class="section-card">
            <div class="section-icon">💼</div>
            <h3>Mes projets</h3>
            <p>Découvrez mes réalisations et projets techniques. De la programmation aux modifications matérielles.</p>
            <a href="projets.php" class="btn btn-outline">Voir le portfolio</a>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 Le Coin de Clem.</p>
    </footer>

    <script>
        // Menu burger mobile
        const burger = document.querySelector('.burger');
        const mobileNav = document.querySelector('.mobile-nav');

        burger.addEventListener('click', () => {
            mobileNav.classList.toggle('active');
            burger.classList.toggle('active');
        });

        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease forwards';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.section-card').forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html>