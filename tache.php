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

// Récupérer tous les projets avec le total des tâches associées
$stmt = $pdo->query("
    SELECT p.id, p.titre, COUNT(t.id) AS total_taches
    FROM projets p
    LEFT JOIN taches t ON t.projet_id = p.id
    GROUP BY p.id, p.titre
");
$projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Liste des Projets</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
  
  <style>
    body {
      background-color: #dcdee0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      color: #333;
    }
    .container-fluid {
      display: flex;
      height: 100vh;
    }
    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 264px;
      background: var(--bs-dark, #212529);
      color: #fff;
      height: 100%;
      padding-top: 20px;
      overflow-y: auto;
    }
    .sidebar a {
      color: #adb5bd;
      text-decoration: none;
      padding: 15px 20px;
      display: block;
      font-weight: 600;
      border-radius: 5px;
      transition: background-color 0.3s, color 0.3s;
    }
    .sidebar a:hover,
    .sidebar a.active {
      background-color: #adb5bd;
      color: #000;
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
      padding: 30px 40px;
      flex: 1;
      overflow-y: auto;
      background: #f0f2f5;
    }
    h1 {
      text-align: center;
      margin-bottom: 40px;
      color: #0d6efd;
      text-shadow: 1px 1px 2px #b0c4de;
    }
    table {
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      background: #fff;
    }
    thead {
      background-color: #0d6efd;
      color: white;
      font-weight: 600;
      font-size: 1.1rem;
    }
    tbody tr:hover {
      background-color: #e7f1ff;
      transition: background-color 0.3s ease;
    }
    .btn-primary {
      background-color: #0a58ca;
      border: none;
      font-weight: 600;
      box-shadow: 0 4px 10px rgba(10,88,202,0.3);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #084298;
      box-shadow: 0 6px 14px rgba(8,66,152,0.5);
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
        <img src="images/logo.webp" alt="Logo" class="logo" />
      </div>
     <a href="dashboard_membre.php" class="active"><i class="fas fa-tasks"></i> Dashboard des Tâches</a>
            <a href="tache.php"><i class="fas fa-list"></i> Liste des Tâches</a>
            
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <!-- Contenu principal -->
    <div class="content">
      <div class="header">
        Bienvenue, <?= htmlspecialchars($connected_username) ?> !
      </div>

      <h1>Liste des Projets avec les tâches</h1>

      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom du projet</th>
            <th>Total des tâches</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($projets as $projet): ?>
          <tr>
            <td><?= htmlspecialchars($projet['id']) ?></td>
            <td><?= htmlspecialchars($projet['titre']) ?></td>
            <td><?= (int)$projet['total_taches'] ?></td>
            <td>
              <a href="liste_tache.php?projet_id=<?= $projet['id'] ?>" class="btn btn-primary btn-sm">Tâches</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
