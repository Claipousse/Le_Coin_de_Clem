<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'coin_de_clem');
define('DB_USER', 'root'); //A changer
define('DB_PASS', 'root'); //A changer

//Connexion à la BDD
try  {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4" , DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

//Fonctions utilitaires
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    if ($time < 60) return "A l'instant";
    if ($time < 3600) return floor($time / 60) . ' min';
    if ($time < 86400) return floor($time / 3600) . ' h';
    if ($time < 2592000) return floor($time / 86400) . ' j';
    if ($time < 31536000) return floor($time / 31536000) . ' mois';
    return floor($time / 31536000) . ' an' . (floor($time / 31536000) > 1 ? ' s' : '');
}

function getEtatColor($etat) {
    switch ($etat) {
        case 'Neuf': return '#1e5631';
        case 'Très bon état': return '#2d7e3e';
        case 'Bon état': return '#4caf50';
        case 'Etat satisfaisant': return '#ffeb3b';
        case 'Pour pièces': return '#ff9800';
        default: return '#6c757d';
    }
}
?>