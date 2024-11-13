<?php
namespace App\Fonctions;
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