<?php
ini_set('display_errors',1);
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

?>
<div id="bloc-page">
    <div class="bloc-100pc">
		<div class="bloc-50pc-gauche">
			<?php
			//Sélection de la structure
			$structure=ChercheStructure ($code_obs, $bd);
			
			//Doit-on supprimer un coordinateur ?
			if (isset($_GET['numero']))  $sup=StructureCoordinateurSup($_GET['numero'], $structure->code,$bd);
			
			echo "<p class='titre'>$structure->nom</p>";
			?>
			<p class="sous-titre">Votre association</p>
			<?php
			echo "<a href='mailto:$structure->mail'>$structure->mail</a><br>Vous êtes l'administrateur de cette structure.";
			?>
			<br><br>
			<p class="sous-titre">Votre logo</p>
			<?php
		
			//Le logo existe-t-il ?
			if (empty($structure->fichier_logo)) $logo="noLogo.jpg";
			else $logo="images/logo/$structure->fichier_logo";
		
			 echo "<div class='centrer'><img src='$logo' alt='logo' max-width='400px' width='100%'></div>";
			 ?>
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
		<p class="sous-titre">Emprise géographique de votre structure</p>
				X : Longitude <input type="text" id="GeoXYFormLon" name="longitude_X" class="number" value="<?PHP echo $structure->x_longitude;?>" style="width:90px; margin-top:10px;" maxlength="10" placeholder="Latitude"  pattern="-?\d{1,3}\.\d+">
				Latitude <input type="text" id="GeoXYFormLat" name="latitude_X" class="number" value="<?PHP echo $structure->x_latitude;?>" style="width:90px; margin-left:13px;" maxlength="10" placeholder="Longitude" pattern="-?\d{1,3}\.\d+">
				<br>
				Y : Longitude 
				<input type="text" id="GeoXYFormLon2" name="longitude_Y" class="number" value="<?PHP echo $structure->y_longitude;?>" style="width:90px ;margin-left:5px;" maxlength="10" placeholder="Longitude" pattern="-?\d{1,3}\.\d+">
				Latitude 
				<input type="text" id="GeoXYFormLat2" name="latitude_Y" class="number" value="<?PHP echo $structure->y_latitude;?>" style="width:90px;margin-left:5px;" maxlength="10" placeholder="Latitude" pattern="-?\d{1,3}\.\d+">
				<br><br>
				<div id="mapid"></div>
				<?php
				//Calcul du point moyen pour le centre de la carte
				$longitude_centre=($structure->y_longitude-$structure->x_longitude)+$structure->x_longitude;
				$latitude_centre=($structure->x_latitude-$structure->y_latitude)+$structure->x_latitude;
								
				echo "<script>var longitude_centre=$longitude_centre; var latitude_centre=$latitude_centre;</script>";
				//Variables pour le rectangle
				?>
				<script>var latA=<?php echo $structure->x_latitude;?>;var latB=latA; var latC=<?php echo $structure->y_latitude;?>; var latD=latC;</script>
				<script>var longA=<?php echo $structure->x_longitude;?>;var longD=longA; var longB=<?php echo $structure->y_longitude;?>; var longC=longB;</script>
				<script src="leaflet/leaflet_structure.js"></script>
				
		</div>
		<div class="bloc-50pc-gauche">
		<p class="sous-titre">Les coordinateurs attachés à votre structure</p>
		<?php
		TabStructureCoordinateurs ($structure->code, $bd);
		TabStructureCoordinateursDemande ($structure->code, $bd);
		AjoutCoordinateur ($structure->code, $bd);
		?>
		</div>
    <?php
            PiedDePage($session, $code_obs, $bd);
