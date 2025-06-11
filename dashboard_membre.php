<?php
session_start();
require_once '../config.php';

// Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
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

// Statistiques des tâches par statut
$sql = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) AS en_cours,
    SUM(CASE WHEN statut = 'non_commence' THEN 1 ELSE 0 END) AS non_commence,
    SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) AS termine
FROM taches";
$stmt = $pdo->query($sql);
$stats = $stmt->fetch();

$total = $stats['total'] ?? 0;
$total_en_cours = $stats['en_cours'] ?? 0;
$total_non_commence = $stats['non_commence'] ?? 0;
$total_termine = $stats['termine'] ?? 0;

// Récupération de tous les projets
$stmt = $pdo->query("SELECT * FROM projets");
$projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_projets = count($projets);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard des Tâches</title>
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
        .content {
            margin-left: 264px;
            padding: 20px;
            flex: 1;
            background-color: #dcdee0;
            overflow-y: auto;
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
        <a href="dashboard_membre.php" class="active"><i class="fas fa-tasks"></i> Dashboard des Tâches</a>
        <a href="tache.php"><i class="fas fa-list"></i> Liste des Tâches</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <!-- Contenu -->
    <div class="content">
        <div class="header">
            Bienvenue, <?= htmlspecialchars($connected_username) ?> !
        </div>

        <h1 class="mb-4">Dashboard des Tâches</h1>

        <div class="row g-4">
            <div class="col-md-5">
                <div class="card text-white bg-dark">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total des Projets</h5>
                        <p class="card-text fs-3"><?= $total_projets ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total des Tâches</h5>
                        <p class="card-text fs-3"><?= $total ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h5 class="card-title">En Cours</h5>
                        <p class="card-text fs-3"><?= $total_en_cours ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card text-white bg-secondary">
                    <div class="card-body text-center">
                        <h5 class="card-title">Non Commencées</h5>
                        <p class="card-text fs-3"><?= $total_non_commence ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-10">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h5 class="card-title">Terminées</h5>
                        <p class="card-text fs-3"><?= $total_termine ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
