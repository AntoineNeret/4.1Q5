<?php
namespace App\Vue;
use App\Utilitaire\Vue_Composant;

class Vue_Mail_ReinitMdp extends Vue_Composant
{
    public function __construct()
    {
    }

    function donneTexte(): string
    {
        $str= "  
  <form action='index.php' method='post' style='    width: 50%;    display: block;    margin: auto;'>
             ".genereChampHiddenCSRF()."
                <h1>Mail à renouveler</h1>
                
                <label><b>Compte</b></label>
                <input type='email' placeholder='mail du compte à renouveler le mdp' name='email'>
                
                <button type='submit' id='submit' name='action' 
                            value='reinitmdpconfirm'>
                            Renouveler mdp
                </button>
                
                
                                <h1 style='margin-top: 25%'>Mail du compte à renouveler avec jeton</h1>
                
                <label><b>Compte</b></label>
                <input type='email' placeholder='mail du compte à renouveler le mdp' name='mailJeton'>
                
                <button type='submit' id='submit' name='action' 
                            value='reinitmdpconfirmtoken'>
                            Renouveler mdp
                </button>";
        $str .=  "
 </form>
    ";
        return $str;
    }
}