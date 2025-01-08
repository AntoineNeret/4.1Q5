<?php

namespace App\Modele;
use App\Utilitaire\Singleton_ConnexionPDO;
use DateTime;
use PDO;

class Modele_Utilisateur
{
    /**
     * @param $connexionPDO : connexion à la base de données
     * @return mixed : le tableau des étudiants ou null (something went wrong...)
     *
     */
    static function Utilisateur_Select()
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $requetePreparee = $connexionPDO->prepare('
        select utilisateur.*, categorie_utilisateur.libelle
        from `utilisateur`  inner join categorie_utilisateur on utilisateur.idCategorie_utilisateur = categorie_utilisateur.id
        order by login');
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        $tableauReponse = $requetePreparee->fetchAll(PDO::FETCH_ASSOC);
        return $tableauReponse;
    }

    /**
     * @param $connexionPDO : connexion à la base de données
     * @return mixed : le tableau des étudiants ou null (something went wrong...)
     */
    static function Utilisateur_Select_Cafe()
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $requetePreparee = $connexionPDO->prepare('
        select utilisateur.*, categorie_utilisateur.libelle
        from `utilisateur`  inner join categorie_utilisateur on utilisateur.idCategorie_utilisateur = categorie_utilisateur.id
        where utilisateur.idCategorie_utilisateur = 2 or utilisateur.idCategorie_utilisateur = 1  or utilisateur.idCategorie_utilisateur = 5
        order by login');
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        $tableauReponse = $requetePreparee->fetchAll(PDO::FETCH_ASSOC);
        return $tableauReponse;
    }

    /**
     * @param $connexionPDO
     * @param $idUtilisateur
     * @return mixed
     */
    static function Utilisateur_Select_ParId($idUtilisateur)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $requetePreparee = $connexionPDO->prepare('select * from `utilisateur` where idUtilisateur = :paramId');
        $requetePreparee->bindParam('paramId', $idUtilisateur);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        $etudiant = $requetePreparee->fetch(PDO::FETCH_ASSOC);
        return $etudiant;
    }

    /**
     * @param $connexionPDO
     * @param $idUtilisateur
     * @return mixed
     */
    static function Utilisateur_Select_ParLogin($login)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $requetePreparee = $connexionPDO->prepare('select * from `utilisateur` where login = :paramLogin');
        $requetePreparee->bindParam('paramLogin', $login);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        $utilisateur = $requetePreparee->fetch(PDO::FETCH_ASSOC);
        return $utilisateur;
    }

    /**
     * @param $connexionPDO
     * @param $login
     * @param $niveauAutorisation
     * @return mixed
     */
    static function Utilisateur_Creer($login, $motDePasse, $codeCategorie)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();

        //On vérifie avant que le login n'existe pas !
        $requetePreparee = $connexionPDO->prepare('select * from `utilisateur` where login = :paramlogin');
        $requetePreparee->bindParam('paramlogin', $login);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        $utilisateur = $requetePreparee->fetch(PDO::FETCH_ASSOC);
        if ($utilisateur != null) {
            return false;
        }

        $requetePreparee = $connexionPDO->prepare(
            'INSERT INTO `utilisateur` (`idUtilisateur`, `login`, `idCategorie_utilisateur`, `motDePasse`) 
         VALUES (NULL, :paramlogin, :paramidCategorie_utilisateur, "");');

        $requetePreparee->bindParam('paramlogin', $login);
        $requetePreparee->bindParam('paramidCategorie_utilisateur', $codeCategorie);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        if($reponse != false) {
            $idUtilisateur = $connexionPDO->lastInsertId();
            $desactiver = 0;
            self::Utilisateur_Modifier_Desactivation($idUtilisateur, $desactiver);
            self::Utilisateur_Modifier_motDePasse($idUtilisateur, $motDePasse);
            return $idUtilisateur;
        }
        return false;
    }

    /**
     * @param $connexionPDO
     * @param $idUtilisateur
     * @return mixed
     */
    static function Utilisateur_Supprimer($idUtilisateur)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();

        $requetePreparee = $connexionPDO->prepare('delete utilisateur.* from `utilisateur` where idUtilisateur = :paramId');
        $requetePreparee->bindParam('paramId', $idUtilisateur);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        return $reponse;
    }

    /**
     * @param $connexionPDO
     * @param $idUtilisateur
     * @param $login
     * @param $niveauAutorisation
     * @return mixed
     */
    static function Utilisateur_Modifier($idUtilisateur, $login, $idCodeCategorie)

    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();

        $requetePreparee = $connexionPDO->prepare(
'UPDATE `utilisateur`
SET `login`= :paramlogin, `idCategorie_utilisateur`= :paramidCategorie_utilisateur
WHERE idUtilisateur = :paramidUtilisateur');
        $requetePreparee->bindParam('paramlogin', $login);
        $requetePreparee->bindParam('paramidCategorie_utilisateur', $idCodeCategorie);
        $requetePreparee->bindParam('paramidUtilisateur', $idUtilisateur);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête


        return $reponse;
    }

