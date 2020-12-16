<?php
session_start();
require("Util.php");
require ("ObservateursFormulaire.php");

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

$session = ControleAcces ("ObservateursMAJ.php", $_POST, session_id(), $bd);
if (SessionValide ($session, $bd))
{
// Production de l'entête
$observateur = Chercheobservateurs ($session->email, $bd, FORMAT_OBJET);
$code_obs = "$observateur->code_obs";
Entete ("epitheca.fr", "", $code_obs, $bd);
}

//Inclusion du formulaire de controle
include ("Observateurs_controles.php");

echo '<div id="bloc-page">';

Formobservateurs ("MAJperso", $code_obs, $bd);  

echo '</div>';

// Pied de page
PiedDePage($session, $code_obs, $bd);
?>