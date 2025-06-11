<?php
include '../config.php';

$id = $_GET['id'];

// Récupération du projet
$stmt = $pdo->prepare("SELECT * FROM projets WHERE id = ?");
$stmt->execute([$id]);
$projet = $stmt->fetch();

// Récupération des membres avec rôle "membre"
$utilisateurs = $pdo->query("SELECT id, username FROM users WHERE role = 'membre'")->fetchAll();

// Récupération des membres déjà assignés à ce projet
$stmt = $pdo->prepare("SELECT users_id FROM projets_users WHERE projet_id = ?");
$stmt->execute([$id]);
$membres_assignes = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $membres = $_POST['membres'] ?? [];

    // Mise à jour du projet
    $stmt = $pdo->prepare("UPDATE projets SET titre=?, description=?, date_debut=?, date_fin=? WHERE id=?");
    $stmt->execute([$titre, $description, $date_debut, $date_fin, $id]);

    // Mise à jour des affectations : on supprime d'abord les anciennes
    $pdo->prepare("DELETE FROM projets_users WHERE projet_id = ?")->execute([$id]);

    // Puis on insère les nouvelles affectations
    if (!empty($membres)) {
        $stmt = $pdo->prepare("INSERT INTO projets_users (projet_id, users_id) VALUES (?, ?)");
        foreach ($membres as $user_id) {
            $stmt->execute([$id, $user_id]);
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
    <title>Modifier un Projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Modifier un Projet</h1>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($projet['titre']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($projet['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Date début</label>
            <input type="date" name="date_debut" class="form-control" value="<?= $projet['date_debut'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Date fin</label>
            <input type="date" name="date_fin" class="form-control" value="<?= $projet['date_fin'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Membres assignés</label>
            <select name="membres[]" class="form-select" multiple size="5">
                <?php foreach ($utilisateurs as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= in_array($user['id'], $membres_assignes) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
