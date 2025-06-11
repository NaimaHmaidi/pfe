<?php
require_once '../config.php';

$projet_id = isset($_GET['projet_id']) ? (int) $_GET['projet_id'] : 0;
if (!$projet_id) {
    die("ID du projet manquant.");
}

// Récupérer les membres
$stmtMembres = $pdo->query("SELECT id, username FROM users WHERE role = 'membre'");
$membres = $stmtMembres->fetchAll(PDO::FETCH_ASSOC);

// Traitement de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $statut = $_POST['statut'];
    $commentaire = $_POST['commentaire'];
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;

    $stmt = $pdo->prepare("INSERT INTO taches (titre, description, statut, commentaire, projet_id, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titre, $description, $statut, $commentaire, $projet_id, $user_id]);

    header("Location: liste_tache.php?projet_id=" . $projet_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Tâche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<h2>Ajouter une Tâche au Projet <?= $projet_id ?></h2>

<form method="POST" class="border p-4">
    <div class="mb-3">
        <label for="titre" class="form-label">Titre</label>
        <input type="text" class="form-control" name="titre" id="titre" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" name="description" id="description"></textarea>
    </div>
    <div class="mb-3">
        <label for="statut" class="form-label">Statut</label>
        <select name="statut" id="statut" class="form-select">
            <option value="non_commence">Non commencé</option>
            <option value="en_cours">En cours</option>
            <option value="termine">Terminé</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="commentaire" class="form-label">Commentaire</label>
        <textarea class="form-control" name="commentaire" id="commentaire"></textarea>
    </div>
    <div class="mb-3">
        <label for="user_id" class="form-label">Assigner à</label>
        <select name="user_id" id="user_id" class="form-select">
            <option value="">-- Aucun --</option>
            <?php foreach ($membres as $membre): ?>
                <option value="<?= $membre['id'] ?>"><?= htmlspecialchars($membre['username']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Ajouter</button>
   
</form>

</body>
</html>
