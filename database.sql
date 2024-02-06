CREATE TABLE Produits (
    `idP` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `nomP` varchar(100),
    `contenanceP` float,
    `description` varchar(255),
    PRIMARY KEY (`idP`),
    UNIQUE KEY `idP` (`idP`)
);

CREATE TABLE Quantite (
    `idP` int NOT NULL,
    `quantite` varchar(100) DEFAULT '0',
    `estimation` float DEFAULT 'NULL',
    PRIMARY KEY (`idP`)
);

/* TRIGGER pour la colonne quantité*/

DELIMITER $$

CREATE TRIGGER ajuster_quantite_avant_insert
BEFORE INSERT ON Quantite
FOR EACH ROW
BEGIN
    DECLARE contenance FLOAT;

    -- Récupérer la contenanceP du produit correspondant
    SELECT contenanceP INTO contenance FROM Produits WHERE idP = NEW.idP;

    -- Calculer la nouvelle quantité et ajuster avant l'insertion
    IF contenance > 0 THEN -- S'assurer que contenanceP n'est pas 0 pour éviter division par 0
        SET NEW.quantite = CEIL(NEW.estimation / contenance);
    END IF;
END$$

CREATE TRIGGER ajuster_quantite_avant_update
BEFORE UPDATE ON Quantite
FOR EACH ROW
BEGIN
    DECLARE contenance FLOAT;

    -- Récupérer la contenanceP du produit correspondant
    SELECT contenanceP INTO contenance FROM Produits WHERE idP = NEW.idP;

    -- Calculer la nouvelle quantité et ajuster avant la mise à jour
    IF contenance > 0 THEN
        SET NEW.quantite = CEIL(NEW.estimation / contenance);
    END IF;
END$$

/* TRIGGER pour la colonne estimation*/

CREATE TRIGGER estimation_non_negative
BEFORE UPDATE ON Quantite
FOR EACH ROW
BEGIN
    -- S'assurer que la nouvelle estimation ne soit pas négative
    IF NEW.estimation < 0 THEN
        SET NEW.estimation = 0;
    END IF;
END$$

DELIMITER ;