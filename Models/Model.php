<?php 

class Model {
    private $db;
    private static $instance = null;

    /**
     * Méthode permettant de se connecter a la bdd
     */
    private function __construct()
    {
        include "Utils/credentials.php";
        $this->db = new PDO($dsn, $login, $mdp);
        $this->db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Méthode permettant de récupérer un modèle car le constructeur est privé (Implémentation du Design Pattern Singleton)
     */
    public static function getModel() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Méthode permettant d'ajouter un nouveau produit
     * @param string $name
     * @param int $contenance
     * @param string $description
     */
    public function addProduct($name, $contenance, $description) {
        $req = $this->db->prepare('INSERT INTO Produits (nomP, contenanceP, description) VALUES (?, ?, ?)');
        $req->execute([$name, $contenance, $description]);
        return $this->db->lastInsertId();
    }

    /**
     * Méthode permettant de verifier si un produit existe deja 
     * @param string $name
     */
    public function productExists($name) {
        $req = $this->db->prepare('SELECT COUNT(*) FROM Produits WHERE nomP = ?');
        $req->bindValue(1, $name, PDO::PARAM_STR);
        $req->execute();
        $count = $req->fetchColumn();
        return $count > 0;
    }

    /**
     * Méthode permettant de lister tous les produits avec une liaison des tables dfans la bdd
     */
    public function listProduct() {
        $req = $this->db->prepare('SELECT Produits.idP, Produits.nomP, Produits.contenanceP, Produits.description,Quantite.quantite, Quantite.estimation
        FROM Produits
        LEFT JOIN Quantite ON Produits.idP = Quantite.idP ORDER BY Produits.nomP');
        $req->execute();
        return $req->fetchAll();
    }

    /**
     * Méthode permettant de filtrer la liste par le nom du produit
     * @param int $produitId
     */
    public function filtrerParId($produitId) {
        $req = $this->db->prepare('SELECT Produits.idP, Produits.nomP, Produits.contenanceP, Produits.description,Quantite.quantite, Quantite.estimation
        FROM produits 
        LEFT JOIN Quantite ON Produits.idP = Quantite.idP WHERE Produits.nomP = ?');
        $req->execute([$produitId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Méthode permettant de filtrer la liste par l'estimation
     * @param int $estimation
     */
    public function filtrerParEstimation($estimation)
    {
        $req = $this->db->prepare('SELECT Produits.idP, Produits.nomP, Produits.contenanceP, Produits.description,Quantite.quantite, Quantite.estimation
        FROM produits 
        LEFT JOIN Quantite ON Produits.idP = Quantite.idP WHERE Quantite.estimation <= ?');
        $req->execute([$estimation]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Méthode permettant de filtrer la liste par l'estimation et par le nom
     * @param int $produitId
     * @param int $estimation
     */
    public function filtrerParIdEtEstimation($produitId, $estimation)
    {
        $req = $this->db->prepare('SELECT Produits.idP, Produits.nomP, Produits.contenanceP, Produits.description,Quantite.quantite, Quantite.estimation
        FROM produits 
        LEFT JOIN Quantite ON Produits.idP = Quantite.idP WHERE Produits.nomP = ? AND Quantite.estimation <= ?');
        $req->execute([$produitId, $estimation]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Méthode permettant de recuperer tous les elements de la table Produits
     */    
    public function getProduit() {
        $req = $this->db->prepare('SELECT * FROM Produits ORDER BY nomP');
        $req->execute();
        return $req->fetchAll();
    }

    /**
     * Méthode permettant d'ajouter manuellement la quantité d'un produit
     * @param int $produit
     * @param int $aug
     */
    public function addQuantity($produit, $aug){
        $req = $this->db->prepare('SELECT COUNT(*) FROM Quantite WHERE idP = ?');
        $req->execute([$produit]);
        $exists = $req->fetchColumn() > 0;

        if ($exists) {
            // Mise à jour de l'estimation pour un produit avec une valeur existante
            $req = $this->db->prepare('UPDATE Quantite SET estimation = estimation + ? WHERE idP = ?');
            $req->execute([$aug, $produit]);
        } else {
            // Mise à jour de l'estimation pour un produit avec une valeur null
            $req = $this->db->prepare('INSERT INTO Quantite (idP, estimation) VALUES (?, ?)');
            $req->execute([$produit, $aug]);
        }
    }

    /**
     * Méthode permettant de diminuer manuellement la quantité d'un produit
     * @param int $produit
     * @param int $dim
     */
    public function remQuantity($produit, $dim) {
        // D'abord, récupérons la valeur actuelle d'estimation
        $req = $this->db->prepare('SELECT estimation FROM Quantite WHERE idP = ?');
        $req->execute([$produit]);
        $estimationActuelle = $req->fetchColumn();
    
        // Calculons la nouvelle estimation et assurons-nous qu'elle n'est pas négative
        $nouvelleEstimation = max(0, $estimationActuelle - $dim);
    
        // Mettons à jour l'estimation avec la nouvelle valeur qui n'est jamais négative
        $req = $this->db->prepare('UPDATE Quantite SET estimation = ? WHERE idP = ?');
        $req->execute([$nouvelleEstimation, $produit]);
        // Pas besoin de fetchAll puisque c'est une requête de mise à jour
    }

    /**
     * Méthode permettant de supprimer un produit de la liste
     * @param int $id
     */
    public function removeProduct($id) {
        $req = $this->db->prepare("DELETE FROM Produits WHERE idP = ?");
        $req->execute([$id]);
        return (bool) $req->rowCount();
    }
}
