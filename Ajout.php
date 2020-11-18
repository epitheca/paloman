<!--
Copyright Mathieu MONCOMBLE (contact@epitheca.fr) 2009-2020

This file is part of epitheca.

    epitheca is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License.

    epitheca is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with epitheca.  If not, see <https://www.gnu.org/licenses/>.
-->

<?php
session_start ();
require("Util.php");
require_once ("Ajout_formulaire.php");
require_once ("Ajout_tableaudonnees.php");

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

$session = ControleAcces ("Ajout.php", $_POST, session_id(), $bd);
if (SessionValide ($session, $bd))
    {
$observateur = Chercheobservateurs ($session->email, $bd, FORMAT_OBJET);
$code_obs = "$observateur->code_obs";
Entete ("epitheca.fr", "2", $code_obs, $bd);
	}

//Ouverture du bloc page
echo '<div id="bloc-page">';

//Capture du code obs de l'utilisateur
$select  = "SELECT code_obs, email FROM observateurs WHERE email='$session->email'";
$resultat = $bd->execRequete ($select);
while ($bo = $bd->objetSuivant ($resultat))
$obs1=$bo->code_obs;	

//Traduction des dates
if (isset($_POST['date']))
{
$date= $_POST['date'];
$datefr=dateservertosite ($date);
}

// Capture du mode
$mode="NOUVEAU";
$modesup="NON";
if (isset ($_GET['mode'])) 
{
$mode=$_GET['mode'];
$modesup="OUI";
}
if (isset ($_POST['mode'])) $mode= $_POST ['mode'];

//Cas 1 : C'est une suppression
if (($modesup =="OUI") && ($mode=="SUP"))
{
$numeroobs= $_GET ['numeroobs'];

//Vérification de l'existence de la donnée
$existence = VerificationExistenceDonnee ($numeroobs,$bd);

		if ($existence=="non")
		{  
			fenetre_modal ("Erreur","Cette donnée n'existe plus. <br> Vous avez peut être tenté de rafraichir la page.");
		}
		
		else
		{
		//Controle des droits de suppression
		$autorisation= Controle_droit_donnee ($numeroobs, $code_obs, $bd);
	
		if ($autorisation=="non")
		{
	?>  
			<script>
			<!--
			window.alert("<?php echo "Vous ne pouvez pas supprimer cette donnée." ?>");
			window.location.replace("index.php");
			//-->
			</script>
	<?php
		}
		else
		{
			//Extraction des données inchangées
			$select  = "SELECT * FROM donnees WHERE numero='$numeroobs'";
			$resultat = $bd->execRequete ($select);
			while ($bo = $bd->objetSuivant ($resultat))
				{
				$date=$bo->date;
				$latitude= $bo->latitude;
				$longitude= $bo->longitude;
				$vent= $bo->vent;
				$remarques=$bo->remarques;
				$meteo= $bo->meteo;
				$temp= $bo->temperature;
				$obs2= $bo->obs_2;
				$obs3= $bo->obs_3;
				$origineDonnee= $bo->origineDonnee;
				$riviere= $bo->riviere;
				$route= $bo->route;
				$corine1= $bo->corine_1;
				$corine2= $bo->corine_2;
				$corine3= $bo->corine_3;
				$corine4= $bo->corine_4;
				$choix=$bo->type_donnees;
				}
				
			//Suppression de la donnée
			$requete  = "DELETE FROM donnees WHERE numero='$numeroobs'";
			$resultat = $bd->execRequete ($requete);
		}
		
			// Réaffichage du formulaire
			FormDonnees ($date, $longitude, $latitude, NULL, NULL, NULL, NULL, NULL, $corine1, $corine2, $corine3, $corine4, $obs1, $obs2, $obs3, $origineDonnee, $choix, $vent, $meteo, $temp, $riviere, $route, $remarques, 'INSERTION', 'X', $bd); 
			//Affichage du tableau
			Tabdonnees ($date, $latitude, $longitude, $obs1, $corine1, $corine2, $corine3, $corine4, $vent, $meteo, $temp, $riviere, $route, $bd);
		}
}
	
