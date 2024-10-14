<?php
namespace App\Vue;
use App\Utilitaire\Vue_Composant;

class Vue_Menu_Redaction extends Vue_Composant
{
    public function __construct( )
    {           }
    function donneTexte(): string
    {

        return "
             <nav id='menu'>
              <ul id='menu-closed'> 
                <li><a href='?case=Gerer_CommandeClient'>Commandes</a></li>
                <li><a href='?case=Gerer_Entreprise'>Entreprises</a></li>

            
                <li><a href='?case=Gerer_monCompte'>Mon compte</a></li> 
               </ul>
            </nav> 
";

    }
}
