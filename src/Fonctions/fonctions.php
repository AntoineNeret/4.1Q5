<?php
namespace App\Fonctions;
use PHPMailer\PHPMailer\PHPMailer;
    function Redirect_Self_URL():void{
        unset($_REQUEST);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

function GenereMDP($nbChar) :string{

    return "secret";
}

function CalculComplexiteMdp($mdp) :int{
    $base = 0;
    if (str_contains($mdp, 'A-Z')){
        $base+=26;
    }
    if (str_contains($mdp, '0-9')){
        $base+=10;
    }
    if (str_contains($mdp, 'a-z')){
        $base+=26;
    }
    else{
        $base+=23;
    }

return round(strlen($mdp)*log($base,2));
}

    function motDePassePerdu($nbChar)
    {
        $chaine = "ABCDEFGHIJKLMONOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789&é\"'(-è_çà)=$^*ù!:;,~#{[|`\^@]}¤€";
        srand((double)microtime() * random_int(1,1000000) * rand(1,1000000));
        $pass = '';
        for ($i = 0; $i < $nbChar; $i++) {
            $pass .= $chaine[rand() % strlen($chaine)];
        }
        return $pass;
    }

    function envoyerMail($pass)
    {

        //Obligatoire pour avoir l’objet phpmailer qui marche
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = '127.0.0.1';
        $mail->Port = 1025; //Port non crypté
        $mail->SMTPAuth = false; //Pas d’authentification
        $mail->SMTPAutoTLS = false; //Pas de certificat TLS
        $mail->setFrom('café@café.fr', 'café');
        $mail->addAddress($_POST["email"], 'Mon client');
        if ($mail->addReplyTo($_POST["email"], 'café')) {
            $mail->Subject = 'Objet : Réinitialisation de mot de passe !';
            $mail->isHTML(false);
            $mail->Body = "Votre mot de passe à usage unique est le suivant : ".$pass;

            if (!$mail->send()) {
                $msg = 'Désolé, quelque chose a mal tourné. Veuillez réessayer plus tard.';
            } else {
                $msg = 'Message envoyé ! Merci de nous avoir contactés.';
            }
        } else {
            $msg = 'Il doit manquer qqc !';
        }
        echo $msg;
    }