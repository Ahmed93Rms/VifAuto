<!DOCTYPE html>
<html lang="fr">   

<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <meta name="description" content="Outils qui permet gerer son stock de peinture">
        <title>Vif Auto</title>
        <link rel="stylesheet" href="Content/css/style.css"/>
        <link rel="icon" href="Content/image/vifauto.png" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
        <header>
                <nav class="nb">
                        <div><img src="Content/image/vifauto_2.png" class="logoHead" alt="vifautologohead"></div>
                        <div class="m_nb">
                                <a href="#import" class="nb_a"><span class="material-symbols-outlined">download</span>Importer</a>
                                <a href="#list" class="nb_a"><span class="material-symbols-outlined">menu</span>Produits</a>
                                <a href="#graph" class="nb_a"><span class="material-symbols-outlined">monitoring</span>Graphique</a>
                        </div>
                </nav>
        </header>
        <main>
                <h1 class="center">Modifier votre liste de produit</h1>
                <p class="grey center"><u>Pour les valeurs decimal, il faut mettre un point et pas une virgule. (Ex: 3.5)</u></p>
                <section class="form" id="import">
                        <div class="block blue">
                                <h1 style="color: #0085FF;">Ajouter un produit</h1>
                                <form method="post" action="?controller=home&action=home">
                                        <div>Reference du produit : <input type="text" name="ref" required></div>
                                        <div>Contenance du produit(mL) : <input type="text" name="litre" required></div>
                                        <div class="textDesc">Description : <textarea name="desc" placeholder="facultatif"></textarea></div>
                                        <button type="submit" class="bt blue2">Ajouter</button>
                                        <?php echo $errorP; ?>
                                </form>
                        </div>
                        <div class="block green">
                                <h1 style="color: #2C964D;">Augmenter la quantité d'un produit</h1>
                                <form method="post" action="?controller=home&action=home">
                                        <div>Produit : 
                                        <select id="productSelect" name="produit">
                                                <?php foreach ($selectPr as $p): ?>
                                                <option value="<?php echo $p['idP']; ?>"><?php echo $p['nomP']; ?></option>
                                                <?php endforeach; ?>
                                        </select> 
                                        </div>
                                        <div>Quantité(mL) : <input type="text" name="aug" required></div>
                                        <button type="submit" class="bt green2">Augmenter</button>
                                </form>
                        </div>
                        <div class="block red">
                                <h1 style="color: #FF0101;">Diminuer la quantité d'un produit</h1>
                                <form method="post" action="?controller=home&action=home">
                                        <div>Produit : 
                                        <select id="productSelect" name="produit">
                                                <?php foreach ($selectPr as $p): ?>
                                                <option value="<?php echo $p['idP']; ?>"><?php echo $p['nomP']; ?></option>
                                                <?php endforeach; ?>
                                        </select> </div>
                                        <div>Quantité(mL) : <input type="text" name="dim" required></div>
                                        <button type="submit" class="bt red2">Diminuer</button>
                                </form>
                        </div>
                </section>
                <section id="XML" class ="import">
                        <form action="?controller=home&action=home" method="post" enctype="multipart/form-data">
                                <div>Importer un fichier XML : <input type="file" name="fichierXML"/></div>
                                <button type="submit" class="bt black">Importer</button>
                        </form>
                </section>
                <section class="list" id="list">
                        <h1 class="center">Liste des produits</h1>
                        <!-- Affichage des alertes -->
                        <?php   foreach ($nomPr as $produit) {
                                        if ($produit['estimation'] == 0) {
                                                echo '<div class="alert">
                                                <span class="material-symbols-outlined">
                                                warning
                                                </span>
                                                Le produit '.$produit['nomP'].' est épuisé.</div>';
                                        } elseif ($produit['estimation'] < 150) {
                                                echo '<div class="alert">
                                                <span class="material-symbols-outlined">
                                                warning
                                                </span>
                                                Le produit '.$produit['nomP'].' risque d\'être épuiser.</div>';
                                        }
                                }
                        ?>
                        
                        <form action="?controller=home&action=home" method="post" style="text-align: center;">
                                <div>Retrouver un produit : 
                                <input type="text" name="find" placeholder="Entrez une valeur" />
                                </div>
                                <div>Estimation <= : <input type="number" name="estimationF" placeholder="Entrez une valeur" /></div>
                                <div><a href="index.php">Réinitialiser la recherche</a></div>
                                <button type="submit" class="bt black">Filtrer</button>
                        </form>
                        <table>
                                <tr>
                                        <th>Produit</th>
                                        <th>Contenance par pot(mL)</th>
                                        <th>Quantité</th>
                                        <th>Estimation(mL)</th>
                                        <th>Description</th>
                                        <th class="sansBordure"></th>
                                </tr>
                                <!-- Recuperation des infos dans la bdd pour la liste -->
                                <?php foreach ($nomPr as $item) : ?>
                                <tr>
                                        <td><?php echo $item['nomP']; ?></td>
                                        <td><?php echo $item['contenanceP']; ?></td>
                                        <td><?php echo $item['quantite']; ?></td>
                                        <!-- Couleur dans la colonne estimation -->
                                        <?php if ($item['estimation'] <= 150) {
                                                echo '<td style="background-color:#e04f4f;">'.$item['estimation'].'</td>';
                                        } elseif ($item['estimation'] <= 500){
                                                echo '<td style="background-color:#e0b04f;">'.$item['estimation'].'</td>';
                                        }
                                        else {
                                                echo '<td style="background-color:#4fe06a;">'.$item['estimation'].'</td>';
                                        }
                                        ?>

                                        <td><?php echo $item['description']; ?></td>
                                        <td class="sansBordure">
                                                <a href="?controller=set&action=remove&idP=<?php echo $item['idP'] ?>">
                                                        <img class="icone" src="Content/image/remove-icon.png" alt="supprimer"/>
                                                </a>
                                        </td>
                                </tr>
                                <?php endforeach ?> 
                                
                        </table>                        
                </section>
                <section class="graph" id="graph">
                        <h1 class="center">Evolution d'un produits</h1>
                        <form action="" method="post" class="center">
                                <h3>Sélectionner un produit</h3>
                                <div>
                                        <select id="productSelect" name="produit">
                                        <?php foreach ($selectPr as $p): ?>
                                        <option value="<?php echo $p['idP']; ?>"><?php echo $p['nomP']; ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                </div>
                                <button type="submit" class="bt black">Afficher</button>
                        </form>
                        
                </section>
        </main>
        <footer>
                <div><img src="Content/image/vifauto_2.png" class="logoFoot" alt="vifautologofoot"></div>
        </footer>
</body>
</html>

<!--import.XML, graphique -->