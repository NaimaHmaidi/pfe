<?php

include '../config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin')) {
    header('Location: login.php');
    exit();
}

// Le résultat est stocké dans $stmt, qui est un objet PDOStatement contenant les résultats de la requête.
$stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY id ASC"); 
// Récupération des résultats sous forme de tableau associatif avec fetchAll(PDO::FETCH_ASSOC).
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lineicons@3.0.0/dist/lineicons.css">

    <title>Document</title>
    <style>
    .title-spacing {
    margin-bottom: 30px; /* Ajuste cette valeur selon ton besoin */
}
span {
    font-family: 'Line Awesome Free' !important;
    font-weight: 900 !important;
}
    </style>
</head>
<body>
<div class="container mt-3 table-container">
    <h3 class="mt-3 title-spacing">La liste des utilisateurs</h3>
    <input type="form-control" id="searchBar" class="form-control mb-4" placeholder="Rechercher un utilisateurs...">
    <table class="table table-striped table-responsive">
    <thead class="table-secondary">
        <tr>
            <th><span class="la la-arrow-up"></span>ID</th>
            <th><span class="la la-arrow-up"></span>Nom d'utilisateur</th>
            <th><span class="la la-arrow-up"></span>Rôle</th>
            <th><span class="la la-arrow-up"></span>Actions</th>
        </tr>
    </thead>
    <tbody id="contactTable">
        <!-- La récupération des données de $users -->
        <?php foreach ($users as $user) : ?>
        <tr>
            <td><?= $user['id']; ?></td>
            <td><?= $user['username']; ?></td>
            <td><?= $user['role']; ?></td>
            <td>
                <a href="dashboard_admin.php?page=add-user&id=<?= $user['id']; ?>"  class="btn btn-primary btn-sm" class="sidebar-link">Ajouter</a>
                <a href="dashboard_admin.php?page=update-user&id=<?= $user['id'] ?>"  class="btn btn-warning btn-sm" class="sidebar-link">Modifier</a>
                <a href="delete-user.php?id=<?= $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
            </td> 
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script src="js/recherche.js"></script>
<a href="export-etudiants.php" class="btn btn-success">Exporter la liste des utilisateurs en CSV</a>


</div>
</body>
</html> 