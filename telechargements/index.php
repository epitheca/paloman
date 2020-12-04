<?php
ini_set('display_errors',1);
session_start();

require("Util.php");
require_once ("index_fonction.php");
require_once ("index_pasdedonnee.php");

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

// Contrôle de la session
$session = ControleAcces ("index.php", $_POST, session_id(), $bd);
if (SessionValide ($session, $bd))
{
$observateur = Chercheobservateurs ($session->email, $bd, FORMAT_OBJET);
$code_obs = "$observateur->code_obs";
Entete ("epitheca.fr", "1", $code_obs, $bd);
}

?>
<div id="bloc-page">
    <div class="bloc-100pc">
    <?php
            
//Calcul du nombre total
$nbrtotal = Calcdontotal ($code_obs, '', $bd);

	if ($nbrtotal=="0")
    {
        pasdedonnee ($code_obs, $bd);
    }
    else
    {
		?>
    <div class="bloc-50pc-gauche">
	<?php 
	mesdonnees($code_obs, $bd);
	?>
	</div>
	
	<div class="bloc-50pc-droit">	
	<?php
	include ("index_structure.php");
	include ("index_fluxdonnees.php");
	include ("acces_rapide.php");
	?>
	</div>
	
    </div>
</div>
	

<?php
    }
PiedDePage($session, $code_obs, $bd);
?>
