<?php
session_start();

require("Util.php");
include ("Listes.php");	


//Récupération de l'URL
$monUrl = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

// Contrôle de la session
$session = ControleAcces ("$monUrl", $_POST, session_id(), $bd);
if (SessionValide ($session, $bd))
{
$observateur = Chercheobservateurs ($session->email, $bd, FORMAT_OBJET);
$code_obs = "$observateur->code_obs";
Entete ("epitheca.fr", "1", $code_obs, $bd);
}

//Capture des valeurs
if (isset($_GET['m'])) $email = $_GET['m'];
if (isset($_GET['u']))  $secret = $_GET['u'];

$control="no";

 if (isset($email))
	{
 //Vérification de la légitimité de la requête;
$select  = "SELECT * FROM structure_coordinateurs_temporaire WHERE code_obs='$email' AND id_demande='$secret'";
			$resultat = $bd->execRequete ($select);
			while ($bo = $bd->objetSuivant ($resultat))
				{
				//Vérification du temps
				$timestamp_demande=strtotime($bo->timestamp);
				$timestamp_actuel=time();
				if ($timestamp_demande+691200<$timestamp_actuel) $control="La demande a expiré.";
				else $control="yes";
					}
	}

if ($control<>"yes")
			{
		?>
		<p class="titre">Votre demande n'est pas sécurisée. Le lien suivi n'est pas valide.</p>
		<?php
		}
		else
		{
					
		//On ajoute le coordinateur à la table
		$select  = "SELECT * FROM structure_coordinateurs_temporaire WHERE code_obs='$email' AND id_demande='$secret'";
			$resultat = $bd->execRequete ($select);
			while ($bo = $bd->objetSuivant ($resultat))
				{
					//récupération du code observateur
					$observateur=Chercheobservateurs ($email, $bd);
					
					//Vérification dans la table définitive
					$requete3  = "DELETE FROM structure_coordinateurs WHERE code_observateurs='$observateur->code_obs' AND taxon='$bo->Classe_ordre'";
					$resultat3 = $bd->execRequete ($requete3);
					
					//Insertion dans la table définitive
					$ins = "INSERT INTO structure_coordinateurs (code_structure, code_observateurs, taxon) VALUES ('$bo->code_structure', '$observateur->code_obs', '$bo->Classe_ordre')"; 
					$res = $bd->execRequete ($ins);
				
					//Supression dans la table temporaire
					$requete2  = "DELETE FROM structure_coordinateurs_temporaire WHERE code_obs='$email' AND id_demande='$secret' AND Classe_ordre='$bo->Classe_ordre'";
					$resultat2 = $bd->execRequete ($requete2);
				
				}	
			
			//Recherche du nom de la structure
			//Sélection de la structure
			$structure=ChercheStructure ($code_obs, $bd);
			
		?>
    <div class="100pc">
    <p class="titre">Vous êtes désormais coordinateur pour l'association</p>
    </div>
    <div class="bloc-50pc-droit-centre">
       <?php
       echo "<p class='sous-titre'>$structure->nom </p>";
  if (empty($structure->fichier_logo)) $logo="noLogo.jpg";
			else $logo="images/logo/$structure->fichier_logo";
		
			 echo "<div class='centrer'><img src='$logo' alt='logo' max-width='400px' width='100%'></div>";
		 }
			 ?>
   </div>
    </div>
<?php
    PiedDePage($session, $code_obs, $bd);
    ?>	


