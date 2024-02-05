<?php

class Controller_home extends Controller{

    public function action_home()
    {
        //Initialisation des variables
        $errorP  ="";
        $alert    = ""; 

        $m = Model::getModel();
        //Récupère le nom des produits
        $selectPr = $m->getProduit();
        //Récupère toutes les infos d'un produit
        $nomPr = $m->listProduct();


        //Ajouter un produit et vérifier son existance
        if (!empty($_POST["ref"]) && !empty($_POST["litre"])) {
            $name        = htmlspecialchars($_POST["ref"]);
            $contenance  = htmlspecialchars($_POST["litre"]);
            $description = htmlspecialchars($_POST["desc"]); 

            if ($m->productExists($name)) {
                $errorP = '<p style="color:red; text-align:center;">Ce produit existe deja.</p>';
            } else {
                $m->addProduct($name, $contenance, $description);
                header('Location: index.php');
            }

        }

        //Augmenter la quantité d'un produit
        if (!empty($_POST["produit"]) && !empty($_POST["aug"])) {
            $produit = htmlspecialchars($_POST["produit"]);
            $aug     = htmlspecialchars($_POST["aug"]);

            $m->addQuantity($produit, $aug);
            header('Location: index.php');
        }

        //Diminuer la quantité d'un produit
        if (!empty($_POST["produit"]) && !empty($_POST["dim"])) {
            $produit = htmlspecialchars($_POST["produit"]);
            $dim     = htmlspecialchars($_POST["dim"]);

            $m->remQuantity($produit, $dim);
            header('Location: index.php');
        }

        //Supprimer un produit
        if (isset($_GET["idP"]) and preg_match("/^[1-9]\d*$/", $_GET["idP"])) {
            $id = $_GET["idP"];
            $m->removeProduct($id);
            header('Location: index.php');
        }

        //Filtre

        if(isset($_POST["find"]) || isset($_POST["estimationF"])) {
            // Récupérer la valeur de produitFiltre du formulaire
            $produitFiltre = htmlspecialchars($_POST["find"]);
            $estimation    = htmlspecialchars($_POST["estimationF"]);

            // Filtre combiné: si les deux filtres sont utilisés
            if(!empty($produitFiltre) && !empty($estimation)) {
                $nomPr = $m->filtrerParIdEtEstimation($produitFiltre, $estimation);
            }
            // Filtre par produit uniquement
            else if(!empty($produitFiltre)) {
                $nomPr = $m->filtrerParId($produitFiltre);
            }
            // Filtre par estimation uniquement
            else if(!empty($estimation)) {
                $nomPr = $m->filtrerParEstimation($estimation);
            }
            // Aucun filtre spécifique n'est sélectionné, récupérer tous les produits
            else {
                $nomPr = $m->listProduct();
            }
        } else {
            // Si aucun formulaire n'a été soumis, récupérer tous les produits
            $nomPr = $m->listProduct();
        }

        $alert    = ""; // Initialiser l'alerte comme fausse

        // Vérifier si un produit a une estimation inférieure à 150mL
        foreach ($nomPr as $produit) {
            if ($produit['estimation'] < 150) {
                $alert = '<p style="color:red; text-align:center;">Le produit '.$produit['nomP'].' risque d\'être épuiser.</p>';
            }
        }

        /**
        * Affiche la vue
        * @param 'home' nom de la vue
        * @param array $data tableau contenant les données à passer à la vue
        */
        $data = ['errorP'=>$errorP, 'nomPr'=>$nomPr, 'selectPr'=>$selectPr, 'alert'=>$alert];
        $this->render('home', $data);
    }

    /**
     * Affiche l'action home par defaut
     */
    public function action_default()
    {
        $this->action_home();
    }
}
