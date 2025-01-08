<?php

use App\Modele\Modele_Entreprise;
use App\Modele\Modele_Salarie;
use App\Modele\Modele_Utilisateur;
use App\Vue\Vue_Connexion_Formulaire_client;
use App\Vue\Vue_Mail_Confirme;
use App\Vue\Vue_Mail_ReinitMdp;
use App\Vue\Vue_Menu_Administration;
use App\Vue\Vue_Menu_Commercial;
use App\Vue\Vue_Structure_BasDePage;
use App\Vue\Vue_Structure_Entete;

use PHPMailer\PHPMailer\PHPMailer;
//Ce contrôleur gère le formulaire de connexion pour les visiteurs

$Vue->setEntete(new Vue_Structure_Entete());

switch ($action) {
    case "Modifier_RGPD":
        
        break;
    case "reinitmdpconfirmtoken":
        $modele_jeton = new \App\Modele\Modele_Jeton();
        $modele_utilisateur = new \App\Modele\Modele_Utilisateur();
        $token = \App\Fonctions\jeton();
        $_SESSION["jeton"]=$token;
        $idUtilisateur = $modele_utilisateur->Utilisateur_Select_ParLogin($_POST["mailJeton"])["idUtilisateur"];
        $modele_jeton->Jeton_Creer($idUtilisateur,75,$token);
        \App\Fonctions\envoyerToken($token);
        $Vue->addToCorps(new Vue_Mail_Confirme());
        break;
    case "choixmdptoken":
        $modele_jeton = new \App\Modele\Modele_Jeton();
        $jeton = $modele_jeton->Jeton_Fetch($_SESSION["jeton"]);
        Modele_Utilisateur::Utilisateur_Modifier_motDePasse($jeton[0]["idUtilisateur"],$_POST["mdp1"]);
        $Vue->addToCorps(new Vue_Connexion_Formulaire_client());
        break;
    case "reinitmdpconfirm":
          if (isset($_POST["email"]) && $_POST["email"] != "") {
              $nouveauMDP = \App\Fonctions\motDePassePerdu(30);
              \App\Fonctions\envoyerMail($nouveauMDP);
              Modele_Utilisateur::Utilisateur_Modifier_motDePasse(Modele_Utilisateur::Utilisateur_Select_ParLogin($_POST["email"])["idUtilisateur"],$nouveauMDP);
              $_SESSION["reinitmdp"] = true;
          }
        $Vue->addToCorps(new Vue_Mail_Confirme());
        break;

    case "reinitmdp":
        $Vue->addToCorps(new Vue_Mail_ReinitMdp());
        break;

    case "token":
        $modele_jeton = new \App\Modele\Modele_Jeton();
        $modele_utilisateur = new \App\Modele\Modele_Utilisateur();
        $jeton = $modele_jeton->Jeton_Fetch($_SESSION["jeton"]);
        $Vue->addToCorps(new \App\Vue\Vue_Mail_ChoisirNouveauMdp($_SESSION["jeton"]));
        break;
    case "Se connecter":
        $modele_utilisateur = new \App\Modele\Modele_Utilisateur();
        if (isset($_REQUEST["compte"]) && isset($_REQUEST["password"]) && $modele_utilisateur->RecupererRGPD($_REQUEST["compte"]) && $modele_utilisateur->RecupererDesactiver($_REQUEST["compte"])) {
            //Si tous les paramètres du formulaire sont bons
            $utilisateur = Modele_Utilisateur::Utilisateur_Select_ParLogin($_REQUEST["compte"]);
            $aAccepteRGPD = false;
            if ($utilisateur != null) {
                //error_log("utilisateur : " . $utilisateur["idUtilisateur"]);
                if ($utilisateur["desactiver"] == 0) {
                    if ($_REQUEST["password"] == $utilisateur["motDePasse"]) {
                        if ( isset($_SESSION["reinitmdp"]) && $_SESSION["reinitmdp"]) {
                            header("Location:index.php?case=Gerer_monCompte&action=changerMDP");
                        }
                        $_SESSION["idUtilisateur"] = $utilisateur["idUtilisateur"];
                        //error_log("idUtilisateur : " . $_SESSION["idUtilisateur"]);
                        $_SESSION["idCategorie_utilisateur"] = $utilisateur["idCategorie_utilisateur"];
                        //error_log("idCategorie_utilisateur : " . $_SESSION["idCategorie_utilisateur"]);
                        switch ($utilisateur["idCategorie_utilisateur"]) {
                            case 1:
                                $_SESSION["typeConnexionBack"] = "administrateurLogiciel"; //Champ inutile, mais bien pour voir ce qu'il se passe avec des étudiants !
                                $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                                break;
                            case 2:
                                if ($aAccepteRGPD) {
                                    $_SESSION["typeConnexionBack"] = "rédacteur";
                                    $Vue->setMenu(new Vue_Menu_Administration($_SESSION["typeConnexionBack"]));
                                    break;
                                }
                                    include __DIR__."/Controleur_AccepterRGPD.php";
                                    break;
                            case 3:
                                $_SESSION["typeConnexionBack"] = "entrepriseCliente";
                                //error_log("idUtilisateur : " . $_SESSION["idUtilisateur"]);
                                $_SESSION["idEntreprise"] = Modele_Entreprise::Entreprise_Select_Par_IdUtilisateur($_SESSION["idUtilisateur"])["idEntreprise"];
                                include "./Controleur/Controleur_Gerer_Entreprise.php";
                                break;
                            case 4:
                                if ($aAccepteRGPD) {
                                    $_SESSION["typeConnexionBack"] = "salarieEntrepriseCliente";
                                    $_SESSION["idSalarie"] = $utilisateur["idUtilisateur"];
                                    $_SESSION["idEntreprise"] = Modele_Salarie::Salarie_Select_byId($_SESSION["idUtilisateur"])["idEntreprise"];
                                    include "./Controleur/Controleur_Catalogue_client.php";
                                    break;
                                }
                                case 5:
                                if ($aAccepteRGPD) {
                                    $_SESSION["typeConnexionBack"] = "commercialCafe";
                                    $_SESSION["idSalarie"] = $utilisateur["idUtilisateur"];
                                    $_SESSION["idEntreprise"] = Modele_Salarie::Salarie_Select_byId($_SESSION["idUtilisateur"])["idEntreprise"];
                                    include "./Controleur/Controleur_Catalogue_client.php";
                                    break;
                                }
                                    include __DIR__."/Controleur_AccepterRGPD.php";
                        }
                    } else {//mot de passe pas bon
                        $msgError = "Mot de passe erroné";

                        $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));

                    }
                } else {
                    $msgError = "Compte désactivé";

                    $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));

                }
            } else {
                $msgError = "Identification invalide";

                $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));
            }
        } else {
            $msgError = "Identification incomplete";

            $Vue->addToCorps(new Vue_Connexion_Formulaire_client($msgError));
        }
    break;
    default:

        $Vue->addToCorps(new Vue_Connexion_Formulaire_client());

        break;
}


$Vue->setBasDePage(new Vue_Structure_BasDePage());