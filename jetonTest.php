<?php

require_once './vendor/autoload.php';

$modele = new \App\Modele\Modele_Jeton();

$token = \App\Fonctions\jeton();

$modele->Jeton_Creer(14,75,$token);

$modele->Jeton_Modifier(2);


print($modele->Jeton_Fetch($token)[0]["id"])."\n";
print($modele->Jeton_Fetch($token)[0]["valeur"])."\n";
print($modele->Jeton_Fetch($token)[0]["codeAction"])."\n";
print($modele->Jeton_Fetch($token)[0]["idUtilisateur"])."\n";
print($modele->Jeton_Fetch($token)[0]["dateFin"])."\n";

