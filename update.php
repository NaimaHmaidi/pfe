<?php 
ob_start();  // Démarre la mise en tampon de sortie
include '../config.php';

// Vérifier si l'ID est présent et valide
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Erreur : ID invalide ou manquant dans l'URL.");
}
$id=$_GET['id'];
$stmt=$pdo->prepare(query: 'SELECT * FROM users WHERE id = ? '); 
$stmt->execute(params: [$id]);
$user=$stmt->fetch(mode: PDO::FETCH_ASSOC);

if (!$user) {
    die("Erreur : Utilisateur introuvable !");
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username=$_POST['username'];
    $role=$_POST['role'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($role)) {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $hashedPassword, $role, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $role, $id]);
        }

        $_SESSION['success_message'] = "Utilisateur modifié avec succès.";
        header("Location: dashboard_admin.php?page=users");
        exit();
    } else {
        $error = "Tous les champs obligatoires doivent être remplis.";
    }
}
ob_end_flush();  // Envoie le contenu tamponné au navigateur

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
<div class="container mt-5">

    <h2>Modifier un étudiant</h2>

     <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" id="username" class="form-control" value="<?= $user['username']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas modifier)</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Entrer votre Mot de passe">
        </div>
        
        <div class="mb-3">
            <label for="role" class="form-label">Rôle</label>
            <select name="role" id="role" class="form-control" required>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrateur</option>
                <option value="chef" <?= $user['role'] == 'chef' ? 'selected' : '' ?>>Chef de projet</option>
                <option value="membre" <?= $user['role'] == 'membre' ? 'selected' : '' ?>>Membre</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="dashboard_admin.php?page=users" class="btn btn-secondary">Annuler</a>
    </form>
    
</div> 
</body>
</html>