<?php

if (isset($_POST['maj']))
{

// Contrôle des variables passées en POST
$message = ControleObs($_POST) ;

	// Erreur de saisie détectée: on affiche le message
if (!empty($message))
{  
fenetre_modal ("Erreur de saisie","$message");
}
	
	//Pas d'erreur on continue
else
{	
  // Traitement des apostrophes
  $email = $bd->prepareChaine($_POST['email']);
  $nom = $bd->prepareChaine($_POST['nom']);
  $prenom = $bd->prepareChaine($_POST['prenom']);  
  
  //Capture de autres champs
  $code_obs=$_POST['code_obs'];
 
	//Mise à jour des observateurs accompagnants
 if ($_POST['mode'] == "MAJ_OBS" || $_POST['mode'] == "MAJ_OBS_ADM") 
    {
		$obs2=$_POST['obs1'];
 $requete  = "UPDATE observateurs SET association=$obs2, associabilite='$associabilite'  WHERE code_obs='$code_obs'";
	  $req = $bd->execRequete ($requete);
}

	//Mise à jour  
  if ($_POST['mode'] == "MAJ" || $_POST['mode'] == "MAJperso") 
    {
    	//Un nouveau mot de passe a été saisi
    	if ($_POST['nouveau_mot_de_passe'] <> "")
			{
			//Cryptage du mot de passe
			$motCrypte = md5 ($_POST['nouveau_mot_de_passe']);
			//Modification du compte
			$requete  = "UPDATE observateurs SET nom='$nom', prenom='$prenom', "
			. "email='$email', mot_de_passe='$motCrypte' WHERE code_obs='$code_obs'";
			$req = $bd->execRequete ($requete);
	  		}
  		
  		//Aucun nouveau mot de passe fut saisi
  		if ($_POST['nouveau_mot_de_passe'] == "")
    	{
		$requete  = "UPDATE observateurs SET nom='$nom', prenom='$prenom', "
		. "email='$email' WHERE code_obs='$code_obs'";
		$req = $bd->execRequete ($requete);
  		}
  	if ($_POST['mode'] == "MAJperso")
  	{
	?>	
				<SCRIPT LANGUAGE="JavaScript">
				document.location.href="Deconnexion.php"
				</SCRIPT>
	<?php
	}
  		
       }
    
     //Insertion  
  if ($_POST['mode']=="INSERTION")  
    {
      // On va quand même vérifier que cet email n'est pas déjà inséré
      $controleemail=Chercheobservateurs($email, $bd, FORMAT_OBJET);
      if (isset($controleemail))
      {
      if ($email == $controleemail->email)
	{
		?>
	<script type="text/javascript">
			<!--
			window.alert("<?php echo "Un observateur avec cet email existe déjà." ?>");
			window.location.replace("Observateurs_maj_admin.php?mode=MAJ&codeobsamaj=<?php echo $controleemail->code_obs;?>");
			//-->
			</script>
		<?php
	}}
	   else
	{
		//Création du mot de passe
// on declare une chaine de caractÃ¨res
$chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@";

//nombre de caractÃ¨res dans le mot de passe
$nb_caract = 8;

// on fait une variable contenant le futur pass
$pass = "EPI";

//on fait une boucle
for($u = 1; $u <= $nb_caract; $u++) {
    
//on compte le nombre de caractÃ¨res prÃ©sents dans notre chaine
    $nb = strlen($chaine);
    
// on choisie un nombre au hasard entre 0 et le nombre de caractÃ¨res de la chaine
    $nb = mt_rand(0,($nb-1));
    
// on ajoute la lettre à la valeur de $pass
    $pass.=$chaine[$nb];
}
	  $motCrypte = md5 ($pass);
	  $requete  = "INSERT INTO observateurs (nom, prenom, email, "
	    . "mot_de_passe) "
	    . "VALUES ('$nom', '$prenom', "
	    . "'$email', '$motCrypte')";
	  $req = $bd->execRequete ($requete);
	  
	  fenetre_modal ("L'utilisateur à été crée avec succès","Le mot de passe est $pass<br>Vous pouvez lui envoyer automatiquement un mail comprenant ces informations en cliquant <a href='observateur_mail.php?code_obs=$code_obs&email=$email&pass=$pass&mode=nouveau'>ici</a>"); 
		
		//Extraction du code		
		$resultat = $bd->execRequete ("SELECT * FROM observateurs WHERE nom='$nom' && prenom='$prenom' && email='$email'");     
	while ($bo = $bd->objetSuivant ($resultat)) 
    $code=$bo->code_obs;
	   
	}
    }
	
	     //Insertion  
  if ($_POST['mode']=="NOUVEAUMOTDEPASSE")  
    {
    
		//Création du mot de passe
// on declare une chaine de caractÃ¨res
$chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@";

//nombre de caractÃ¨res dans le mot de passe
$nb_caract = 8;

// on fait une variable contenant le futur pass
$pass = "EPI";

//on fait une boucle
for($u = 1; $u <= $nb_caract; $u++) {
    
//on compte le nombre de caractÃ¨res prÃ©sents dans notre chaine
    $nb = strlen($chaine);
    
// on choisie un nombre au hasard entre 0 et le nombre de caractÃ¨res de la chaine
    $nb = mt_rand(0,($nb-1));
    
// on ajoute la lettre à la valeur de $pass
    $pass.=$chaine[$nb];
}
	  $motCrypte = md5 ($pass);
	  $requete  = "UPDATE observateurs SET mot_de_passe='$motCrypte' WHERE email='$email'";
	  $req = $bd->execRequete ($requete);
	  
	  fenetre_modal ("Le mot de passe a été modifié","Le mot de passe est $pass<br>Vous pouvez envoyer automatiquement un mail comprenant cette information en cliquant <a href='observateur_mail.php?mode=motdepasse&email=$email&pass=$pass'>ici</a>"); 
		
		//Extraction du code		
		$resultat = $bd->execRequete ("SELECT * FROM observateurs WHERE nom='$nom' && prenom='$prenom' && email='$email'");     
	while ($bo = $bd->objetSuivant ($resultat)) 
    $code=$bo->code_obs;
	   }
	
}}

?>