// fonction pour activer ou désactiver un utilisateur
    static function Utilisateur_Modifier_Desactivation($idUtilisateur, $desactiver)

    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();

        $requetePreparee = $connexionPDO->prepare(
            'UPDATE `utilisateur` 
SET `desactiver`= :paramdesactiver
WHERE idUtilisateur = :paramidUtilisateur');
        $requetePreparee->bindParam('paramdesactiver', $desactiver);
        $requetePreparee->bindParam('paramidUtilisateur', $idUtilisateur);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        return $reponse;
    }


    /**
     * @param $connexionPDO
     * @param $idUtilisateur
     * @param $motDePasseClair
     * @return mixed
     */
    static function Utilisateur_Modifier_motDePasse($idUtilisateur, $motDePasse)

    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
         $requetePreparee = $connexionPDO->prepare(
            'UPDATE `utilisateur` 
SET motDePasse = :parammotDePasse
WHERE idUtilisateur = :paramidUtilisateur');
        $requetePreparee->bindParam('parammotDePasse', $motDePasse);
        $requetePreparee->bindParam('paramidUtilisateur', $idUtilisateur);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        return $reponse;
    }

    /**
     * @param $connexionPDO
     * @param $idUtilisateur
     * @param $motDePasseClair
     * @return mixed
     */
    static function Utilisateur_Modifier_ALL($motDePasse)

    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();

        $requetePreparee = $connexionPDO->prepare(
            'UPDATE `utilisateur` 
SET motDePasse = :parammotDePasse ');
        $requetePreparee->bindParam('parammotDePasse', $motDePasse);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
        return $reponse;
    }

    static function Utilisateur_ModifierRGPD(): void
    {
        $aAccepteRGPD = true;
        $IP = $_SERVER['REMOTE_ADDR'];
        $dateAcceptationRGPD = new DateTime();
        $connexionPDO = Singleton_ConnexionPDO::getInstance();

        $requetePreparee = $connexionPDO->prepare(
            'UPDATE `utilisateur`
SET `aAccepteRGPD`= :aAccepteRGPD, `dateAcceptationRGPD`= :dateAcceptationRGPD, `IP`= :IP
WHERE idUtilisateur = :paramidUtilisateur');
        $requetePreparee->bindParam('aAccepteRGPD', $aAccepteRGPD);
        $requetePreparee->bindParam('dateAcceptationRGPD', $dateAcceptationRGPD);
        $requetePreparee->bindParam('IP', $IP);
        $reponse = $requetePreparee->execute(); //$reponse boolean sur l'état de la requête
    }

    static function RecupererRGPD(string $login): bool
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();

        $requetePreparee = $connexionPDO->prepare(
            'SELECT aAccepteRGPD FROM `utilisateur` WHERE login=:login');
        $requetePreparee->bindParam('login', $login);
        return $requetePreparee->execute();
    }

    static function RecupererDesactiver(string $login): bool
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();

        $requetePreparee = $connexionPDO->prepare(
            'SELECT desactiver FROM `utilisateur` WHERE login=:login');
        $requetePreparee->bindParam('login', $login);
        return $requetePreparee->execute();
    }

}