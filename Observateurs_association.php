<?php
session_start();
require("Util.php");

//Récupération de l'URL
$monUrl = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);
$session = ControleAcces ($monUrl, $_POST, session_id(), $bd);
if (SessionValide ($session, $bd))
{
// Production de l'entête
$observateur = Chercheobservateurs ($session->email, $bd, FORMAT_OBJET);
$code_obs = "$observateur->code_obs";
Entete ("epitheca.fr", "6", $code_obs, $bd);
}

$cle=$_GET['cle'];

if ($_GET['accept']=="yes")
{
	
//Recherche de l'existence de la clé dans la table
$select  = "SELECT * FROM observateurs_demande WHERE id_demande='$cle'";

			$resultat = $bd->execRequete ($select);
			while ($bo = $bd->objetSuivant ($resultat))
				{
				//Vérification du temps
				echo $bo->timestamp;
				$unesemaine = strtotime('+1 week');
				$calcul= date ("Y-m-d H:i:sa", $unesemaine);
				echo $calcul;
}}

// Affichage du pied de page
PiedDePage($session, $code_obs, $bd);
  
?>
