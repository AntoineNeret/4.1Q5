<?php
/**
 * @throws \Random\RandomException
 */
function passgen1($nbChar)
{
    $chaine = "ABCDEFGHIJKLMONOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789&é\"'(-è_çà)=$^*ù!:;,~#{[|`\^@]}¤€";
    srand((double)microtime() * random_int(1,1000000) * rand(1,1000000));
    $pass = '';
    for ($i = 0; $i < $nbChar; $i++) {
        $pass .= $chaine[rand() % strlen($chaine)];
    }
    return $pass;
}

    //Création de la séquence aléatoire à la base du mot de passe
    $octetsAleatoires = openssl_random_pseudo_bytes (12) ;
//Transformation de la séquence binaire en caractères alpha
    $motDePasse = sodium_bin2base64($octetsAleatoires, SODIUM_BASE64_VARIANT_ORIGINAL);
    echo $motDePasse;


    echo passgen1(10) . "\n";

