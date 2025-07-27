<?php
global $pdo;
require_once 'config/config.php';

// Définir le titre de la page et la page active
$page_title = "Ventes d'occasion - Le Coin de Clem";
$current_page = 'ventes';

// Paramètres de pagination
$items_per_page = 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// Filtres
$categorie = isset($_GET['categorie']) && $_GET['categorie'] !== '' ? $_GET['categorie'] : null;
$etat = isset($_GET['etat']) && $_GET['etat'] !== '' ? $_GET['etat'] : null;
$prix_min = isset($_GET['prix_min']) && $_GET['prix_min'] !== '' ? floatval($_GET['prix_min']) : null;
$prix_max = isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? floatval($_GET['prix_max']) : null;
$search = isset($_GET['search']) && $_GET['search'] !== '' ? trim($_GET['search']) : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

// Construction de la requête
$where_conditions = ["a.statut = 'disponible'"];
$params = [];

if ($categorie) {
    $where_conditions[] = "a.categorie = ?";
    $params[] = $categorie;
}

if ($etat) {
    $where_conditions[] = "a.etat = ?";
    $params[] = $etat;
}

if ($prix_min !== null) {
    $where_conditions[] = "a.prix >= ?";
    $params[] = $prix_min;
}

if ($prix_max !== null) {
    $where_conditions[] = "a.prix <= ?";
    $params[] = $prix_max;
}

if ($search) {
    $where_conditions[] = "(a.titre LIKE ? OR a.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = implode(' AND ', $where_conditions);

// Ordre de tri
$order_clause = "ORDER BY ";
switch ($sort) {
    case 'prix_asc':
        $order_clause .= "a.prix ASC";
        break;
    case 'prix_desc':
        $order_clause .= "a.prix DESC";
        break;
    case 'nom_asc':
        $order_clause .= "a.titre ASC";
        break;
    case 'nom_desc':
        $order_clause .= "a.titre DESC";
        break;
    case 'date_asc':
        $order_clause .= "a.date_creation ASC";
        break;
    default:
        $order_clause .= "a.date_creation DESC";
}

// Requête principale
$sql = "SELECT a.*, p.nom_fichier as photo_principale 
        FROM annonces a 
        LEFT JOIN photos p ON a.id = p.annonce_id AND p.ordre = 1
        WHERE $where_clause 
        $order_clause 
        LIMIT $items_per_page OFFSET $offset";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $annonces = $stmt->fetchAll();

    // Compter le total pour la pagination
    $count_sql = "SELECT COUNT(*) FROM annonces a WHERE $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Récupérer les catégories et états disponibles
    $categories = $pdo->query("SELECT DISTINCT categorie FROM annonces WHERE statut = 'disponible' ORDER BY categorie")->fetchAll(PDO::FETCH_COLUMN);
    $etats = ['neuf', 'très bon état', 'bon état', 'état satisfaisant', 'pour pièces'];

} catch (PDOException $e) {
    $annonces = [];
    $total_pages = 0;
    $categories = [];
    $etats = [];
}
include 'includes/header.php';
?>
<main class="main-content">
    <div class="page-header">
        <h1>Ventes d'occasion</h1>
        <p>Découvrez mes objets d'occasions</p>
    </div>

    <!-- Filtres -->
    <form class="filters" method="GET" action="ventes.php">
        <div class="filters-container">
            <div class="filters-row">
                <div class="filter-group">
                    <label for="search">Rechercher</label>
                    <input type="text" id="search" name="search" placeholder="Nom ou description..." value="<?php echo htmlspecialchars(isset($search) ? $search : ''); ?>">
                </div>

                <div class="filter-group">
                    <label for="categorie">Catégorie</label>
                    <select id="categorie" name="categorie">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $categorie === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="etat">État</label>
                    <select id="etat" name="etat">
                        <option value="">Tous les états</option>
                        <?php foreach ($etats as $et): ?>
                            <option value="<?php echo htmlspecialchars($et); ?>" <?php echo $etat === $et ? 'selected' : ''; ?>>
                                <?php echo ucfirst($et); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="filters-row-2">
                <div class="filter-group">
                    <label>Prix (€)</label>
                    <div class="price-range">
                        <input type="number" name="prix_min" placeholder="Min" value="<?php echo isset($prix_min) ? $prix_min : ''; ?>" step="0.01">
                        <span>-</span>
                        <input type="number" name="prix_max" placeholder="Max" value="<?php echo isset($prix_max) ? $prix_max : ''; ?>" step="0.01">
                    </div>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="ventes.php" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Résultats -->
    <div class="results-header">
        <div class="results-count">
            <?php echo $total_items; ?> annonce<?php echo $total_items > 1 ? 's' : ''; ?> trouvée<?php echo $total_items > 1 ? 's' : ''; ?>
        </div>

        <div class="sort-controls">
            <label for="sort">Trier par :</label>
            <select id="sort" name="sort" onchange="updateSort(this.value)">
                <option value="date_desc" <?php echo $sort === 'date_desc' ? 'selected' : ''; ?>>Plus récent</option>
                <option value="date_asc" <?php echo $sort === 'date_asc' ? 'selected' : ''; ?>>Plus ancien</option>
                <option value="prix_asc" <?php echo $sort === 'prix_asc' ? 'selected' : ''; ?>>Prix croissant</option>
                <option value="prix_desc" <?php echo $sort === 'prix_desc' ? 'selected' : ''; ?>>Prix décroissant</option>
                <option value="nom_asc" <?php echo $sort === 'nom_asc' ? 'selected' : ''; ?>>Nom A-Z</option>
                <option value="nom_desc" <?php echo $sort === 'nom_desc' ? 'selected' : ''; ?>>Nom Z-A</option>
            </select>
        </div>
    </div>

    <!-- Grille des annonces -->
    <?php if (empty($annonces)): ?>
        <div class="empty-state">
            <h3>Aucune annonce trouvée</h3>
            <p>Essayez de modifier vos critères de recherche.</p>
        </div>
    <?php else: ?>
        <div class="annonces-grid">
            <?php foreach ($annonces as $annonce): ?>
                <div class="annonce-card" onclick="window.location.href='annonce.php?id=<?php echo $annonce['id']; ?>'">
                    <div class="annonce-image">
                        <?php if ($annonce['photo_principale']): ?>
                            <img src="uploads/annonces/<?php echo $annonce['id']; ?>/<?php echo htmlspecialchars($annonce['photo_principale']); ?>"
                                 alt="<?php echo htmlspecialchars($annonce['titre']); ?>"
                                 onerror="this.parentElement.innerHTML='📷';">
                        <?php else: ?>
                            📷
                        <?php endif; ?>
                    </div>

                    <div class="annonce-content">
                        <h3 class="annonce-title"><?php echo htmlspecialchars($annonce['titre']); ?></h3>
                        <div class="annonce-price"><?php echo formatPrice($annonce['prix']); ?></div>

                        <div class="annonce-meta">
                            <span class="annonce-category"><?php echo htmlspecialchars($annonce['categorie']); ?></span>
                            <span class="annonce-etat" style="background-color: <?php echo getEtatColor($annonce['etat']); ?>">
                                    <?php echo ucfirst($annonce['etat']); ?>
                                </span>
                        </div>

                        <div class="annonce-date">
                            Publié <?php echo timeAgo($annonce['date_creation']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">« Premier</a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">‹ Précédent</a>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);

                for ($i = $start; $i <= $end; $i++):
                    ?>
                    <?php if ($i == $page): ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Suivant ›</a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>">Dernier »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>