<?php

session_start();
require("Util.php");
// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

/************************************************************
 * Script realise par Emacs
 * Crée le 19/12/2004
 * Maj : 23/06/2008
 * Licence GNU / GPL
 * webmaster@apprendre-php.com
 * http://www.apprendre-php.com
 * http://www.hugohamon.com
 *
 * Changelog:
 *
 * 2008-06-24 : suppression d'une boucle foreach() inutile
 * qui posait problème. Merci à Clément Robert pour ce bug.
 *
 *************************************************************/
 
/************************************************************
 * Definition des constantes / tableaux et variables
 *************************************************************/
 

// Constantes
define('TARGET', 'images/logo/');    // Repertoire cible
define('MAX_SIZE', 100000);    // Taille max en octets du fichier
define('WIDTH_MAX', 800);    // Largeur max de l'image en pixels
define('HEIGHT_MAX', 800);    // Hauteur max de l'image en pixels
 
 // Tableaux de donnees
$tabExt = array('jpg','gif','png','jpeg');    // Extensions autorisees
$infosImg = array();
 
// Variables
$extension = '';
$message = '';
$nomImage = '';

/************************************************************
 * Creation du repertoire cible si inexistant
 *************************************************************/
if( !is_dir(TARGET) ) {
  if( !mkdir(TARGET, 0755) ) {
    exit('Erreur : le répertoire cible ne peut-être créé ! Vérifiez que vous diposiez des droits suffisants pour le faire ou créez le manuellement !');
  }
}
 
/************************************************************
 * Script d'upload
 *************************************************************/
if(!empty($_POST))
{
  // On verifie si le champ est rempli
  if( !empty($_FILES['fichier']['name']) )
  {
    // Recuperation de l'extension du fichier
    $extension  = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);
 
    // On verifie l'extension du fichier
    if(in_array(strtolower($extension),$tabExt))
    {
      // On recupere les dimensions du fichier
      $infosImg = getimagesize($_FILES['fichier']['tmp_name']);
 
      // On verifie le type de l'image
      if($infosImg[2] >= 1 && $infosImg[2] <= 14)
      {
		  
        // On verifie les dimensions et taille de l'image
        if(($infosImg[0] <= WIDTH_MAX) && ($infosImg[1] <= HEIGHT_MAX) && (filesize($_FILES['fichier']['tmp_name']) <= MAX_SIZE))
        {
          // Parcours du tableau d'erreurs
          if(isset($_FILES['fichier']['error']) 
            && UPLOAD_ERR_OK === $_FILES['fichier']['error'])
            
          {
            // On renomme le fichier
            $nomImage = md5(uniqid()) .'.'. $extension;
 
            // Si c'est OK, on teste l'upload
            if(move_uploaded_file($_FILES['fichier']['tmp_name'], TARGET.$nomImage))
            {
				//Supression de l'éventuel précédent fichier
				
				//Sélection de la structure
				$structure=ChercheStructure ($_POST['code_obs'], $bd);
								echo $structure->fichier_logo;

				if (empty($structure->fichier_logo)) unlink (TARGET.$structure->fichier);
				//Insertion du fichier dans la base
				INSFichierLogo ($nomImage, $_POST['code_structure'], $bd);
             header('Location: Structure.php');
            }
            else
            {
              // Sinon on affiche une erreur systeme
              ?> <script>alert ('Il y a eu un problème dans l\'import du fichier')</script><?php
               header('Location: Structure.php');
              
            }
          }
          else
          {
         <script>alert ("Un problème est survenu.");
			document.location.href="Structure.php";
         </script>
          }
        }
        else
        {
          // Sinon erreur sur les dimensions et taille de l'image
         ?> <script>alert ("L\'image n\'a pas les bonnes dimensions (800 pixels maximum)");
			document.location.href="Structure.php";
         </script>
         <?php
              
        }
      }
      else
      {
        // Sinon erreur sur le type de l'image
          ?> <script>alert ("Le fichier n\'est pas une image");
          document.location.href="Structure.php";
          </script>
          <?php
      }
    }
    else
    {
      // Sinon on affiche une erreur pour l'extension
         ?> <script>alert ('L\'extension n\'est pas prise en charge');
         document.location.href="Structure.php";
         </script>
         <?php
    }
  }
  else
  {
    // Sinon on affiche une erreur pour le champ vide
    header('Location: Structure.php');
  }
}
	
?>     
