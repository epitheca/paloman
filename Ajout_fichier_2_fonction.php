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

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" charset="utf-8" > 
<link rel='stylesheet' HREF='Css_iframe_fichier.css' TYPE='text/css'>
<link rel='stylesheet' HREF='Css_fenetre_nodal.css' TYPE='text/css'>
</HEAD>
<BODY>
<?php

$target_dir = "temporaire/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$type = pathinfo($target_file,PATHINFO_EXTENSION);

//Capture du numéro de la donnée
$numero=$_POST['numero'];

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {

// Check file size


	if ($type=="png" || $type=="jpg" || $type=="jpeg" || $type=="PNG" || $type=="JPG" || $type=="JPEG")
	{
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			$uploadOk = 1;
		} else {
			$uploadOk = 0;
		}
	}

	if($type=='audio/mp3')
     {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			$uploadOk = 1;
		} else {
			$uploadOk = 0;
         }
}}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
			?>
			<script type="text/javascript">
			window.alert("<?php echo "Le fichier est trop volumineux." ?>");
			window.location.replace("Ajout_fichier_2.php?numero=<?php echo $numero;?>");
			</script>    
			<?php    $uploadOk = 0;
}

// Allow certain file formats
if($type != "jpg" && $type != "png" && $type != "jpeg"
&& $type != "gif" && $type != "mp3") {
			?>
			<script type="text/javascript">
			window.alert("<?php echo "Le fichier doit être au format mp3, gif, png, jpg ou jpeg" ?>");
			window.location.replace("Ajout_fichier_2.php?numero=<?php echo $numero;?>");
			</script>    
			<?php
			$uploadOk = 0;

}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk <> 0) {
	
	//Récupération d'un time stamp
	$timestamp= time ();
	
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        //On renomme le fichier
        $anciennom =$target_dir;
        $anciennom .=basename( $_FILES["fileToUpload"]["name"]);
        $nouveaunom = "televersements/$timestamp.$type";
		rename ($anciennom, $nouveaunom);
		//Redirection à ajouter
		?>
		<SCRIPT LANGUAGE="JavaScript">
			document.location.href="Ajout_fichier_2.php?numero=<?php echo $numero ; ?>"
		</SCRIPT>
				<?php
	       
   //Ajout du fichier dans la table
$ins_fichier = "INSERT INTO fichiers (numero_donnee, identifiant) VALUES ('$numero', '$timestamp.$type') "; 
$res = $bd->execRequete ($ins_fichier);    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
echo "</div>";
?>
