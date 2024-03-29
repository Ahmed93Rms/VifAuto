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
        $req = $this->db->prepare('SELECT Produits.idP, Produits.nomP, Produits.contenanceP, Produits.description, Produits.alerte, Quantite.quantite, Quantite.estimation
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
        $req = $this->db->prepare('SELECT Produits.idP, Produits.nomP, Produits.contenanceP, Produits.description, Produits.alerte, Quantite.quantite, Quantite.estimation
        FROM produits 
        LEFT JOIN Quantite ON Produits.idP = Quantite.idP WHERE Produits.nomP = ? ORDER BY Produits.nomP');
        $req->execute([$produitId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Méthode permettant de filtrer la liste par l'estimation
     * @param int $estimation
     */
    public function filtrerParEstimation($estimation)
    {
        $req = $this->db->prepare('SELECT Produits.idP, Produits.nomP, Produits.contenanceP, Produits.description, Produits.alerte, Quantite.quantite, Quantite.estimation
        FROM produits 
        LEFT JOIN Quantite ON Produits.idP = Quantite.idP WHERE Quantite.estimation <= ? ORDER BY Produits.nomP');
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
        $req = $this->db->prepare('SELECT Produits.idP, Produits.nomP, Produits.contenanceP, Produits.description, Produits.alerte, Quantite.quantite, Quantite.estimation
        FROM produits 
        LEFT JOIN Quantite ON Produits.idP = Quantite.idP WHERE Produits.nomP = ? AND Quantite.estimation <= ? ORDER BY Produits.nomP');
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
            $req = $this->db->prepare('UPDATE Quantite SET estimation = estimation + ? WHERE idP = ?');
            $req->execute([$aug, $produit]);
        } else {
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
        $req = $this->db->prepare('SELECT estimation FROM Quantite WHERE idP = ?');
        $req->execute([$produit]);
        $estimationActuelle = $req->fetchColumn();
    
        $nouvelleEstimation = max(0, $estimationActuelle - $dim);
    
        $req = $this->db->prepare('UPDATE Quantite SET estimation = ? WHERE idP = ?');
        $req->execute([$nouvelleEstimation, $produit]);
    }

    /**
     * Méthode permettant de supprimer un produit de la liste
     * @param int $id
     */
    public function removeProduct($id) {
        $req = $this->db->prepare('DELETE FROM Produits WHERE idP = ?');
        $req->execute([$id]);
        return (bool) $req->rowCount();
    }


    public function testFromXML($nomProduit, $quantiteMl) {
        $req = $this->db->prepare('SELECT produits.nomP, quantite.estimation
        FROM quantite
        INNER JOIN produits ON quantite.idP = produits.idP
        WHERE produits.nomP = ?');
        $req->execute([$nomProduit]);
        $res = $req->fetchAll(PDO::FETCH_ASSOC);
    
        if (count($res) > 0) {
            $estimation = $res[0]['estimation'];
            $nomP       = $res[0]['nomP'];
            if ($estimation > ($quantiteMl*2)) {
                $message = "La quantité du produit $nomP est largement suffisante pour cette vente.";
            } else if ($quantiteMl >= $estimation && $quantiteMl > ($estimation * 2)) {
                $message = "La quantité du produit $nomP ne permet pas la vente.";
            } else {
                $message = "La quantité du produit $nomP est suffisante pour cette vente mais pensez à vous approvisionner.";
            }
        } else {
            $message = "Aucun produit trouvé pour le nom spécifié.";
        }
    
        return ['message' => $message];
    }


    public function updateFromXML($nomProduit, $quantiteMl) {
        $req = $this->db->prepare('SELECT idP FROM Produits WHERE nomP = ?');
        $req->execute([$nomProduit]);
        $idP = $req->fetchColumn();

        if ($idP) {
            $req = $this->db->prepare('UPDATE Quantite SET estimation = estimation - ? WHERE idP = ?');
            $req->execute([$quantiteMl, $idP]);
        } else {
            echo "Produit non trouvé : " . $nomProduit . "\n";
        }
    }

    public function getAlertesProduits() {
        $req = $this->db->prepare('SELECT nomP, estimation, alerte FROM produits JOIN quantite ON produits.idP = quantite.idP');
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function graphique($nomP) {
        $req = $this->db->prepare('SELECT valeur, date FROM graphique WHERE idP = ? ORDER BY date, valeur desc');
        $req->execute([$nomP]);
        return $req->fetchAll();
    }

    public function infoP($nomP) {
        $req = $this->db->prepare('SELECT ecart FROM graphique WHERE idP = ? AND ecart IS NOT NULL ORDER BY date');
        $req->execute([$nomP]);
        return $req->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function getTotalProductsCount() {
        $req = $this->db->prepare('SELECT COUNT(*) FROM Produits');
        $req->execute();
        return $req->fetchColumn();
    }

    public function listProductPaginated($offset = 0, $limit = 25) {
        $req = $this->db->prepare('SELECT Produits.idP, Produits.nomP, Produits.contenanceP, Produits.description, Produits.alerte, Quantite.quantite, Quantite.estimation FROM Produits LEFT JOIN Quantite ON Produits.idP = Quantite.idP ORDER BY Produits.nomP LIMIT :offset, :limit');
        $req->bindParam(':offset', $offset, PDO::PARAM_INT);
        $req->bindParam(':limit', $limit, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll();
    }

    public function getProductNameById($idP) {
        $req = $this->db->prepare('SELECT nomP FROM Produits WHERE idP = ?');
        $req->execute([$idP]);
        $row = $req->fetch();
        return $row ? $row['nomP'] : null;
    }

    public function updateAlert($nomP, $alert) {
        $req = $this->db->prepare('UPDATE Produits SET alerte = ? WHERE idP = ?');
        $req->execute([$alert, $nomP]);
        return $req->fetchAll();
    }

    public function augmenterEstimationAvecContenance($idP) {
        $Contenance = $this->db->prepare('SELECT contenanceP FROM Produits WHERE idP = ?');
        $Contenance->execute([$idP]);
        $result = $Contenance->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['contenanceP'])) {
            $contenanceP = $result['contenanceP'];

            $Update = $this->db->prepare('UPDATE Quantite SET estimation = estimation + ? WHERE idP = ?');
            $Update->execute([$contenanceP, $idP]);
        }
    }

}