//Cas 2 : C'est une insertion
if ($mode=="INSERTION")
{ 
$sps= $_POST['espece'];
$latitude= $_POST['latitude'];
$longitude= $_POST['longitude'];
$cond_clim= $_POST['cond_clim'];
$vent= $_POST['vent'];
$temp= $_POST['temp'];
$obs2= $_POST['obs2'];
$obs3= $_POST['obs3'];
$origineDonnee= $_POST['origineDonnee'];
$riviere= $_POST['riviere'];
$route= $_POST['route'];
$corine1= $_POST['corine1'];
$corine2= $_POST['corine2'];
$corine3= $_POST['corine3'];
$corine4= $_POST['corine4'];
$dateen= $_POST['date'];

//Modification de la valeur du second observateur en fonction du choix
			$choix=$_POST['choix'];
			if ($choix=="1") $obs2=$_POST['obs_det'];
			if ($choix=="2") $obs2=$_POST['obs_coll'];
			if ($choix=="3") $origineDonnee=$_POST['origineDonnee'];

//L'espèce n'a pas été choisie, on renvoie le formulaire pour le choix du reste
		if (!isset($_POST['valider_final']))
		{
			if ($choix=="0") $choix="CLASSIQUE";
			if ($choix=="1") $choix="DETERMINATEUR";
			if ($choix=="2") $choix="COLLECTEUR";
			if ($choix=="3") $choix="RAPPORTEUR";
	
			//C'était le bouton annuler
			if (isset($_POST['annuler_sp'])) $sps="0";
			FormDonnees ($dateen, $longitude, $latitude, $sps, "X", "X", "X", "X", $corine1, $corine2, $corine3, $corine4, $obs1, $obs2, $obs3, $origineDonnee, $choix, $vent, $cond_clim, $temp, $riviere, $route, '', 'INSERTION', '', $bd); 
			Tabdonnees ($dateen, $latitude, $longitude, $obs1, $corine1, $corine2, $corine3, $corine4, $vent, $cond_clim, $temp, $riviere, $route, $bd);
		}
		
	if (isset($_POST['valider_final']))
    {
		
			$abo= $_POST['abo'];
			$info_1= $_POST['info_1'];
			$info_2= $_POST['info_2'];
			$sexe=$_POST['sexe'];
			$remarques=$_POST['remarques'];
			$fichier_joint= $_POST['fichier_joint'];

			//Modification de la valeur du second observateur en fonction du choix
			$choix=$_POST['choix'];

			//Controle des données
			$message = ControleDonnees($_POST) ;
			
				// Erreur de saisie détectée: on affiche le message en alerte
				if (!empty($message))
					{
						fenetre_modal ("Erreur","$message");
						
						//Capture du mode d'observation
						if ($choix=="0") $choix="CLASSIQUE";
						if ($choix=="1") $choix="DETERMINATEUR";
						if ($choix=="2") $choix="COLLECTEUR";
						if ($choix=="3") $choix="RAPPORTEUR";
						
						//On annule le fichier
						$fichier="";
					
			
						// Réaffichage du formulaire avec les valeurs saisies
						FormDonnees ($date, $longitude, $latitude, "", $abo, $info_1, $info_2, $sexe, $corine1, $corine2, $corine3, $corine4, $obs1, $obs2, $obs3, $origineDonnee, $choix, $vent, $cond_clim, $temp, $riviere, $route, $remarques, 'INSERTION', $fichier, $bd);
					}
				//Pas d'erreur de saisie, on continue
				else
					{						
						// Transformation des virgules en points
						$longitude = str_replace(',', '.', $longitude);
						$latitude = str_replace(',', '.', $latitude);
														
						// Insertion des données
						INSDonnees ($date, $longitude, $latitude, $sps, $abo, $info_1, $info_2, $sexe, $corine1, $corine2, $corine3, $corine4, $obs1, $obs2, $obs3, $origineDonnee, $choix, $vent, $cond_clim, $temp, $riviere, $route, $remarques, $bd);
							
						//Capture du mode d'observation
						if ($choix=="0") $choix="CLASSIQUE";
						if ($choix=="1") $choix="DETERMINATEUR";
						if ($choix=="2") $choix="COLLECTEUR";
						if ($choix=="3") $choix="RAPPORTEUR";
						
						//Ajout de Fichier ?
						if ($fichier_joint<>"X")
						{
							$numero=Cherche_derniere_donnee ($obs1, $bd);
							INSFichier ($numero, $fichier_joint, $bd);
						}	
						//Réaffichage du formulaire
						FormDonnees ($date, $longitude, $latitude, "", $abo, $info_1, $info_2, $sexe, $corine1, $corine2, $corine3, $corine4, $obs1, $obs2, $obs3, $origineDonnee, $choix, $vent, $cond_clim, $temp, $riviere, $route, $remarques, 'INSERTION', 'X', $bd);
					}
            //Affichage du tableau des données
			Tabdonnees ($dateen, $latitude, $longitude, $obs1, $corine1, $corine2, $corine3, $corine4, $vent, $cond_clim, $temp, $riviere, $route, $bd);
		}
}

//Cas 3 : C'est un appel à mise à jour
if ($mode=="MAJ")
{ 
// Capture du numéro
if (isset ($_POST['numeroobs'])) 
{
	$numero= $_POST['numeroobs'];
}
else $numero = $_GET['numeroobs'];
//Affichage du formulaire
$mail = $session->email;
FormMajDonnees ($numero, $mail, $bd);
}

