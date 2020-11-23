<?php
session_start();
require("Util.php");

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);
$session = ControleAcces ("Observateurs_demande.php", $_POST, session_id(), $bd);
if (SessionValide ($session, $bd))
{
// Production de l'entête
$observateur = Chercheobservateurs ($session->email, $bd, FORMAT_OBJET);
$code_obs = "$observateur->code_obs";
Entete ("epitheca.fr", "6", $code_obs, $bd);
}

if (isset($_POST['valider']))
{
	//Récupération des valeurs
	$mail=$_POST['email'];
	if (!filter_var ($mail, FILTER_VALIDATE_EMAIL)) 
	{
		?>
		<script>
			alert  ("L'adresse de courriel doit être de la forme xxx@yyy[.zzz].");
					document.location.href="ObservateursMAJ.php";
		</script>
		<?php
	}
	else
	{
	$obs=$_POST['code_obs'];
	
	$password=password();
	    
    $password_url_accepter="https://epitheca.fr/Observateurs_association.php?cle=$password&accept=yes";
    $password_url_refuser="https://epitheca.fr/Observateurs_association.php?cle=$password&accept=no";
	
	//L'adresse mail est-elle associé à un observateur de la base ?
	$existe=Chercheobservateurs ($mail, $bd, $format=FORMAT_OBJET);
	
	if (isset($existe->nom))
	{
		?>
		<script>
		alert ("Un message vient d'être envoyé à cet observateur pour lui demandé s'il accepte d'être associé à vos données.");
				document.location.href="ObservateursMAJ.php";
			</script>
			<?php
								
			//Extraction des renseignements sur l'observateur faisant la demande
			$obs=Chercheobservateursaveccode ($obs, $bd, $format=FORMAT_OBJET); 
			$mail_obs=$obs->email;
			$code_obs=$obs->code_obs;
			$prenom_obs=$obs->prenom;
			$nom_obs=$obs->nom;
			
			//Vérification de l'existence de la demande précédente
			$resultat = $bd->execRequete ("SELECT * FROM observateurs_demande WHERE code_obs_demande='$existe->code_obs' AND code_obs='$code_obs'");
			while ($bq = $bd->objetSuivant ($resultat))
			$resultat2 = $bd->execRequete ("DELETE FROM observateurs_demande WHERE code_obs_demande='$existe->code_obs' AND code_obs='$code_obs'");
							
			//Insertion dans la table des demandes
			//Calcul de la date dans une semaine
			$timestamp= date ("Y-m-d H:i:s", strtotime('+1 week'));
			 $ins_demande = "INSERT INTO observateurs_demande (id_demande, code_obs_demande, code_obs, timestamp) "
       . "VALUES ('$password', '$existe->code_obs', '$code_obs', '$timestamp') ";
			$res = $bd->execRequete ($ins_demande); 	
					
			
			$ancre_accepter = Ancre_renomme ($password_url_accepter, 'accepter d\'être associé à cet observateur');
			$ancre_refuser = Ancre_renomme ($password_url_refuser,' refuser d\'être associé à cet observateur');
	$from =  $mail_obs;
	$to = $mail;
	$headers = 'From: ' . $from . "\r\n" .
            'Reply-To: ' . $from . "\r\n" .
            'Content-type: text/html; charset= utf-8' ."\r\n";
	$message= "
	<head>
       <title>epitheca.fr</title>
      </head>
      <body>
      <table>
        <tr>
         <th></th><th></th>
        </tr>
        <tr>
         <td><img src=\"https://epitheca.fr/images/logo200pt.png\"></td><td><H3>Base de données naturalistes Epitheca.fr</H3></td>
        </tr>
        </table>
      <p>Bonjour ce message est généré à la demande de $prenom_obs $nom_obs qui souhaite pouvoir vous associer à ses données.</p>
	
	 <p>Vous pouvez $ancre_accepter ou $ancre_refuser .</p>
	 <br><br>
	 Cette demande n'est valable qu'une semaine.
	 <br><br>
	 <p>En cas de problème, vous pouvez copier/coller cette adresse pour accepter : $password_url_accepter 
	 <br>
	 ou celle-ci pour refuser : $password_url_refuser
	 </p> ";
	 
mail ($to, "Demande d'association sur la base Epitheca.fr", $message, $headers);
		}
	else 
	{
			?>
			<script>
				alert ("	Aucun observateur n'est associé à cette adresse mail. Cette opération sera possible quand il possédera un compte sur cette base.");
				document.location.href="ObservateursMAJ.php";
			</script>		
			<?php
		}	
}}
// Affichage du pied de page
PiedDePage($session, $code_obs, $bd);
  
?>
