<?php
require_once 'config/config.php';

// Définir le titre de la page
$page_title = "Le Coin de Clem - Ventes d'occasion et services";

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

// Inclure le header
include 'includes/header.php';
?>

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

<?php
// Inclure le footer
include 'includes/footer.php';
?>