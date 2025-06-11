<?php
require_once '../config.php';

$tache_id = isset($_GET['tache_id']) ? (int) $_GET['tache_id'] : 0;
if (!$tache_id) {
    die("ID de la tâche manquant.");
}

// Récupération de la tâche
$stmt = $pdo->prepare("SELECT * FROM taches WHERE id = ?");
$stmt->execute([$tache_id]);
$tache = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tache) {
    die("Tâche introuvable.");
}

$projet_id = $tache['projet_id'];

// Récupérer les membres
$stmtMembres = $pdo->query("SELECT id, username FROM users WHERE role = 'membre'");
$membres = $stmtMembres->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $statut = $_POST['statut'];
    $commentaire = $_POST['commentaire'];
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;

    $stmt = $pdo->prepare("UPDATE taches SET titre = ?, description = ?, statut = ?, commentaire = ?, user_id = ? WHERE id = ?");
    $stmt->execute([$titre, $description, $statut, $commentaire, $user_id, $tache_id]);

     header("Location: liste_tache.php?projet_id=" . $projet_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une Tâche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<h2>Modifier la Tâche <?= $tache_id ?></h2>

<form method="POST" class="border p-4">
    <div class="mb-3">
        <label for="titre" class="form-label">Titre</label>
        <input type="text" class="form-control" name="titre" id="titre" value="<?= htmlspecialchars($tache['titre']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" name="description" id="description"><?= htmlspecialchars($tache['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label for="statut" class="form-label">Statut</label>
        <select name="statut" id="statut" class="form-select">
            <option value="non_commence" <?= $tache['statut'] == 'non_commence' ? 'selected' : '' ?>>Non commencé</option>
            <option value="en_cours" <?= $tache['statut'] == 'en_cours' ? 'selected' : '' ?>>En cours</option>
            <option value="termine" <?= $tache['statut'] == 'termine' ? 'selected' : '' ?>>Terminé</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="commentaire" class="form-label">Commentaire</label>
        <textarea class="form-control" name="commentaire" id="commentaire"><?= htmlspecialchars($tache['commentaire']) ?></textarea>
    </div>
    <div class="mb-3">
        <label for="user_id" class="form-label">Assigner à</label>
        <select name="user_id" id="user_id" class="form-select">
            <option value="">-- Aucun --</option>
            <?php foreach ($membres as $membre): ?>
                <option value="<?= $membre['id'] ?>" <?= $tache['user_id'] == $membre['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($membre['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="liste_tache.php?projet_id=<?= $projet_id ?>" class="btn btn-secondary">Annuler</a>
</form>

</body>
</html>
