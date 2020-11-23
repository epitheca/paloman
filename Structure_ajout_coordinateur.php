<?php
session_start();

require("Util.php");

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

// Contrôle de la session
$session = ControleAcces ("Structure.php", $_POST, session_id(), $bd);
if (SessionValide ($session, $bd))
{
$observateur = Chercheobservateurs ($session->email, $bd, FORMAT_OBJET);
$code_obs = "$observateur->code_obs";
}

//Controle des valeurs passées en POST

if (isset($_POST['email']))
{
	//L'observateur a-t-il le droit de faire cette demande ?
		//Recherche si une structure est attaché à l'observateur connecté		
			$structure=ChercheStructure ($code_obs, $bd);
			
			//Récupération des valeurs pour la structure
			$nom_structure= $structure->nom;
			$mail_structure=$structure->mail;
			
			//Récupération de l'adresse mail du futur coordinateur
			$mail_coordinateur=$_POST['email'];

		//L'observateur n'existe pas...
			$existe=Chercheobservateurs ($_POST['email'], $bd, $format=FORMAT_OBJET);
			if (!isset($existe->nom))
			{
			?>
			<script>
				alert ("	Aucun observateur n'est associé à cette adresse mail. Cette opération sera possible quand il possédera un compte sur cette base.");
				document.location.href="Structure.php";
			</script>		
			<?php
		}
		else
		{

		//Si les deux codes correspondent pas, on redirige
			if ($structure->code<>$_POST['structure']) header('location: Structure.php');

		//Sinon, on continue
		else
		{	
			//Supression des anciennes demandes
$requete  = "DELETE FROM structure_coordinateurs_temporaire WHERE code_obs='$mail_coordinateur'";
$req = $bd->execRequete ($requete);
					
			//Création d'un mot de passe
			$password=password();
			
			//Récupération du code_structure
			$structure=$_POST['structure'];
			
			if ($_POST['classe_ordre']=="tous")
			{
			//Création de la variable $groupe_offert
			$groupe_offert="<br><ul>";	
				
			$select  = "SELECT * FROM classe_ordre";
			$resultat = $bd->execRequete ($select);
			while ($bo = $bd->objetSuivant ($resultat))
				{
				$ins = "INSERT INTO structure_coordinateurs_temporaire (id_demande, code_structure, code_obs, Classe_ordre) VALUES ('$password', '$structure',  '$mail_coordinateur', '$bo->Code_classe_ordre')"; 
				$res = $bd->execRequete ($ins);
				
				$groupe_offert.="<li>$bo->Classe_ordre </li>";
			}
			//Fermeture de la liste
				$groupe_offert.="</ul>";	
			
			// définition de la variable pour l'orthographe :
			$pluriel="les groupes suivants";
		}
		else
		{
		//Création de la variable $groupe_offert
			$groupe_offert="";	
			$groupe=	$_POST['classe_ordre'];
			$select  = "SELECT * FROM classe_ordre WHERE Code_classe_ordre='$groupe'";
			$resultat = $bd->execRequete ($select);
			while ($bo = $bd->objetSuivant ($resultat))
				{
				$ins = "INSERT INTO structure_coordinateurs_temporaire (id_demande, code_structure, code_obs, Classe_ordre) VALUES ('$password', '$structure',  '$mail_coordinateur', '$bo->Code_classe_ordre')"; 
				$res = $bd->execRequete ($ins);
				
				$groupe_offert="$bo->Classe_ordre";
			}
					
			// définition de la variable pour l'orthographe :
			$pluriel="le groupe suivant ";
		}
			
			//Envoi du mail avec la proposition
			$subject="epitheca.fr : Votre compte";
		

$message="
<center>Base de données naturalistes epitheca.fr<br>
<img src=\"https://epitheca.fr/images/logo200pt.png\"></center><br>
<p align=center>Ceci est un message automatique.</p><br>
Bonjour,<br>
Une association naturaliste, <b>$nom_structure</b> souhaite que vous coordonniez des données pour elle.<br><br>
Elle souhaite que vous coordonniez $pluriel : 
$groupe_offert<br>
<div align='center'>
<a href='https://epitheca.fr/Structure_ajout_coordinateur_confirmation.php?m=$session->email&u=$password'>Cliquez sur ce lien pour accepter cette coordination. </a>
<br>
Attention, ce lien est valable une semaine.
</div><br><br>
Vous pouvez contacter l'association $nom_structure en cas de besoin : <a href='mailto:$mail_structure'>$mail_structure</a><br><br>

En cas de problème, vous pouvez copier ce lien dans votre navigateur : <br><b>https://epitheca.fr/Structure_ajout_coordinateur_confirmation.php?m=$session->email&u=$password</b>
<br><br>
Je reste à votre disposition pour plus de renseignements.<br>

Mathieu MONCOMBLE
<br>
";

$to = $mail_coordinateur;
	
// Version MINE
$headers = "MIME-Version: 1.0\n";
 
// en-têtes expéditeur
$headers .= "From : $mail_administrateur\n";
 
// en-têtes adresse de retour
$headers .= "Reply-to : $mail_administrateur\n";
 
// priorité urgente
$headers .= "X-Priority : 3\n";
 
// type de contenu HTML
$headers .= "Content-type: text/html; charset=utf-8\n";
 
// code de transportage
$headers .= "Content-Transfer-Encoding: 8bit\n";
 
 mail($to,$subject,$message, $headers);
 ?>
 	<script>
		alert ("Un message vient d'être envoyé à cet observateur pour lui demandé s'il accepte de coordonner les données.");
		document.location.href="Structure.php";
	</script>
			<?php		
			
			
			}
			}
		}
//Le champ est vide, retour vers la page
else header('location: Structure.php');
?>
