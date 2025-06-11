<?php
include '../config.php';

// Récupérer les utilisateurs ayant le rôle "membre"
$utilisateurs = $pdo->query("SELECT id, username FROM users WHERE role = 'membre'")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $membres = $_POST['membres'] ?? [];

    // Insertion du projet
    $stmt = $pdo->prepare("INSERT INTO projets (titre, description, date_debut, date_fin) VALUES (?, ?, ?, ?)");
    $stmt->execute([$titre, $description, $date_debut, $date_fin]);

    $projet_id = $pdo->lastInsertId(); // ID du projet créé

    // Assigner les membres sélectionnés au projet
    if (!empty($membres)) {
        $stmt = $pdo->prepare("INSERT INTO projets_users (projet_id, users_id) VALUES (?, ?)");
        foreach ($membres as $user_id) {
            $stmt->execute([$projet_id, $user_id]);
        }
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Ajouter un Projet</h1>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="titre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Date début</label>
            <input type="date" name="date_debut" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Date fin</label>
            <input type="date" name="date_fin" class="form-control" required>
        </div>
        <div class="mb-3">
    <label class="form-label">Assigner des membres</label>
    <select name="membres[]" class="form-select" multiple size="5">
        <?php foreach ($utilisateurs as $user): ?>
            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
        <?php endforeach; ?>
    </select>
    
</div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
</body>
</html>