//Cas 4 : C'est une confirmation de mise à jour
if ($mode=="MAJ_CONF")
{ 
//Vérification des valeurs
//Controle des données
$message = ControleDonnees($_POST) ;
 
// Erreur de saisie détectée: on affiche le message en alerte
	if (!empty($message))
	{	
	fenetre_modal ("Erreur","$message");
	// Récupération de l'adresse mail de l'observateur
	$mail=Chercheobservateursaveccode ($code_obs, $bd, $format=FORMAT_OBJET);
	$mail=$mail->email;
	// Renvoie du formulaire
	FormMajDonnees ($_POST['numero'], $mail, $bd);
	}

//Pas d'erreur détectée
	//Envoie de la requête
	$choix =$_POST['choix'];
	if ($choix=="0") $obs2=$_POST['obs2'];
	if ($choix=="1") $obs2=$_POST['obs_det'];
	if ($choix=="2") $obs2=$_POST['obs_coll'];	
	if ($choix=="3") $obs4=$_POST['obs_rap'];
	MAJDonnees ($date, $_POST['longitude'], $_POST['latitude'], $_POST['espece'], $_POST['abo'], $_POST['info_1'], $_POST['info_2'], $_POST['sexe'], $_POST['corine1'], $_POST['corine2'], $_POST['corine3'], $_POST['corine4'], $_POST['obs1'], $_POST['obs2'], $_POST['obs3'], $_POST['origineDonnee'], $_POST['choix'], $_POST['vent'], $_POST['cond_clim'], $_POST['temp'], $_POST['riviere'], $_POST['route'], $_POST['remarques'], $_POST['numero'], $bd);
	//Réaffichage du formulaire
	//Transformation des observateurs
	if ($choix=="0") $choix="CLASSIQUE";
	if ($choix=="1") $choix="DETERMINATEUR";
	if ($choix=="2") $choix="COLLECTEUR";
	if ($choix=="3") $choix="RAPPORTEUR";	

	FormDonnees ($_POST['date'], $_POST['longitude'], $_POST['latitude'], "0", $_POST['abo'], $_POST['info_1'], $_POST['info_2'], $_POST['sexe'], $_POST['corine1'], $_POST['corine2'], $_POST['corine3'], $_POST['corine4'], $obs1, $_POST['obs2'], $_POST['obs3'], $_POST['origineDonnee'], $choix, $_POST['vent'], $_POST['cond_clim'], $_POST['temp'], $_POST['riviere'], $_POST['route'], $_POST['remarques'], 'INSERTION', "", $bd);
	Tabdonnees ($_POST['date'], $_POST['latitude'], $_POST['longitude'], $obs1, $_POST['corine1'], $_POST['corine2'], $_POST['corine3'], $_POST['corine4'], $_POST['vent'], $_POST['cond_clim'], $_POST['temp'],$_POST['riviere'], $_POST['route'], $bd);

}

//Cas 5 : C'est un premier access
if ($mode=="NOUVEAU")
{ 
//Capture de l'observateur
$obs1=$code_obs;

//Capture des préférences (observateurs)
$pref= Chercheobservateursaveccode ($code_obs, $bd, $format=FORMAT_OBJET);

?>
<script>
getLocation();
</script>
<?php
FormDonnees (NULL, NULL, NULL, NULL, NULL, 'X', 'X', 'X', '0', '0', '0', '0', $obs1, NULL,NULL,NULL,'CLASSIQUE','X', 'X', NULL, 'X', 'X', NULL, 'INSERTION', 'X', $bd);
}

//Cas 6 : C'est un complément de fiche
if ($mode=="completer")
{
//Capture de la donnees
$numero=$_GET['numero'];
$code_obs= $observateur->code_obs;

//vérification des droits
$controle= Controle_droit_donnee ($numero, $code_obs, $bd);
if ($controle=="non") fenetre_modal ("Problème de droits", "Vous n'êtes pas auteur de cette donnée.");
else 
{
	$requete ="select * FROM donnees WHERE numero =$numero";
$resultat = $bd->execRequete ($requete); 
 while ($bo = $bd->objetSuivant ($resultat))
 {
FormDonnees ($bo->date, $bo->longitude,$bo->latitude, '', '', '', '', '', $bo->corine_1, $bo->corine_2, $bo->corine_3, $bo->corine_4,$bo->obs_1, $bo->obs_2, $bo->obs_3, $bo->origineDonnee, $bo->type_donnees, $bo->vent, $bo->meteo, $bo->temperature, $bo->riviere, $bo->route, $bo->remarques, 'INSERTION', 'X', $bd);
Tabdonnees ($bo->date, $bo->latitude, $bo->longitude, $bo->obs_1,  $bo->corine_1, $bo->corine_2, $bo->corine_3, $bo->corine_4, $bo->vent, $bo->meteo, $bo->temperature,$bo->riviere, $bo->route, $bd);
}
}}
//Fermeture du bloc page
echo '</div>';
// Affichage du pied de page
PiedDePage($session, $code_obs, $bd);
?>
