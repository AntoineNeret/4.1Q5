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
if (isset($_POST["AccepterRGPD"])) {
    $action = $_POST["AccepterRGPD"];
}else{
    $action = "off";
}

switch ($action) {
    case "on":
        Modele_Utilisateur::Utilisateur_ModifierRGPD();
        break;
    case "off":
        session_destroy();
        unset($_SESSION);
        $Vue->setEntete(new Vue_Structure_Entete());
        $Vue->addToCorps(new Vue_Connexion_Formulaire_client());
        break;
    default:
        $Vue->setEntete(new Vue_Structure_Entete());
        $Vue->addToCorps(new \App\Vue\Vue_Accepter_RGPD());
        break;
}