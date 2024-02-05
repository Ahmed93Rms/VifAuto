<?php

//Message d'erreur
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Démarrage de la session
session_start();

// Inclusion des modèles et des contrôleurs
require_once 'Models/Model.php';
require_once 'Controllers/Controller.php';
include_once 'Utils/credentials.php';

//Liste des contrôleurs
$controllers = ["home"];
//Nom du contrôleur par défaut
$controller_default = "home";

//On teste si le paramètre controller existe et correspond à un contrôleur de la liste $controllers

// On teste si le paramètre controller existe et correspond à un contrôleur de la liste $controllers
if (isset($_GET['controller']) and in_array($_GET['controller'], $controllers)) {
    $nom_controller = $_GET['controller'];
} else {
    $nom_controller = $controller_default;
}

// On détermine le nom de la classe du contrôleur
$nom_classe = 'Controller_' . $nom_controller;

// Définir la variable $nom_fichier avec le chemin correct vers le fichier du contrôleur
$nom_fichier = 'Controllers/' . $nom_classe . '.php';

// Si le fichier existe et est accessible en lecture
if (is_readable($nom_fichier)) {
    // On l'inclut et on instancie un objet de cette classe
    include_once $nom_fichier;
    new $nom_classe();
} else {
    die("Error 404: not found!");
}
