<?php

namespace App\Modele;

use App\Utilitaire\Singleton_ConnexionPDO;
use PDO;
use function App\Fonctions\jeton;

class Modele_Jeton
{
    function Jeton_Creer(int $idUtilisateur, int $codeAction, string $token)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $dateFin = date("Y-m-d", strtotime("+1 hour"));
        $requetePreparee = $connexionPDO->prepare("INSERT INTO `token` (`valeur`,`codeAction`,`idUtilisateur`,`dateFin`) VALUES (:paramValeur,:paramCodeAction,:paramIdUtilisateur,NOW())");
        $requetePreparee->bindValue(':paramValeur',$token);
        $requetePreparee->bindValue(':paramCodeAction',$codeAction);
        $requetePreparee->bindValue(':paramIdUtilisateur',$idUtilisateur);
        $requetePreparee->execute();
    }

    function Jeton_Modifier(int $id)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $requetePreparee = $connexionPDO->prepare("UPDATE token SET codeAction=:paramCodeAction WHERE id=:id");
        $requetePreparee->bindValue(':paramCodeAction',0);
        $requetePreparee->bindValue(':id',$id);
        $requetePreparee->execute();
    }

    function Jeton_Fetch(string $token)
    {
        $connexionPDO = Singleton_ConnexionPDO::getInstance();
        $requetePreparee = $connexionPDO->prepare("SELECT * FROM token WHERE valeur=:token");
        $requetePreparee->bindValue(':token',$token);
        $requetePreparee->execute();
        return $requetePreparee->fetchAll(PDO::FETCH_ASSOC);
    }

}