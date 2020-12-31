<?php
session_start();

require("Util.php");
include ("Listes.php");
require_once ("Structure_tableau_coordinateurs.php");
require_once ("Structure_fonctions.php");

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

// Contrôle de la session
$session = ControleAcces ("Structure.php", $_POST, session_id(), $bd);
if (SessionValide ($session, $bd))
{
$observateur = Chercheobservateurs ($session->email, $bd, FORMAT_OBJET);
$code_obs = "$observateur->code_obs";
Entete ("epitheca.fr", "1", $code_obs, $bd);
}

//Sélection de la structure
	$structure=ChercheStructure ($code_obs, $bd);
			
//Doit-on supprimer un coordinateur ?
	if (isset($_GET['numero'])&&$_GET['mode']=='temp')  $sup=StructureCoordinateurSup($_GET['numero'], $structure->code,$_GET['mode'],$bd);
	if (isset($_GET['numero'])&&$_GET['mode']=='def')  $sup=StructureCoordinateurSup($_GET['numero'], $structure->code,$_GET['mode'],$bd);
?>

<!-- Début du bloc page -->
<div id="bloc-page">
    <div class="bloc-100pc">
		<?php echo "<p class='titre'>$structure->nom</p>"; ?>
		<div class="bloc-50pc-gauche">		
			<!--Titre de l'association  -->
			<p class="sous-titre">Votre association</p>
			<?php
			echo "<a href='mailto:$structure->mail'>$structure->mail</a><br>Vous êtes l'administrateur de cette structure.<br><br>";
			// Le logo existe-t-il ?
			if (empty($structure->fichier_logo)) $logo="noLogo.jpg";
			else $logo="images/logo/$structure->fichier_logo";
			 echo "<div class='centrer'><img src='$logo' alt='logo' max-width='400px' width='100%'></div>";
			?>
			<!-- Début du formulaire pour le changement du logo -->
			<form action="Structure_ajout_logo.php" method="post" enctype="multipart/form-data">
				 <label for="fichier">Ajouter ou modifier un logo pour votre structure.</label>
				 <br>
				<input type="file" name="fichier" id="fichier" accept="image/*" capture>
				<input type="hidden" name="code_structure" value="<?php echo $structure->code; ?>">
				<input type="hidden" name="code_obs" value="<?php echo $code_obs; ?>">
				<input type="submit" value="Envoyer le fichier" name="submit">
			</form>		
		</div>

		<div class="bloc-50pc-droit">
			<!-- Affichage des coordonnées de l'emprise -->
			<p class="sous-titre">Emprise géographique de votre structure</p>
			X : Longitude : <?PHP echo $structure->x_longitude;?>
			Latitude : <?PHP echo $structure->x_latitude;?>
			<br>
			Y : Longitude :<?PHP echo $structure->y_longitude;?>
			Latitude : <?PHP echo $structure->y_latitude;?>
			<br><br>
			
			<!-- Affichage de la carte -->
			<div id="mapid"></div>
			<?php
			//Calcul du point moyen pour le centre de la carte
			$longitude_centre=($structure->y_longitude-$structure->x_longitude)+$structure->x_longitude;
			$latitude_centre=($structure->x_latitude-$structure->y_latitude)+$structure->x_latitude;
			//Création des variables Javascript 
				
			// Variable pour le centre
				echo "<script>var longitude_centre=$longitude_centre; var latitude_centre=$latitude_centre;</script>";
			?>
			
			<!--Variables pour le rectangle  -->
			<script>var latA=<?php echo $structure->x_latitude;?>;var latB=latA; var latC=<?php echo $structure->y_latitude;?>; var latD=latC;</script>
			<script>var longA=<?php echo $structure->x_longitude;?>;var longD=longA; var longB=<?php echo $structure->y_longitude;?>; var longC=longB;</script>
			
			<!-- Insertion du script de la carte -->
			<script src="leaflet/leaflet_structure.js"></script>
		</div>
	</div>
	
	<div class="bloc-100pc"></div>
		<div class="bloc-50pc-gauche">
			<p class="sous-titre">Coordinateurs</p>
			<?php
			// Script pour les coordinateurs actuels
			TabStructureCoordinateurs ($structure->code, $bd);
			// Script pour les demandes en cours
			TabStructureCoordinateursDemande ($structure->code, $bd);
			// Script pour les nouvelles demandes
			AjoutCoordinateur ($structure->code, $bd);
			?>
		</div>

		<div class="bloc-50pc-droit">
			<!-- Charte de l'association -->
			<p class="sous-titre">Charte vous liant aux observateurs</p>
			<p class="attention">La modification de cette charte entraine un désengagement automatique de tous les observateurs</p>
			<form action="Structure_ajout_charte.php" method="post" enctype="multipart/form-data">
				<label for="fichier">Modifier la charte pour votre structure.</label>
				<br>
				<input type="file" name="fichier" id="fichier" accept="application/pdf">
				<input type="hidden" name="code_structure" value="<?php echo $structure->code; ?>">
				<input type="hidden" name="code_obs" value="<?php echo $code_obs; ?>">
				<input type="submit" value="Envoyer le fichier" name="submit">
			</form>

			<!-- Liste des observateurs collaborant actuellement -->
			<p class="sous-titre">Observateurs qui collaborent avec votre structure</p>
			<?php
			// Script pour les nouvelles demandes
			TabStructureObservateurs ($structure->code, $bd);
			?>
		</div>
	</div>
</div>

<?php
PiedDePage($session, $code_obs, $bd);
?>