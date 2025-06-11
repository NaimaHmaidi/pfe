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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Projets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            background: #343a40;
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
            padding: 30px 40px;
            flex: 1;
            overflow-y: auto;
        }
        h1 {
            color: #0d6efd;
            font-weight: 700;
            margin-bottom: 30px;
        }
        table th, table td {
            vertical-align: middle;
            text-align: center;
        }
        .btn-warning, .btn-danger, .btn-success {
            min-width: 100px;
            font-weight: 600;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #e9f0ff;
        }
        .table thead {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
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

        <h1>Liste des Projets</h1>

        <a href="ajouter.php" class="btn btn-success mb-3">Ajouter un projet</a>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Statut</th>
                    <th>Équipe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM projets");
            while ($row = $stmt->fetch()) {
                $stmt_users = $pdo->prepare("SELECT COUNT(*) FROM projets_users WHERE projet_id = ?");
                $stmt_users->execute([$row['id']]);
                $nombre_membres = $stmt_users->fetchColumn();

                $statut = $nombre_membres > 0 ? "<span class='badge bg-success'>Assigné</span>" : "<span class='badge bg-secondary'>Non assigné</span>";

                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['titre']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['date_debut']}</td>
                    <td>{$row['date_fin']}</td>
                    <td>$statut</td>
                    <td>{$nombre_membres} membre(s)</td>
                    <td>
                        <a href='modifier.php?id={$row['id']}' class='btn btn-warning btn-sm'>Modifier</a>
                        <a href='supprimer.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Supprimer ce projet ?');\">Supprimer</a>
                    </td>
                </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
