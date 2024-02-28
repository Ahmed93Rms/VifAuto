<?php

class Controller_home extends Controller{

    //Fonction qui calcule la medianne
    private function calculMedian($values) {
        sort($values);
        $count = count($values);
        $moy = floor(($count-1)/2);
        if ($count % 2) { // Impair
            $median = $values[$moy];
        } else { // Pair
            $median = ($values[$moy]+$values[$moy+1]) / 2;
        }
        return $median;
    }

    //Fonction qui calcule la moyenne
    private function calculMoyenne($values) {
        if (count($values) === 0) return 0;
        $sum = array_sum($values);
        $moyenne = $sum / count($values);
        return $moyenne;
    }

    public function action_home()
    {
        //Initialisation des variables
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
                echo '<script>alert("Ce produit existe déja")</script>';
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

        //Importer le fichier XML et convertir les information dans la bdd
        if (!empty($_FILES["fichierXML"])) {
            $tmpPath = $_FILES['fichierXML']['tmp_name'];
            $xmldata = simplexml_load_file($tmpPath) or die("Failed to load");
            foreach($xmldata->Session->Formulation->Mix as $mix) {
                $mixName = preg_replace('/\D/', '', $mix->Mix_name);
                $Mixml   = $mix->Mix_ml;  
                $m->updateFromXML($mixName, $Mixml);  
            }
            header('Location: index.php');
        }

        //Bouton ajouter
        if (isset($_POST['augmenterQuantite']) && isset($_POST['idP'])) {
            $idP = $_POST['idP'];
            $m->augmenterEstimationAvecContenance($idP);
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $_SERVER['PHP_SELF'] . '#list";';
            echo '</script>';
        }

        //Supprimer un produit
        if (isset($_GET["idP"]) and preg_match("/^[1-9]\d*$/", $_GET["idP"])) {
            $id = $_GET["idP"];
            $m->removeProduct($id);
            header('Location: index.php');
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 20;
        $offset = ($page - 1) * $itemsPerPage;

        //Filtre
        if(isset($_POST["find"]) || isset($_POST["estimationF"])) {
            // Récupérer la valeur de produitFiltre du formulaire
            $produitFiltre = htmlspecialchars($_POST["find"]);
            $estimation    = htmlspecialchars($_POST["estimationF"]);
            // Filtre combiné: si les deux filtres sont utilisés
            if(!empty($produitFiltre) && !empty($estimation)) {
                $nomPr = $m->filtrerParIdEtEstimation($produitFiltre, $estimation);
                $totalProducts = count($nomPr);
            }
            // Filtre par produit uniquement
            else if(!empty($produitFiltre)) {
                $nomPr = $m->filtrerParId($produitFiltre);
                $totalProducts = count($nomPr);
            }
            // Filtre par estimation uniquement
            else if(!empty($estimation)) {
                $nomPr = $m->filtrerParEstimation($estimation);
                $totalProducts = count($nomPr);
            }
            // Aucun filtre spécifique n'est sélectionné, récupérer tous les produits
            else {
                $nomPr = $m->listProduct();
                $totalProducts = count($nomPr);
            }
        } else {
            // Si aucun formulaire n'a été soumis, récupérer tous les produits
            $nomPr = $m->listProductPaginated($offset, $itemsPerPage);
            $totalProducts = $m->getTotalProductsCount();
        }
        $totalPages = ceil($totalProducts / $itemsPerPage);

        //Affichage du graphique 
        $nom = 1;
        if (isset($_POST["produitG"])) {
            $nom         = htmlspecialchars($_POST["produitG"]);
            $values      = $m->infoP($nom);
            $median      = round($this->calculMedian($values), 1);
            $moyenne     = round($this->calculMoyenne($values), 1);
            $result      = $m->graphique($nom);
            $donneesJson = json_encode($result);
            $nomProduit  = $m->getProductNameById($nom);
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $_SERVER['PHP_SELF'] . '#graph";';
            echo '</script>';
        } else {
            $result = [];
            $donneesJson = json_encode($result);
            $median      = 0;
            $moyenne     = 0;
            $nomProduit = "";      
        }

        if(isset($_POST["alert"])) {
            $nom   = htmlspecialchars($_POST["nomInfo"]);
            $alert = htmlspecialchars($_POST["alert"]);
            $m->updateAlert($nom, $alert);
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $_SERVER['PHP_SELF'] . '#graph";';
            echo '</script>';
        }



        /**
        * Affiche la vue
        * @param 'home' nom de la vue
        * @param array $data tableau contenant les données à passer à la vue
        */
        $data = ['nomProduit' => $nomProduit, 'totalPages' => $totalPages, 'currentPage' => $page,
        'nomPr'=>$nomPr, 'selectPr'=>$selectPr, 'alert'=>$alert, 'donneesJson'=>$donneesJson,
        'median'=>$median, 'moyenne'=>$moyenne];
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
