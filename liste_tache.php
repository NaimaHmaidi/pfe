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
$stmtUser = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);
$connected_username = $user ? $user['username'] : 'Utilisateur inconnu';

// Récupérer l'ID du projet
$projet_id = isset($_GET['projet_id']) ? (int) $_GET['projet_id'] : 0;
if (!$projet_id) {
    die("ID du projet manquant.");
}

// Récupérer les tâches du projet avec le nom de l'utilisateur assigné
$stmt = $pdo->prepare("SELECT t.*, u.username AS membre FROM taches t LEFT JOIN users u ON t.user_id = u.id WHERE t.projet_id = ?");
$stmt->execute([$projet_id]);
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Suppression
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM taches WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: tache.php?projet_id=" . $projet_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #dcdee0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }
        .container-fluid {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 264px;
            background: #212529; /* Bootstrap dark */
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
            color: #212529;
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
        /* Content */
        .content {
            margin-left: 264px;
            padding: 20px 40px;
            flex: 1;
            overflow-y: auto;
            background-color: #f0f2f5;
        }

        h2 {
            color: #0d6efd;
            margin-bottom: 30px;
            font-weight: 700;
        }

        table th, table td {
            vertical-align: middle;
            text-align: center;
        }

        .btn-success, .btn-warning, .btn-danger {
            min-width: 100px;
        }

        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #e9f0ff;
        }

        .table thead {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
        }

        .btn {
            font-size: 14px;
            font-weight: 600;
        }

        a.btn-secondary {
            margin-top: 20px;
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

            <h2>Gestion des Tâches du Projet <?= $projet_id ?></h2>

            <a href="ajouter_tache.php?projet_id=<?= $projet_id ?>" class="btn btn-success mb-3"><i class="fas fa-plus"></i> Ajouter une tâche</a>

            <!-- Liste des Tâches -->
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Commentaire</th>
                        <th>Assigné à</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taches as $tache): ?>
                        <tr>
                            <td><?= htmlspecialchars($tache['titre']) ?></td>
                            <td><?= htmlspecialchars($tache['description']) ?></td>
                            <td><?= htmlspecialchars($tache['statut']) ?></td>
                            <td><?= htmlspecialchars($tache['commentaire']) ?></td>
                            <td><?= htmlspecialchars($tache['membre'] ?? 'Non assignée') ?></td>
                            <td><?= $tache['date_creation'] ?></td>
                            <td>
                                <a href="modifier_tache.php?projet_id=<?= $projet_id ?>&tache_id=<?= $tache['id'] ?>" class="btn btn-warning btn-sm mb-1">Modifier</a>
                                <a href="?projet_id=<?= $projet_id ?>&delete=<?= $tache['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette tâche ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

           
        </div>
    </div>
</body>
</html>
