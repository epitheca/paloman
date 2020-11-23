<?php
function form_fichier ($fichier, $numero, $bd)
{
	//Vérification de l'existence d'un fichier joint
	$fichier=Cherche_fichier ($numero, $bd, $format=FORMAT_OBJET);
	
	//Il y a un fichier joint
	if ($fichier<>"X")
	{
	?>
	<br><br>
	<iframe width="890" height="270" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" src='Ajout_fichier_2.php?numero=<?php echo $numero;?>'></iframe>
	<?php
	}
	if ($fichier=="X")
	{
	?>
	<iframe width="890" height="40" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src='Ajout_fichier.html'></iframe>
	<?php
	}
}

function form_observateur ($intitule, $nom, $obsPrincipal, $obs_nom, $bd)
{
	//Vérification de l'existence d'observateur associé.
	$observateurAssocie=observateurAssocie ($obsPrincipal, $bd);
	
	//Comptage du nombre d'observateur associé
	$nombreAssocie=count($observateurAssocie);
		
$comma_separated = implode(",", $observateurAssocie);

$resultat  = $bd->execRequete ("SELECT code_obs, nom, prenom FROM observateurs WHERE code_obs IN (1, $comma_separated) ORDER BY prenom");

  while ($obs = $bd->objetSuivant ($resultat))
    $liste_obs["$obs->code_obs"] = "$obs->prenom $obs->nom";
  echo "$intitule";
  ?>  
<select name="obs<?php echo "$nom";?>">
<?php
foreach($liste_obs as $code => $description) {  
		if ($obs_nom==$code)   echo "<option selected value=\"$code\">$description</option>\n";              
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>  
<?php
}

function form_observateur_sans_entete ($obs_nom, $bd)
{
	// Listes pour les observateurs
$resultat  = $bd->execRequete ("SELECT code_obs, nom, prenom FROM observateurs ORDER BY prenom");
  while ($obs = $bd->objetSuivant ($resultat))
    $liste_obs["$obs->code_obs"] = "$obs->prenom $obs->nom";

foreach($liste_obs as $code => $description) {  
		if ($obs_nom==$code)   echo "<option selected value=\"$code\">$description</option>\n";              
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>  
<?php
}
       
    // création liste vent
function form_vent ($vent, $bd)
{
	
$resultat  = $bd->execRequete ("SELECT code_vent, vent FROM vent");
  while ($ventl = $bd->objetSuivant ($resultat))
    $liste_vent["$ventl->code_vent"] = "$ventl->vent";
?>
Vent : <select name="vent">
<?php
foreach($liste_vent as $code => $description) { 
	if ($vent==$code)   echo "<option selected value=\"$code\">$description</option>\n";   
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php
}
		

function form_condclim ($cond_clim, $bd)
{
	//Création de la liste en php
$resultat  = $bd->execRequete ("SELECT conditions_climatiques, code_conditions_climatiques FROM conditions_climatiques");
  while ($cond = $bd->objetSuivant ($resultat))
    $liste_cond["$cond->code_conditions_climatiques"] ="$cond->conditions_climatiques";
    
    //Création de la liste en html
	?>
Météo : <select name="cond_clim">
<?php
foreach($liste_cond as $code => $description) { 
	if ($cond_clim==$code)   echo "<option selected value=\"$code\">$description</option>\n";
	else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php
}

// Création du champ pour la température
function form_temperature ($temp, $bd)
{
?>
Température : <input type="text" name="temp" value="<?PHP echo $temp;?>" size="4" MAXLENGTH="4">°C
<?php
}


function form_corine ($numero, $corine_nom,  $margegauche,$largeur, $bd)
{
	
	//Création du style
$style=styleduselect($margegauche, $largeur);
	
$resultat  = $bd->execRequete ("SELECT * FROM corine WHERE FRANCE LIKE 'true' ORDER BY LB_CODE");
  while ($corine = $bd->objetSuivant ($resultat))
  {
	  if ($corine->LB_HAB_FR=="") $nom_corine=substr($corine->LB_HAB_EN,0,50);
	 else $nom_corine=substr($corine->LB_HAB_FR,0,50);
	  $liste_corine["$corine->CD_HAB"] ="$corine->LB_CODE,  $nom_corine";
}
echo "$numero  "; ?>
<select name="corine<?php echo $numero;?>" <?php echo $style;?>>
<?php
foreach($liste_corine as $code => $description) {  
	if ($corine_nom==$code)   echo "<option selected value=\"$code\">$description</option>\n";                   
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php
}

function form_route ($route, $margegauche,$largeur, $bd)
{
	
	//Création du style
$style=styleduselect($margegauche, $largeur);

// création liste routes
$resultat  = $bd->execRequete ("SELECT code_route, nom FROM routes");
  while ($routes = $bd->objetSuivant ($resultat))
    $liste_routes["$routes->code_route"] = "$routes->nom";
    
//Liste en html
?>
Route :<select name="route" <?php echo $style;?>>
<?php
foreach($liste_routes as $code => $description) { 
	if ($route==$code)   echo "<option selected value=\"$code\">$description</option>\n";        
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php
}
	
// création liste rivières
function form_riviere ($riviere,$margegauche,$largeur, $bd)
{
	//Création du style
$style=styleduselect($margegauche, $largeur);

$resultat  = $bd->execRequete ("SELECT code_riviere, nom FROM rivieres ORDER by nom");
  while ($rivieres = $bd->objetSuivant ($resultat))
    $liste_rivieres["$rivieres->code_riviere"] = "$rivieres->nom";  
    ?>
Rivière :<select name="riviere" <?php echo $style;?>>
<?php
foreach($liste_rivieres as $code => $description) {             
		if ($riviere==$code)   echo "<option selected value=\"$code\">$description</option>\n";
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select> 
<?php
} 

//Création de la liste des groupes
function form_groupe_avec_tous ($groupe, $margegauche, $largeur, $bd)
{
//Création du style
$style=styleduselect($margegauche, $largeur);
	
// Listes des groupes
$resultat  = $bd->execRequete ("SELECT * FROM classe_ordre");
  while ($classe_ordre = $bd->objetSuivant ($resultat))
  $liste_classe_ordre[$classe_ordre->Code_classe_ordre] = $classe_ordre->Classe_ordre;
?>
Groupe : <select name="classe_ordre" onchange='submit()' <?php echo $style;?>>
<?php
	if ($groupe=="tous") echo "<option selected value=\"tous\">Tous les groupes</option>\n";
	else echo "<option value=\"tous\">Tous les groupes</option>\n";
foreach($liste_classe_ordre as $code => $description) {  
	if ($groupe==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php
}

//Liste pour l'abondance
function form_abondance ($abondance,$bd)
{
?>
<label for="id="abo">Effectif :</label>
<input type="number" name="abo" id="sexe" size="5" min="0" max="99999" value="<?php echo $abondance; ?>">
<?php
}

//Bloc pour les espèces
function form_espece ($sps, $info_1, $info_2, $sexe, $bd)
{
  	//Recherche du groupe de l'espèce
	$groupe=ChercheGroupeSp ($sps, $bd);
	
 	//Stade 	
	$resultat  = $bd->execRequete ("SELECT * FROM information WHERE $groupe LIKE 'oui' ORDER BY  information");
	while ($infol = $bd->objetSuivant ($resultat))
    $liste_info[$infol->code] = $infol->information;
	?>
	<div class="adaptative">
	<label for="info_1">Stade et développement :</label><select name="info_1" style="margin-right:50px">
	<?php
	foreach($liste_info as $code => $description) {    
	if ($info_1==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
	else echo "<option value=\"$code\">$description</option>\n";
	}
	?>
	</select></div>
	<?php	
	
	//Comportement
	$resultat  = $bd->execRequete ("SELECT * FROM information_2 WHERE $groupe LIKE 'oui' ORDER BY information  ");
	while ($infol_2 = $bd->objetSuivant ($resultat))
    $liste_info_2[$infol_2->code] =$infol_2->information;	  
	?>
	<div class="adaptative">
	<label for="info_2">Comportement :</label>
	<select name="info_2" id="info_2">
	<?php	
	foreach($liste_info_2 as $code => $description) {    
	if ($info_2==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
	else echo "<option value=\"$code\">$description</option>\n";
	}
	?>
	</select></div>
	<?php	
	
	//Sexe	
	$resultat  = $bd->execRequete ("SELECT code_sexe, sexe FROM sexe WHERE $groupe LIKE 'oui' ORDER BY sexe  ");
	while ($sexel = $bd->objetSuivant ($resultat))
    $liste_sexe[$sexel->code_sexe] =$sexel->sexe;
	?>
	<div class="adaptative">
		<label for="sexe">Sexe : </label>
	<select name="sexe" id="sexe">
	<?php
	foreach($liste_sexe as $code => $description) {    
	if ($sexe==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
	else echo "<option value=\"$code\">$description</option>\n";
	}
	?>
	</select></div>
	<?php  
}

//Bloc pour les espèces
function form_espece_simple ($groupe, $sps, $margegauche, $largeur, $bd)
{
	if ($sps=="") $sps="%";

	//Recherche du numéro pour le tampon
	$resultat  = $bd->execRequete ("SELECT * FROM $groupe WHERE CD_REF LIKE '$sps'");
  while ($spsm = $bd->objetSuivant ($resultat))
    $sps_num = $spsm->CD_REF;

	//liste pour les espèces
	$resultat  = $bd->execRequete ("SELECT * FROM $groupe");
  while ($spsl = $bd->objetSuivant ($resultat))
    $liste_sps[$spsl->CD_REF] = "$spsl->NOM_COMPLET_HTML";
   ?>
Espèce :<select name="sps" <?php echo $style;?>>
<?php
if ($sps=="X") echo "<option selected value=\"toutes\">Toutes les espèces du groupe</option>\n";
	else echo "<option value=\"toutes\">Toutes les espèces du groupe</option>\n";
foreach($liste_sps as $code => $description) {    
	if ($sps_num==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select><br>
<?php	
}

function form_espece_test ($sps, $bd)
{
	?>
  <input class="awesomplete" list="items" id="item" style="width:90%" placeholder="Saisissez le nom ou une partie du nom de l'espèce" autocomplete="off"/>
<datalist id="items">
		<?php
	//Sélection des goupes
	$resultat  = $bd->execRequete ("SELECT * FROM classe_ordre WHERE Code_classe_ordre <> 1");
  while ($bo = $bd->objetSuivant ($resultat))
   {	
			//liste pour les espèces
			$resultat2  = $bd->execRequete ("SELECT * FROM $bo->Code_classe_ordre");
			while ($spsl = $bd->objetSuivant ($resultat2))
				{
					?><option value="<?php echo $spsl->NOM_COMPLET;?>" data-xyz = "<?php echo $spsl->CD_REF;?>"><?php echo$spsl->NOM_COMPLET;?></option>
				    <?php

                }
                
	}
    
    //Sélection des goupes
	$resultat  = $bd->execRequete ("SELECT * FROM classe_ordre WHERE Code_classe_ordre <> 1");
  while ($bo = $bd->objetSuivant ($resultat))
   {	
			//liste pour les espèces
			$resultat3  = $bd->execRequete ("SELECT * FROM $bo->Code_classe_ordre GROUP BY NOM_VERN");
			while ($spsl = $bd->objetSuivant ($resultat3))
				{
					?><option value="<?php echo $spsl->NOM_VERN;?>" data-xyz = "<?php echo $spsl->CD_REF;?>"><?php echo$spsl->NOM_VERN;?></option>
				    <?php

                }
                
	}
    
    
    
    
	?>
</datalist>
<?php
}


//Bloc pour les espèces
function form_espece_update ($groupe, $sps, $bd)
{
	//Extraction des deux lettres du groupe
	$resultat  = $bd->execRequete ("SELECT * FROM classe_ordre WHERE Code_classe_ordre LIKE '$groupe'");
  while ($nomgroupe = $bd->objetSuivant ($resultat))
    $deuxlettres = $nomgroupe->Abrevclasseordre;
    
    if ($deuxlettres == substr ( $sps, 0, 2 ))	
	{
	//Recherche du numéro pour le tampon
	$resultat  = $bd->execRequete ("SELECT * FROM $groupe WHERE code_espece LIKE '$sps'");
  while ($spsm = $bd->objetSuivant ($resultat))
    $sps_num = $spsm->numero;
}
	else $sps_num ="";
 		
	//liste pour les espèces
	$resultat  = $bd->execRequete ("SELECT * FROM $groupe");
  while ($spsl = $bd->objetSuivant ($resultat))
    $liste_sps[$spsl->numero] = "$spsl->nom";
   ?>
<select name="sps" onchange='submit()'>
<?php
foreach($liste_sps as $code => $description) {    
	if ($sps_num==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php	
}

//Champ pour les remarques
function form_remarque ($remarques,$bd){
?>
	<br><textarea style="width:880px; height:50px;" name="remarques"><?php echo $remarques;?></textarea> 
	<?php
}

//Champ pour les remarques
function form_remarque_610 ($remarques,$bd){
?>
	<br><textarea style="width:500px; height:50px;" name="remarques"><?php echo $remarques;?></textarea> 
	<?php
}


function form_station_sans_vide ($station, $bd) {
// création liste station
$resultat  = $bd->execRequete ("SELECT code_Insee, nom, numero, lieu_dit FROM station, communes 
WHERE protegee LIKE 'non'
AND code_Insee=left(numero,5)
ORDER BY numero");
  while ($stationl = $bd->objetSuivant ($resultat))
    $liste_station[$stationl->numero] = "$stationl->nom, $stationl->lieu_dit ($stationl->numero)";	
    
    ?>
Station :<select name="station">
<?php
foreach($liste_station as $code => $description) {   
	if ($station==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php   
}


function form_station ($station, $margegauche, $largeur, $bd) {
	
//Création du style
$style=styleduselect($margegauche, $largeur);

// création liste station
$resultat  = $bd->execRequete ("SELECT code_Insee, nom, numero, lieu_dit FROM station, communes 
WHERE protegee LIKE 'non'
AND code_Insee=left(numero,5)
ORDER BY numero");
  while ($stationl = $bd->objetSuivant ($resultat))
    $liste_station[$stationl->numero] = "$stationl->nom, $stationl->lieu_dit ($stationl->numero)";	
    
    ?>
Station :<select name="station" onchange='submit()' id="station"  <?php echo $style;?>">
<?php
	if ($station=="X") echo "<option selected value=\"X\">Aucune station choisie</option>\n";
	else echo "<option value=\"X\">Aucune station choisie</option>\n";
foreach($liste_station as $code => $description) {   
	if ($station==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php   
}
   
function form_station_chiro ($station, $margegauche, $largeur, $bd) {
	
//Création du style
$style=styleduselect($margegauche, $largeur);

// création liste station
$resultat  = $bd->execRequete ("SELECT code_Insee, nom, numero, lieu_dit FROM station, communes 
WHERE code_Insee=left(numero,5)
ORDER BY numero");
  while ($stationl = $bd->objetSuivant ($resultat))
    $liste_station[$stationl->numero] = "$stationl->nom, $stationl->lieu_dit ($stationl->numero)";	
    
    ?>
Station :<select name="station" onchange='submit()' id="station" <?php echo $style;?>">
<?php
	if ($station=="X") echo "<option selected value=\"X\">Aucune station choisie</option>\n";
	else echo "<option value=\"X\">Aucune station choisie</option>\n";
foreach($liste_station as $code => $description) {   
	if ($station==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php   
}   
    
function form_sexe ($sexe,$bd) {
// Création liste codes sexe
$resultat  = $bd->execRequete ("SELECT code_sexe, sexe FROM sexe");
  while ($sexel = $bd->objetSuivant ($resultat))
    $liste_sexe[$sexel->code_sexe] = $sexel->sexe;
    
?>
sexe : <input type="select" name="sexe">
<?php
foreach($liste_sexe as $code => $description) {    
	if ($sexe==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</input>
<?php    
}

function form_communes ($commune, $bd) {

//Communes
$resultat  = $bd->execRequete ("SELECT code_insee, nom FROM communes ORDER BY code_insee");
  while ($commune = $bd->objetSuivant ($resultat))
    $liste_communes[$commune->code_insee] = "$commune->code_insee, $commune->nom";	
?>
Communes :<select name="code_insee" >
<?php
foreach($liste_communes as $code => $description) {    
	if ($commune==$code)   echo "<option selected value=\"$code\">$description</option>\n";         
  else echo "<option value=\"$code\">$description</option>\n";
}
?>
</select>
<?php   
}	

function form_case_det ($determinateur, $obs1, $obs2, $obs3, $origineDonnee, $bd)
{
	
	//Capture du mode 
	if ($determinateur=="CLASSIQUE") $obs="checked";
	else $obs="";
	
	if ($determinateur=="DETERMINATEUR") $det="checked";
	else $det="";

	if ($determinateur=="COLLECTEUR") $col="checked";
	else $col="";
	
	if ($determinateur=="RAPPORTEUR") $rap="checked";
	else $rap="";
	
	// Création de la case à cocher :
	?>
	<input type="radio" name="choix" value="0" <?php echo $obs;?> >
		Vous êtes le déterminateur et le collecteur de cette donnée.
		<br>
		<div id="hidden">
			<?php    
			echo "Observateurs accompagnants<br>";
		
				form_observateur ("", "2", $obs1, $obs2, $bd);
			
					//Vérification de l'existence d'observateur associé.
	$observateurAssocie=observateurAssocie ($obs1, $bd);
	
	//Comptage du nombre d'observateur associé
	$nombreAssocie=count($observateurAssocie);
				
				if ($nombreAssocie>1) form_observateur ("","3", $obs1,  $obs3, $bd);
					
				else echo "<input type='hidden'  name='obs3'  value='1'>";
			?>
		</div>
    
    <input type="radio" name="choix" value="1" <?php echo $det;?> >
		Vous êtes le déterminateur mais <u>pas le collecteur</u> de cette donnée.
		<br>
		<div id="hidden_0">
			<?php
		form_observateur ("Le collecteur est : ","obs_det", $obs1, $obs2, $bd);
			 ?>
			 <br><br>
		</div>
 
     <input type="radio" name="choix" value="2" <?php echo $col;?> >
		Vous êtes le collecteur mais <u>pas le déterminateur</u> de cette donnée.
		<br>
		<div id="hidden_1">
		<?php
	form_observateur ("Le collecteur est : ","obs_coll", $obs1, $obs2, $bd);
	  ?>
	</div>
	
	<input type="radio" name="choix" value="3" <?php echo $rap;?> >
		Vous êtes <u>l'informateur de la donnée</u>, indiquez avec précision l'origine de la donnée.
		<br>
		<div id="hidden_2">
			
	<input type="text" name="origineDonnee" size="50" value="<?php echo $origineDonnee;?>"><br>
		
	</div>
	
<?php
	if ($determinateur=="DETERMINATEUR") 
	{
	?>
<script type="text/javascript">
document.getElementById("hidden").style.display = "none"
document.getElementById("hidden_0").style.display = "block"
document.getElementById("hidden_1").style.display = "none"
document.getElementById("hidden_2").style.display = "none"
</script>
<?php
}
	if ($determinateur=="COLLECTEUR") 
	{
	?>
<script type="text/javascript">
document.getElementById("hidden").style.display = "none"
document.getElementById("hidden_0").style.display = "none"
document.getElementById("hidden_1").style.display = "block"
document.getElementById("hidden_2").style.display = "none"

</script>
<?php
}

if ($determinateur=="RAPPORTEUR") 
	{
	?>
<script type="text/javascript">
document.getElementById("hidden").style.display = "none"
document.getElementById("hidden_0").style.display = "none"
document.getElementById("hidden_1").style.display = "none"
document.getElementById("hidden_2").style.display = "block"

</script>
<?php
}

?>
<!--  //Ajout du script javascript pour cacher le div -->

<script type="text/javascript">
var obs = document.donnees.choix[0];
var det = document.donnees.choix[1];
var col = document.donnees.choix[2];
var rap = document.donnees.choix[3];

obs.onclick = function() {
document.getElementById("hidden").style.display = "block"
document.getElementById("hidden_0").style.display = "none"
document.getElementById("hidden_1").style.display = "none"
document.getElementById("hidden_2").style.display = "none"

};

det.onclick = function() {
document.getElementById("hidden").style.display = "none"
document.getElementById("hidden_0").style.display = "block"
document.getElementById("hidden_1").style.display = "none"
document.getElementById("hidden_2").style.display = "none"

};

col.onclick = function() {
document.getElementById("hidden").style.display = "none"
document.getElementById("hidden_0").style.display = "none"
document.getElementById("hidden_1").style.display = "block"
document.getElementById("hidden_2").style.display = "none"
};

rap.onclick = function() {
document.getElementById("hidden").style.display = "none"
document.getElementById("hidden_0").style.display = "none"
document.getElementById("hidden_1").style.display = "none"
document.getElementById("hidden_2").style.display = "block"
};
</script>
 
<?php
}

function form_case_det_resp ($determinateur, $obs1, $obs2, $obs3, $obs4, $bd)
{
	//transformation des code observateur en nom et prénom
	$obs1=Chercheobservateursaveccode ($obs1, $bd, $format=FORMAT_OBJET);
	$obs2=Chercheobservateursaveccode ($obs2, $bd, $format=FORMAT_OBJET);
	$obs3=Chercheobservateursaveccode ($obs3, $bd, $format=FORMAT_OBJET);

	//Capture du mode 
	if ($determinateur=="CLASSIQUE") 
	{
		//Ecriture des observateurs
	if ($obs2->prenom=="Non précisé") $obs2="";
	else $obs2="accompagné de $obs2->prenom $obs2->nom";
	if ($obs3->prenom=="Non précisé") $obs3="";
	else $obs3="accompagné de $obs3->prenom $obs3->nom";
	echo "L'observateur principal est <u>$obs1->prenom $obs1->nom</u> $obs2 $obs3";
		}
	
	if ($determinateur=="DETERMINATEUR")
	{
	echo "Le déterminateur est <u>$obs1->prenom $obs1->nom</u> Le collecteur est $obs2->prenom $obs2->nom";
		}

	if ($determinateur=="COLLECTEUR")
{
	echo "Le collecteur est <u>$obs1->prenom $obs1->nom</u> Le déterminateur est $obs2->prenom $obs2->nom";
		}
		
		if ($determinateur=="RAPPORTEUR")
{
	echo "Vous êtes l'informateur, l'origine de la donnée est : $obs4";
		}
		
}

function form_case_det_resp_val ($determinateur, $obs1, $obs2, $obs3, $obs4, $bd)
{
	//transformation des code observateur en nom et prénom
	$obs1=Chercheobservateursaveccode ($obs1, $bd, $format=FORMAT_OBJET);
	$obs2=Chercheobservateursaveccode ($obs2, $bd, $format=FORMAT_OBJET);
	$obs3=Chercheobservateursaveccode ($obs3, $bd, $format=FORMAT_OBJET);

	//Capture du mode 
	if ($determinateur=="CLASSIQUE") 
	{
		//Ecriture des observateurs
	if ($obs2->prenom=="Non précisé") $obs2="";
	else $obs2="accompagné de $obs2->prenom $obs2->nom";
	if ($obs3->prenom=="Non précisé") $obs3="";
	else $obs3="accompagné de $obs3->prenom $obs3->nom";
	echo "L'observateur principal est <u>$obs1->prenom $obs1->nom</u> $obs2 $obs3";
		}
	
	if ($determinateur=="DETERMINATEUR")
	{
	echo "Le déterminateur est <u>$obs1->prenom $obs1->nom</u> Le collecteur est $obs2->prenom $obs2->nom";
		}

	if ($determinateur=="COLLECTEUR")
{
	echo "Le collecteur est <u>$obs1->prenom $obs1->nom</u> Le déterminateur est $obs2->prenom $obs2->nom";
		}
		
		if ($determinateur=="RAPPORTEUR")
{
	echo "L'informateur est <u>$obs1->prenom $obs1->nom</u>  l'origine de la donnée est : $obs4";
		}
		
}
 ?>
