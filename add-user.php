<?php

//la communication avec la bd a travers require
include '../config.php';
//verification post et recuperation des donne avec post
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    //si lemail ou nom ne sont pas vide sil sont vide mayemchich 
    if (!empty($username) && !empty($role) && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    //insertion des données dans la bd najem nekhdem bl ?? kima fl create 
    $stmt=$pdo->prepare("INSERT INTO users(username, password, role) VALUES (:username, :password, :role)");
    //l'execution de la req a travers execute puisque avent name et email ':' on met l'affectation
    $stmt->execute(['username' => $username,'password' => $hashedPassword,'role' => $role]);
    //yraja3ni lindex khater ki tsir l'ajout yhezni luser wena nheb nchouf liste f index.php
    $_SESSION['success_message'] = "Utilisateur ajouté avec succès.";
    header(header:"Location: dashboard_admin.php?page=users");
    //yokhroj mn adduser.php
    exit();
     } else {
        $error = "Tous les champs sont obligatoires.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!--google material icon-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons"rel="stylesheet">
    <title>Ajouter un étudiant</title>
</head>
<body>

    <div class="container mt-3">   
        <h2>Ajouter un utilisateur</h2>
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
                <!-- l'ajouut ml page add-user.php n3aytoulha fiha code l'ajout et  l'enctype du formulaire pour pouvoir envoyer des fichiers.-->
                <form action="add-user.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="username">Nom d'utilisateur :</label>
                        <input type="text" name="username" class="form-control" id="username" placeholder="Entrer votre nom" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Mot de passe :</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Entrer votre Mot de passe" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="role">Rôle</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="admin">Administrateur</option>
                            <option value="chef">Chef de projet</option>
                            <option value="membre">Membre</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Ajouter l'utilisateur</button>
                        <a href="dashboard_admin.php?page=users" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
</body>
</html>