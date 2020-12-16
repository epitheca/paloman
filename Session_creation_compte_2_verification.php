<?php
require("Util.php");

// Connexion à la base
$bd = Connexion (NOM, PASSE, BASE, SERVEUR);

if ($_POST['valider']) {
    $email   = $_POST['email'];
    
    // On va quand même vérifier que cet email n'est pas déjà inséré
      $controleemail=Chercheobservateurs($email, $bd, FORMAT_OBJET);
      if (isset($controleemail))
      {
      if ($email == $controleemail->email)
	{
		?>
	<script type="text/javascript">
			window.alert("<?php echo "Un observateur avec cet email existe déjà." ?>");
			window.location.replace("https://epitheca.fr");
			//-->
			</script>
		<?php
	}}
    //C'est bon, on continue
    else
    {
    $token  = $_POST['token'];
    $action = "contact";
   
    $curlData = array(
        'secret' => $CLE_reCAPTCHA_secrete,
        'response' => $token
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($curlData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curlResponse = curl_exec($ch);
    
    $captchaResponse = json_decode($curlResponse, true);
        
    if ($captchaResponse['success'] == '1' && $captchaResponse['action'] == $action && $captchaResponse['score'] >= 0.5 && $captchaResponse['hostname'] == $_SERVER['SERVER_NAME']) {
	
	include ("Session_creation_compte_3_premier_mail.php");
	}
	else {
		//redirection en dehors du site
        ?>
		<SCRIPT LANGUAGE="JavaScript">
			alert="Vous avez été reconnu comme étant un robot par le système de sécurité.";
			document.location.href="https://epitheca.fr"
		</SCRIPT>
				<?php
    }
}}
?>
