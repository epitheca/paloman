<!--
Copyright Mathieu MONCOMBLE (mathieu.moncomble@epitheca.fr) 2009-2020

This file is part of epitheca.

    epitheca is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    epitheca is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with epitheca.  If not, see <https://www.gnu.org/licenses/>.
-->

<?php
// Fonctions définissant le design du site

require_once ("Tableau.class.php");
require_once ("HTML.php");

function Entete ($titre, $texte, $code_obs, $bd)
{
?>
<!DOCTYPE HTML>
<HTML lang="fr">
	<HEAD>
		<meta charset="UTF-8" > 
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<!-- Insertion des fichiers CSS-->
        <link rel='stylesheet' HREF='Css.css' TYPE='text/css'/>
		<link rel="stylesheet" media="screen and (min-width: 1025px)" href="<?php echo CHEMIN_URL;?>Css_largescreen.css" type="text/css" />
		<link rel="stylesheet" media="screen and (max-width: 1024px)" href="<?php echo CHEMIN_URL;?>Css_smallscreen.css" type="text/css" />
		<link rel='stylesheet' HREF='Css_fenetre_nodal.css' TYPE='text/css'/>
		<link rel='stylesheet' href='leaflet/leaflet.css' />
		<link rel='stylesheet' href='autocomplete/awesomplete.css'/>

		<!-- Insertion de l'icone-->
		<link rel="icon" href="images/favicon.ico" />

		<!-- Insertion des fichiers javascript-->
		<!-- Leaflet-->		
		<script src="leaflet/geolocalisation.js"></script>
		<script src="leaflet/leaflet.js"></script>
		<!-- Mapbox-->
		<script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-omnivore/v0.3.1/leaflet-omnivore.min.js'></script>
		<!-- Ajax-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<!-- Autocomplétion des espèces-->
		<script src="autocomplete/awesomplete.js"></script>
		<!-- GitHub -->
		<script async defer src="https://buttons.github.io/buttons.js"></script>
		<TITLE><?php echo $titre?></TITLE>

	</HEAD>
	<BODY> 
        
<div id="bloc-tete">
<?php
  	//Extraction de (des) la (les) fonction(s) de l'observateur  
	$select  = "SELECT * FROM observateurs WHERE code_obs='$code_obs'";
	$resultat = $bd->execRequete ($select);
	while ($bo = $bd->objetSuivant ($resultat))
	{
	//Récupération du nom et du prénom
    $obs=Chercheobservateursaveccode ($code_obs, $bd, $format=FORMAT_OBJET);
    $prenom=$obs->prenom;
    $nom=$obs->nom;
    //Est-il responsable d'une structure ?
    $structure=ChercheStructure ($code_obs, $bd);
    if(isset($structure->nom)) $lienStructure="<a href='Structure.php'>Gérer  $structure->nom</a>";
    else $lienStructure="";
	}
    
if ($texte=="1") $Accueil='class="lien_fonce"';
else $Accueil="";
if ($texte=="2") $Ajout='class="lien_fonce"';
else $Ajout="";
if ($texte=="3") $Consultation='class="lien_fonce"';
else $Consultation="";

  //Div pour le bandeau
	?>
            <div class="entete-1">
            <a <?php echo $Accueil;?> href=<?php CHEMIN_URL?>"./index.php"> <img src="images/logo_sanstitre.png" class="img_logo" alt="chargement de l'image"><br><p class="text_logo">epitheca.fr</p> </a>
            </div>
                        
            <div class="entete-2">
            <a <?php echo $Ajout;?> href=<?php CHEMIN_URL?>"./Ajout.php"><img src="images/logo_ajouter.png" class="img_entete" alt="chargement de l'image"><p class="text_entete">Ajouter des données</p></a>
            </div>
        
            <div class="entete-3">
            <a <?php echo $Consultation;?> href=<?php CHEMIN_URL?>"./Consultation.php"><img src="images/logo_consulter.png" class="img_entete" alt="chargement de l'image"><p class="text_entete">Consulter-Exporter</p></a>
            </div>
	<div class="spacer"><br></div>
	<div class="lisere-clair"></div>
    <?php echo "Observateur connecté : $prenom $nom $lienStructure"; ?>
  </div>
  <?php  
}

// Fonction affichant un pied de page, et interrompant le script
function PiedDePage ($session, $code_obs, $bd)
{
	
//Création liens pour le pied de page
 $admin= Ancre_renomme ("mailto:contact@epitheca.fr", "Contact");
 $quoideneuf= Ancre_renomme ("./quoideneuf.php", "Version 1.0.5");  
 $observateurs=Ancre_renomme("Observateurs.php","Gestion observateurs");
 $statistiques=Ancre_renomme("Stats_admin.php","Statistiques");
	
//Calcul du nombre de données   
  $requete  = "SELECT COUNT(*) AS nombre FROM donnees" ;
  $resultat = $bd->execRequete ($requete);
  $nbr = $bd->objetSuivant ($resultat);
  $nombre = number_format("$nbr->nombre", 0, '', ' '); 
  
	
?>
	<!-- ouverture du bloc pied -->
	<div id="bloc-pied">
		
<?php
	gestion_session ("Observateur",$session->prenom,$session->nom);	 
?>		
	
	<div class="pied-gauche">
			<?php
			echo $admin;
			?>
	</div>
	
	<div class="pied-droit">
			<?php
			echo "$quoideneuf";
			?>
		</div>
		<?php
		 //Vérification des droits d'administration
		//Récupération des droits d'administration
		$administration = administration ($code_obs, $bd); 	  

		//Accepté
		if ($administration=="oui") 
		{
			?>
			<div class="pied-gauche">
			<?php
			echo $observateurs;
			?>
	</div>
	
	<div class="pied-droit">
			<?php
			echo "$statistiques";
			?>
		</div>
		<?php
		}
	?>
		<div class="spacer"></div>
		
		<div class="pied-100pc">
		<?php //echo "Il y a actuellement $nombre  données gérées par epitheca.fr";?>
		<br>
		<br>
		Le code source de cette application libre sous licence GPL3 est disponible sur
		<a href="https://github.com/epitheca">GitHub</a>.<br>
		 Besoin d'aide ? <a href="https://github.com/epitheca/epitheca/wiki">Un wiki est en construction.</a>
		<br><br>
		<a href="Charte.php">Mentions légales</a>
		<br><br>
		Copyright © <a href="https://framasphere.org/people/2027e100824e0132ae4e2a0000053625">Mathieu MONCOMBLE</a> 2009-2020
		<br><br>
	<a class="github-button" href="https://github.com/epitheca" data-color-scheme="no-preference: light; light: light; dark: light;" data-size="large" aria-label="Follow @epitheca on GitHub">Follow @epitheca</a>

	</div>
	
</div>
	
 </BODY>
 </HTML>
   <?php
   exit;
}
?>
