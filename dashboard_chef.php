<?php
session_start();
require_once '../config.php';

// Vérifie si l'utilisateur est connecté, sinon redirige
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Récupération du nom de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$connected_username = $user ? $user['username'] : 'Utilisateur inconnu';

// Statistiques projets
$stats = $pdo->query("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN pu.projet_id IS NOT NULL THEN 1 ELSE 0 END) AS assignes_unique
    FROM projets p
    LEFT JOIN projets_users pu ON p.id = pu.projet_id
")->fetch();

$total = (int)$stats['total'];
$assignes_unique = (int)$stats['assignes_unique'];
$non_assignes = $total - $assignes_unique;

// Liste des projets + membres
$projets = $pdo->query("
    SELECT 
        p.id,
        p.titre,
        p.date_debut,
        p.date_fin,
        GROUP_CONCAT(u.username SEPARATOR ', ') AS membres
    FROM projets p
    LEFT JOIN projets_users pu ON p.id = pu.projet_id
    LEFT JOIN users u ON pu.users_id = u.id
    GROUP BY p.id
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard des Projets</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/lineicons@3.0.0/dist/lineicons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #dcdee0;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .container-fluid {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 264px;
            background: var(--bs-dark);
            color: #fff;
            height: 100%;
            padding-top: 20px;
            z-index: 1000;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 15px 20px;
            display: block;
            font-weight: bold;
            border-radius: 5px;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #adb5bd;
            color: #fff;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo {
            width: 50%;
            max-width: 200px;
            height: auto;
            border-radius: 15px;
        }
        .content {
            margin-left: 264px;
            padding: 20px;
            flex: 1;
            background-color: #dcdee0;
            overflow-y: auto;
        }
        .header {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo-container">
                <img src="images/logo.webp" alt="Logo" class="logo">
            </div>
            <a href="dashboard_chef.php" class="active"><i class="fas fa-users"></i> Tableau de bord</a>
            <a href="../projet/index.php"><i class="fas fa-cogs"></i> Projets</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="header">
                Bienvenue, <?= htmlspecialchars($connected_username) ?> !
            </div>

            <h1 class="mb-4">Gestion des Projets</h1>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total projets</h5>
                            <p class="display-6"><?= $total ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Projets assignés</h5>
                            <p class="display-6"><?= $assignes_unique ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Projets non assignés</h5>
                            <p class="display-6"><?= $non_assignes ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tu peux ajouter ici le tableau ou la liste des projets avec leurs membres -->
        </div>
    </div>
</body>
</html>
