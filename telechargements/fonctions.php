<?php

// ------------------------------------------
// Fonctions diverses et variées
// ------------------------------------------

function Connexion ($login, $passe, $base, $serveur)
{
  // define ("SGBD", "PostgreSQL"); // Ou PostgreSQL, ou SQLite
  define ("SGBD", "MySQL"); // Ou PostgreSQL, ou SQLite
  // define ("SGBD", "SQLite"); // Ou PostgreSQL, ou SQLite

  // Instanciation d'un objet instance de BD. 
  // Choix de la sous-classe en fonction de la configuration
  switch (SGBD)
    {
    case "PostgreSQL":
      $bd = new BDPostgreSQL ($login, $passe, $base, $serveur);
      break;
 
    case "SQLite":
      $bd = new BDSQLite ($login, $passe, $base, $serveur);
      break;

    default: // MySQL par défaut
      $bd = new BDMySQL ($login, $passe, $base, $serveur);
      break;
    }
  return $bd;
}
 
 // Recherche d'un groupe en fonction du code de l'éspèce
function ChercheGroupeSp ($espece, $bd, $format=FORMAT_OBJET)
{		
 $requete = $bd->execRequete ("SELECT * FROM classe_ordre");
 //Création d'un tableau pour stocker les noms des taxons
 $taxons=array();
 //Mise à 0 d'un compteur
 $i=0;
 //Exécution de la requête pour stocker le nom des taxons
 while ($bo = $bd->objetSuivant ($requete))
	{ 
     $taxons[]=$bo->Code_classe_ordre;
     $i++;
    }
//Recherche de l'espèce dans les tables du groupe
for	($j=0; $i>$j; $j++)
        {
            $requete="SELECT * FROM $taxons[$j] WHERE CD_NOM = '$espece'";
            $nbr = mysqli_num_rows($bd->execRequete($requete));
            if ($nbr<>0)
                {
                    return $taxons[$j];
                }
        }
}

// Tranformation d'une liste de groupes en nom français du groupe
function ChercheGroupeNomFrancaisSp ($groupe, $bd)
{
    //Création d'une nouvelle liste
    $liste=array();
 
     foreach ($groupe as $value)
{
    $requete=$bd->execRequete ("SELECT * FROM classe_ordre WHERE Code_classe_ordre = '$value'");
	while ($bo = $bd->objetSuivant ($requete))
    $liste[]=$bo->Classe_ordre;
}
return $liste;
}

//Vérification de l'existence d'une donnée
function VerificationExistenceDonnee ($numero,$bd)
{	
	$requete=$bd->execRequete ("SELECT COUNT(*) AS nombre FROM donnees WHERE numero = '$numero'");
	while ($bo = $bd->objetSuivant ($requete))
	$nombre=$bo->nombre;
	
	if ($nombre==0) $existence= "non";
	else $existence= "oui";
			
	return $existence;
	}

// Recherche d'une espèce avec son code
function ChercheSpavecCode ($numero, $bd, $format=FORMAT_OBJET)
{
 //Recherche du groupe de l'espèce
 $groupe=ChercheGroupeSp ($numero,$bd);
	
//Recherche de l'espèce dans la table du groupe		
$resultat  = $bd->execRequete ("SELECT * FROM $groupe WHERE CD_NOM = '$numero'");
  if ($format == FORMAT_OBJET)
    return $bd->objetSuivant ($resultat);
  else
    return $bd->ligneSuivante ($resultat);
}
 
function Cherche_form_spec_obs ($numero, $bd, $format=FORMAT_OBJET)
{
	$formulaire="";
	$texte="";
	$donnees="";
		
	//Recherche de fichiers joints
		$resultat=$bd->execRequete ("SELECT * FROM fichiers WHERE numero_donnee='$numero'");
		if ($bd->nbResultats($resultat) != 0)
		{
			$formulaire.="";
			$texte .='<img src="images/photo.png" alt="chargement en cours" style="width:25px;">';
			$donnees.="Un fichier est attaché<br>Cliquez <a href='Ajout.php?mode=MAJ&numeroobs=$numero'>ici</a> pour voir la fiche";
		}
	
	//Recherche de station spéciale	
		$resultat=$bd->execRequete ("SELECT * FROM donnees WHERE numero='$numero'");
		while ($bv = $bd->objetSuivant ($resultat))
		{
	
	//Recherche du type de données (Classique-observateur_collecteur)	
		if ($bv->type_donnees=="COLLECTEUR") 
		{$texte ="info +";
		
		$donnees="Vous n'êtes que le collecteur de cette donnée";
	}
	if ($bv->type_donnees=="DETERMINATEUR") 
		{$texte ="info +";
		$donnees="Vous n'êtes que le déterminateur de cette donnée";
	}
	if ($bv->type_donnees=="RAPPORTEUR") 
		{$texte ="info +";
		$donnees="Vous n'êtes que le rapporteur de cette donnée";
	}
	
	}
			 return array ($formulaire, $texte, $donnees);
		 }

function Cherche_fichier ($numero, $bd, $format=FORMAT_OBJET)
{
	//Recherche dans la table
		$res=$bd->execRequete ("SELECT * FROM `fichiers` WHERE `numero_donnee` LIKE '$numero'");
  $nbr = $bd->objetSuivant ($res);

  if (!is_object($nbr)) 
    return "X";
  else
  return $nbr->identifiant;  
}

function Compte_fichier ($numero, $bd)
{
	$requete  = "SELECT COUNT(*) AS nombre, numero_donnee  "
    . " FROM fichiers WHERE numero_donnee LIKE '$numero'" ;
  $resultat = $bd->execRequete ($requete);
  $nbr = $bd->objetSuivant ($resultat);

  if (!is_object($nbr)) 
    return 0;
  else
  return $nbr->nombre;  
}

function Cherche_derniere_donnee ($code_obs, $bd)
{
	//Recherche dans la table données
		$res=$bd->execRequete ("SELECT numero, obs_1 FROM donnees WHERE obs_1='$code_obs' ORDER BY numero DESC LIMIT 0,1");
		while ($bo = $bd->objetSuivant ($res)) return $bo->numero; 
}

// Insertion d'un fichier joint
function INSFichier ($numero, $nom, $bd)
{
$ins_fichier = "INSERT INTO fichiers (numero_donnee, identifiant) VALUES ('$numero', '$nom') "; 
$res = $bd->execRequete ($ins_fichier);
 }
 
 // Insertion d'un fichier logo
function INSFichierLogo ($fichier, $code, $bd)
{
$ins_fichier = "UPDATE structure SET fichier_logo='$fichier' WHERE code='$code'"; 
$res = $bd->execRequete ($ins_fichier);
 }

// Insertion d'une donnée avec coordonnées	
function INSDonnees ($date, $longitude, $latitude, $sps, $abo, $info_1, $info_2, $sexe, $corine1, $corine2, $corine3, $corine4, $obs1, $obs2, $obs3, $origineDonnee, $choix, $vent, $cond_clim, $temp, $riviere, $route, $remarques, $bd)
{
//Transformation de la température en NULL et suppression de la virgule
	if ($temp=='') $temp='NULL';
	$temp= str_replace(',', '.', 	$temp);

//Transformation de l'abondance en NULL
	if ($abo=='') $abo='NULL';
	
//Transformation de la date
	$date= datesitetoserver ($date);	
		
//Supression des quotes
		$remarques=addslashes($remarques);
		$origineDonnee=addslashes($origineDonnee);
		$remarques=str_replace(array("\r\n", "\n", "\r"), ' ', $remarques);

//Transformation du choix en type de données
if ($choix=="0") $choixrec = "CLASSIQUE";
if ($choix=="1") $choixrec = "DETERMINATEUR";	
if ($choix=="2") $choixrec = "COLLECTEUR";
if ($choix=="3") $choixrec = "RAPPORTEUR";		
if ($choix=="DETERMINATEUR" || $choix=="COLLECTEUR") 	$obs3="1";
if ($choix=="RAPPORTEUR")
 	{
		$obs3="1";
		$obs2="1";}	
		
		//Conversion des vides en Null
		$origineDonnee= VidetoNull ($origineDonnee);
		if ($info_1=="X") $info_1="" ;
			$info_1=VidetoNull ($info_1);
		if ($info_2=="X") $info_2="" ;
			$info_2=VidetoNull ($info_2);
		if ($sexe=="X") $sexe="" ;
			$sexe=VidetoNull ($sexe);
			
		if ($corine1=="0") $corine1="" ;
			$corine1=VidetoNull ($corine1);
		if ($corine2=="0") $corine2="" ;
			$corine2=VidetoNull ($corine2);
		if ($corine3=="0") $corine3="" ;
			$corine3=VidetoNull ($corine3);
		if ($corine4=="0") $corine4="" ;
			$corine4=VidetoNull ($corine4);
				
		if ($obs2=="1") $obs2="" ;
			$obs2=VidetoNull ($obs2);
			
			if ($obs3=="1") $obs3="" ;
			$obs3=VidetoNull ($obs3);
			
			if ($vent=="X") $vent="" ;
			$vent=VidetoNull ($vent);
			
			if ($cond_clim=="X") $cond_clim="" ;
			$cond_clim=VidetoNull ($cond_clim);
		
		if ($riviere=="X") $riviere="" ;
			$riviere=VidetoNull ($riviere);
			
			if ($route=="X") $route="" ;
			$route=VidetoNull ($route);
			
			$remarques=VidetoNull ($remarques);
			
$ins_donnees = "INSERT INTO donnees (date, longitude, latitude, espece, abondance, info_1, info_2, sexe, corine_1, corine_2, corine_3, corine_4, obs_1, obs_2, obs_3, origineDonnee, type_donnees, vent, meteo, temperature,  riviere, route, remarques) "
      . "VALUES ('$date','$longitude', '$latitude', '$sps', $abo, $info_1, $info_2, $sexe, $corine1, $corine2, $corine3, $corine4, '$obs1', $obs2, $obs3, $origineDonnee, '$choixrec', $vent, $cond_clim, $temp, $riviere, $route, $remarques) "; 

$res = $bd->execRequete ($ins_donnees);
}

function VidetoNull ($variable)
{
		if ($variable=="") $variable="NULL";
						else $variable="'$variable'";
						
						return $variable;
}
   
//Compte du nombre de données totale par observateur et taxons
function Calcdontotal ($obs, $abrev, $bd)
{
if ($obs=="tous")
{
$requete  = "SELECT COUNT(*) AS nombre, espece  "
    . " FROM donnees WHERE espece LIKE '$abrev%'" ;
  $resultat = $bd->execRequete ($requete);
  $nbr = $bd->objetSuivant ($resultat);

  if (!is_object($nbr)) 
    return 0;
  else
    return $nbr->nombre; 
}    
else
{  	
$requete  = "SELECT COUNT(*) AS nombre "
    . " FROM donnees WHERE espece LIKE '$abrev%' AND obs_1='$obs' OR obs_2='$obs'OR obs_3='$obs'" ;
  $resultat = $bd->execRequete ($requete);
  $nbr = $bd->objetSuivant ($resultat);

  if (!is_object($nbr)) 
    return 0;
  else
    return $nbr->nombre; 
 }}
   
//Controle des droits d'administration pour une personne
function administration ($code_obs, $bd)
{
$requete_2="SELECT code_obs FROM observateurs WHERE administrateur LIKE 'oui' AND code_obs='$code_obs'";
	$resultat_2 = $bd->execRequete ($requete_2);
	$num_rows = $resultat_2->num_rows;
	if ($num_rows != 0) $administrateur="oui";
	else $administrateur="non";
	
	return $administrateur;
}

 // Mise à jour d'une donnée
function MAJDonnees ($date, $longitude, $latitude, $sps, $abo, $info_1, $info_2, $sexe, $corine1, $corine2, $corine3, $corine4, $obs1, $obs2, $obs3, $origineDonnee, $choix, $vent, $cond_clim, $temp, $riviere, $route, $remarques, $numero, $bd)
{
		
//Transformation de la température en NULL et suppression de la virgule
	if ($temp=='') $temp='NULL';
	$temp= str_replace(',', '.', 	$temp);

//Transformation de l'abondance en NULL
	if ($abo=='') $abo='NULL';

		//Conversion des vides en Null
		$origineDonnee= VidetoNull ($origineDonnee);
		if ($info_1=="X") $info_1="" ;
			$info_1=VidetoNull ($info_1);
		if ($info_2=="X") $info_2="" ;
			$info_2=VidetoNull ($info_2);
		if ($sexe=="X") $sexe="" ;
			$sexe=VidetoNull ($sexe);
			
		if ($corine1=="X") $corine1="" ;
			$corine1=VidetoNull ($corine1);
		if ($corine2=="X") $corine2="" ;
			$corine2=VidetoNull ($corine2);
		if ($corine3=="X") $corine3="" ;
			$corine3=VidetoNull ($corine3);
		if ($corine4=="X") $corine4="" ;
			$corine4=VidetoNull ($corine4);
		
		if ($obs2=="1") $obs2="" ;
			$obs2=VidetoNull ($obs2);
			
			if ($obs3=="1") $obs3="" ;
			$obs3=VidetoNull ($obs3);
			
			if ($vent=="X") $vent="" ;
			$vent=VidetoNull ($vent);
			
			if ($cond_clim=="X") $cond_clim="" ;
			$cond_clim=VidetoNull ($cond_clim);
		
		if ($riviere=="X") $riviere="" ;
			$riviere=VidetoNull ($riviere);
			
			if ($route=="X") $route="" ;
			$route=VidetoNull ($route);
			
			$remarques=VidetoNull ($remarques);

//Supression des quotes
		$remarques=addslashes($remarques);
		
		//Transformation du choix en type de données
if ($choix=="0") $choixrec = "CLASSIQUE";
if ($choix=="1") $choixrec = "DETERMINATEUR";	
if ($choix=="2") $choixrec = "COLLECTEUR";		
if ($choix=="3") $choixrec = "RAPPORTEUR";		
if ($choix=="DETERMINATEUR" || $choix=="COLLECTEUR") 	$obs3="1";
if ($choix=="RAPPORTEUR")
 	{
		$obs3="1";
		$obs2="1";}	
$majdonnees = "UPDATE donnees SET date='$date', longitude='$longitude', latitude='$latitude',espece='$sps', abondance=$abo, info_1=$info_1, info_2=$info_2, sexe=$sexe, corine_1=$corine1,  corine_2=$corine2, corine_3=$corine3, corine_4=$corine4,obs_1=$obs1, obs_2=$obs2, obs_3=$obs3, origineDonnee=$origineDonnee, type_donnees='$choixrec', vent=$vent, meteo=$cond_clim, temperature=$temp, riviere=$riviere, route=$route, remarques=$remarques  WHERE numero='$numero'";

$res = $bd->execRequete ($majdonnees); 
}

// Recherche d'un observateurs avec son email
function Chercheobservateurs ($email, $bd, $format=FORMAT_OBJET) 
{
  $res = $bd->execRequete ("SELECT * FROM observateurs WHERE email = '$email'");
  if ($format == FORMAT_OBJET)
    return $bd->objetSuivant ($res);
  else
    return $bd->ligneSuivante ($res);    
}

//Controle des accès aux modifications
function Controle_droit_donnee ($numero, $code_obs, $bd) {
	
	//Recherche de la donnée
$requete ="select * FROM donnees WHERE numero =$numero";
$resultat = $bd->execRequete ($requete); 
 while ($bo = $bd->objetSuivant ($resultat))
{
// L'observateur a-t'il le droit compléter cette fiche ?
if ($bo->obs_1 <> $code_obs && $bo->obs_2 <> $code_obs && $bo->obs_3 <> $code_obs) return "non";
else return "oui";
}
}

// Recherche d'un observateurs avec son code
function Chercheobservateursaveccode ($code, $bd, $format=FORMAT_OBJET) 
{
  $res = $bd->execRequete ("SELECT * FROM observateurs WHERE code_obs = '$code'");
  if ($format == FORMAT_OBJET)
    return $bd->objetSuivant ($res);
  else
    return $bd->ligneSuivante ($res);    
}

// Recherche du nom d'un code corine
function Cherchecorine ($code, $bd, $format=FORMAT_OBJET)
{
$res = $bd->execRequete 
     ("SELECT * FROM corine WHERE CD_HAB='$code'");
  if ($format == FORMAT_OBJET)
    return $bd->objetSuivant ($res);
  else
    return $bd->ligneSuivante ($res);   	
}

// Recherche du nom d'une structure
function ChercheStructure ($code, $bd)
{
$res = $bd->execRequete 
     ("SELECT * FROM structure WHERE code_responsable='$code'");
    return $bd->objetSuivant ($res);
}

// Recherche du nom d'une structure
function ChercheCoordination ($code, $bd)
{
$res = $bd->execRequete 
     ("SELECT * FROM structure_coordinateurs WHERE code_observateurs='$code'");
    return $bd->objetSuivant ($res);
}	

//Création d'un mot de passe
function password()
{
	//Génération d'un code aléatoire
	// Initialisation des caractères utilisables
    $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
	$i=0;
	$password="";
	
    for($i=0;$i<50;$i++)
    {
        $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
    }
    return $password;
}
//Fonction pour la recherche d'observateurs associés
function observateurAssocie ($obsPrincipal, $bd)
{
	//Recherche de l'existence d'observateur associé
		$resultat  = $bd->execRequete ("SELECT * FROM observateurs WHERE code_obs LIKE '$obsPrincipal'");
		$i=0;
		$liste=array();
		
  while ($bo = $bd->objetSuivant ($resultat))
   {
	  //récupération des observateurs existants
	   for ($j=1; $j<=20;$j++)
		{
			$colonne= "association_$j";
			if (!empty($bo->$colonne))  
			{
				$i++;
			$liste[$i]= $bo->$colonne;
		}
	}
	return $liste;
}}

// Fonction de contrôle avant insertion/MAJ dans observateurs
function ControleObs ($tableau)
{
  $message = "";

// On vérifie que les champs importants ont été saisis
   if ($tableau['email']=="") 
    $message = "Vous devez saisir une adresse de courriel.\\n";
  else if (!filter_var ($tableau['email'], FILTER_VALIDATE_EMAIL))
    $message = "Votre email doit être de la forme xxx@yyy[.zzz].\\n";
    
  if (isSet ($tableau['mot_de_passe']))
    {
      if ($tableau['mot_de_passe']=="" 
	  or $tableau['conf_passe']=="" 
	  or $tableau['conf_passe'] != $tableau['mot_de_passe'])
	$message .= "Vous devez saisir un mot de passe et le confirmer "
	  . " à l'identique.\\n";
    }
    
    if (isSet ($tableau['nouveau_mot_de_passe']))
    {
      if ($tableau['conf_passe'] != $tableau['nouveau_mot_de_passe'])
      $message .= "Si vous saisisez un nouveau mot de passe vous devez le confirmer "
	  . " à l'identique.\\n";
     
    }
	
     if ($tableau['prenom']=="") 
    $message .= "Vous devez saisir un prénom.\\n";
  if ($tableau['nom']=="") 
    $message .= "Vous devez saisir un nom.\\n";

  return $message;
}

//Fonction de controle des données 
 function ControleFiltre ($tableau)
{
  $message = "";

// On vérifie que les champs minimaux ont été saisis		
	if ($tableau['datefrmin']=="") 
    $message = "Vous devez saisir une date.\\n";

if ($tableau['datefrmax']=="") 
    $message = "Vous devez saisir une date.\\n";

	
// Transformation des virgules en points
		$tableau['longitude_X'] = str_replace(',', '.', $tableau['longitude_X']);
		$tableau['latitude_X'] = str_replace(',', '.', $tableau['latitude_X']);
		$tableau['longitude_Y'] = str_replace(',', '.', $tableau['longitude_Y']);
		$tableau['latitude_Y'] = str_replace(',', '.', $tableau['latitude_Y']);
		
	if ($tableau['longitude_X']=="" )
		$message .= "Vous devez saisir la longitude X.\\n";
		else
		{
	if (is_numeric($tableau['longitude_X']))
		{
				if ($tableau['longitude_X']<-6.458496) 
   				$message .= "La longitude est trop à l'ouest. Elle doit être comprise entre -2.45 et -0.52\\n";
   				if ($tableau['longitude_X']>11.074219) 
   				$message .= "La longitude est trop à l'est. Elle doit être comprise entre -2.45 et -0.52\\n";
		}
		else $message .= "La longitude X doit être un nombre.\\n";
		}
		
	if ($tableau['latitude_X']=="" )
		$message .= "Vous devez saisir la latitude X.\\n";
		else
		{
	if (is_numeric($tableau['latitude_X']))
		{
		if ($tableau['latitude_X']<40.520481) 
   		$message .= "La latitude X est trop au sud. Elle doit être comprise entre 46,24 et 47,1\\n";
   		if ($tableau['latitude_X']>51.237159) 
   		$message .= "La latitude X est trop au nord. Elle doit être comprise entre 46,24 et 47,1\\n"; 
		}
		else $message .= "La latitude X doit être un nombre.\\n";
		}
		
		if ($tableau['longitude_X']=="" )
		$message .= "Vous devez saisir la longitude X.\\n";
		else
		{
	if (is_numeric($tableau['longitude_Y']))
		{
				if ($tableau['longitude_Y']<-6.458496) 
   				$message .= "La longitude Y est trop à l'ouest. Elle doit être comprise entre -2.45 et -0.52\\n";
   				if ($tableau['longitude_Y']>11.074219) 
   				$message .= "La longitude Y est trop à l'est. Elle doit être comprise entre -2.45 et -0.52\\n";
		}
		else $message .= "La longitude Y doit être un nombre.\\n";
		}
		
	if ($tableau['latitude_Y']=="" )
		$message .= "Vous devez saisir la latitude Y.\\n";
		else
		{
	if (is_numeric($tableau['latitude_Y']))
		{
		if ($tableau['latitude_Y']<40.520481) 
   		$message .= "La latitude Y est trop au sud. Elle doit être comprise entre 46,24 et 47,1\\n";
   		if ($tableau['latitude_Y']>51.237159) 
   		$message .= "La latitude Y est trop au nord. Elle doit être comprise entre 46,24 et 47,1\\n"; 
		}
		else $message .= "La latitude Y doit être un nombre.\\n";
		}
				
				echo $tableau['datefrmin'];
//Vérification de la date
if ($tableau['datefrmin']<>"")
{
	$aujourdhui= date("Y-m-d"); 
	if ($tableau['datefrmin']>$aujourdhui)
    $message .= "La date minimale est incorrecte (elle indique une date future).\\n";
	
	list($annee, $mois, $jour) = explode('-',$tableau['datefrmin']);
	if(!checkdate($mois,$jour,$annee))	
      $message .= "La date minimale est non valide.\\n";
}

if ($tableau['datefrmax']<>"")
{
	$aujourdhui= date("Y-m-d"); 
	if ($tableau['datefrmax']>$aujourdhui)
    $message .= "La date maximale est incorrecte (elle indique une date future).\\n";
	
	list($annee, $mois, $jour) = explode('-',$tableau['datefrmin']);
	if(!checkdate($mois,$jour,$annee))	
      $message .= "La date maximale est non valide.\\n";
}		
  return $message; 
  }
  
//Fonction de controle des données 
 function ControleDonnees ($tableau)
{
  $message = "";

// On vérifie que les champs minimaux ont été saisis
		
	if ($tableau['date']=="") 
    $message = "Vous devez saisir une date.\\n";

// Transformation des virgules en points
		$tableau['longitude'] = str_replace(',', '.', $tableau['longitude']);
		$tableau['latitude'] = str_replace(',', '.', $tableau['latitude']);
		
	if ($tableau['longitude']=="" )
		$message .= "Vous devez saisir la longitude.\\n";
		else
		{
	if (is_numeric($tableau['longitude']))
		{
				if ($tableau['longitude']<-6.458496) 
   				$message .= "La longitude est trop à l'ouest. Elle doit être comprise entre -2.45 et -0.52\\n";
   				if ($tableau['longitude']>11.074219) 
   				$message .= "La longitude est trop à l'est. Elle doit être comprise entre -2.45 et -0.52\\n";
		}
		else $message .= "La longitude doit être un nombre.\\n";
		}
		
	if ($tableau['latitude']=="" )
		$message .= "Vous devez saisir la latitude.\\n";
		else
		{
	if (is_numeric($tableau['latitude']))
		{
		if ($tableau['latitude']<40.520481) 
   		$message .= "La latitude est trop au sud. Elle doit être comprise entre 46,24 et 47,1\\n";
   		if ($tableau['latitude']>51.237159) 
   		$message .= "La latitude est trop au nord. Elle doit être comprise entre 46,24 et 47,1\\n"; 
		}
		else $message .= "La latitude doit être un nombre.\\n";
		}
		
		//Capture de la remarque
		$remarques=$tableau['remarques'];
		
	if (preg_match('$;$',$remarques ))
	$message .= "Les remarques ne doivent pas contenir de point virgule (;)\\n";
	if (preg_match('$;$', $tableau['origineDonnee']))
	$message .= "L'origine de la donnée ne doit pas contenir de point virgule (;)\\n";
	
		
//Vérification de la date
if ($tableau['date']<>"")
{
	$aujourdhui= date("Y-m-d"); 
	if ($tableau['date']>$aujourdhui)
    $message .= "La date est incorrecte (elle indique une date future).\\n";
}
	

//Vérification de la température
	$tableau['temp']= str_replace(',', '.', 	$tableau['temp']);
	if ($tableau['temp']<>"")
	{
	if (is_numeric($tableau['temp']))
		{
	if ($tableau['temp']>45 )
		$message .= "La température est anormalement haute.\\n";
	if ($tableau['temp']<-20 )
		$message .= "La température est anormalement basse.\\n";
	}
	else $message .= "La température doit être un nombre.\\n";
}

//Vérification des données chiro
if (isset($_POST['chiro']))
{
//Controle de l'activité sexuelle	
	if ($tableau['sexe'] =="F" && preg_match("/^G/",$tableau['act_sexuelle'])) $message .="Une femelle ne peut avoir activité sexuelle de type G\\n";
	if ($tableau['sexe'] =="M" && $tableau['act_sexuelle']=="lactante") $message .="Un mâle ne peut avoir activité sexuelle lactante\\n";
	if ($tableau['sexe'] =="M" && $tableau['act_sexuelle']=="post-lactante") $message .="Un mâle ne peut avoir activité sexuelle post-lactante\\n";

	$tableau['AB']= str_replace(',', '.', 	$tableau['AB']);
	$tableau['D3']= str_replace(',', '.', 	$tableau['D3']);
	$tableau['D5']= str_replace(',', '.', 	$tableau['D5']);
	$tableau['lg_tibia']= str_replace(',', '.', 	$tableau['lg_tibia']);	
	$tableau['lg_pouce']= str_replace(',', '.', 	$tableau['lg_pouce']);
	$tableau['larg_tragus']= str_replace(',', '.', 	$tableau['larg_tragus']);
	$tableau['poids']= str_replace(',', '.', 	$tableau['poids']);
	$tableau['lg_oreille']= str_replace(',', '.', 	$tableau['lg_oreille']);

	  
	if ($tableau['AB']<>"")
		{ 	
	if (is_numeric($tableau['AB']))
		{
		if ($tableau['AB']<10.1) 
   		$message .= "AB parait être trop faible\\n";
   		if ($tableau['AB']>80) 
   		$message .= "AB parait être trop élevé\\n"; 
		}
		else $message .= "AB doit être un nombre\\n";
	}
	if ($tableau['D3']<>"")
		{ 	
	if (is_numeric($tableau['D3']))
		{
		if ($tableau['D3']<10.1) 
   		$message .= "D3 parait être trop faible\\n";
   		if ($tableau['D3']>130) 
   		$message .= "D3 parait être trop élevé\\n"; 
		}
		else $message .= "D3 doit être un nombre\\n";
	}	
	if ($tableau['D5']<>"")
		{ 	
	if (is_numeric($tableau['D5']))
		{
		if ($tableau['D5']<10.1) 
   		$message .= "D5 parait être trop faible\\n";
   		if ($tableau['D5']>90) 
   		$message .= "D5 parait être trop élevé\\n"; 
		}
		else $message .= "D5 doit être un nombre\\n";
	}
	if ($tableau['lg_tibia']<>"")
		{ 	
	if (is_numeric($tableau['lg_tibia']))
		{
   		if ($tableau['lg_tibia']>50) 
   		$message .= "La longueur du tibia parait être trop élevé\\n"; 
		}
		else $message .= "La longueur du tibia doit être un nombre\\n";
	}
	if ($tableau['lg_oreille']<>"")
		{ 	
	if (is_numeric($tableau['lg_oreille']))
		{
   		if ($tableau['lg_oreille']>60) 
   		$message .= "La longueur de l'oreille parait être trop élevé\\n"; 
		}
		else $message .= "La longueur de l'oreille doit être un nombre\\n";
	}
	if ($tableau['lg_pouce']<>"")
		{ 	
	if (is_numeric($tableau['lg_pouce']))
		{
		if ($tableau['lg_pouce']>20) 
   		$message .= "La longueur du pouce parait être trop élevé\\n"; 
		}
		else $message .= "La longueur du pouce doit être un nombre\\n";
	}
	if ($tableau['larg_tragus']<>"")
		{ 	
	if (is_numeric($tableau['larg_tragus']))
		{
   		if ($tableau['larg_tragus']>10) 
   		$message .= "La largeur du tragus parait être trop élevé\\n"; 
		}
		else $message .= "La largeur du tragus doit être un nombre\\n";
	}
	if ($tableau['poids']<>"")
		{ 	
	if (is_numeric($tableau['poids']))
		{
		if ($tableau['poids']<1) 
   		$message .= "Le poids parait être trop faible\\n";
   		if ($tableau['poids']>60) 
   		$message .= "Le poids parait être trop élevé\\n"; 
		}
		else $message .= "Le poids doit être un nombre\\n";
		
	
}
		if ($tableau['cause_mort']=="Autres (remarques)" && $remarques=="" && $tableau['station']=="X") $message .="La saisi de la remarque est obligatoire";

}

return $message; 
  }

function datesitetoserver($date)
{	
        $date = explode('/',$date);
        $date = array_reverse($date);
        $date = implode('-',$date);
        return "$date";
    }
    
function dateservertosite($date)
{
return strftime('%d-%m-%Y',strtotime($date));
}

function styleduselect ($margegauche, $largeur)
{
	//Création de la liste du style
	//Mise à 0 de la valeur $style
	$style="";

	//L'une des deux variable n'est pas nulle
	if ($margegauche<>"A"||$largeur<>"A")
	{
	$style.="style='";
	
	
	//La marge droite n'est pas nulle
	if ($margegauche<>"A")
	{
	$style .="margin-left:$margegauche";
	}
	
	if ($margegauche<>"A"&&$largeur<>"A")
	{
	$style .=";";
	}
	
	//La largeur n'est pas nulle
	if ($largeur<>"A")
	{
	$style .="width:$largeur";
	}
	
	$style.="'";	
	}
	return $style;
	}

function gestion_session ($type,$prenom,$nom)
{
	$deco= ancre_renomme (CHEMIN_URL."Deconnexion.php","Déconnexion");	
	$compte= ancre_renomme (CHEMIN_URL."ObservateursMAJ.php","Gérer mon compte");
	?>
	
	<div class="pied-gauche">
	<?php
	echo "$compte";
	?>
	</div>
	
	<div class="pied-droit">
	<?php
	echo "$deco";
	?>
	</div>
	<?php
}

// Fonction taille du fichier
function taille($fichier)
{
global $size_unit;
// Lecture de la taille du fichier
$taille = filesize($fichier);
// Conversion en Go, Mo, Ko
if ($taille >= 1073741824)
{ $taille = round($taille / 1073741824 * 100) / 100 . " Go"; }
elseif ($taille >= 1048576)
{ $taille = round($taille / 1048576 * 100) / 100 . " Mo"; }
elseif ($taille >= 1024)
{ $taille = round($taille / 1024 * 100) / 100 . " Ko"; }
else
{ $taille = $taille . " o"; }
if($taille==0) {$taille="Aucune donnée";}
return $taille;
}

function progression($indice)
{
	$indice = round($indice, 0);
	echo "<script>";
		echo "document.getElementById('pourcentage').innerHTML='$indice%';";
		echo "document.getElementById('barre').style.width='$indice%';";
	echo "</script>";
	ob_flush();
	flush();
	ob_flush();
	flush();
}

//Fonction pour les fenêtre modal
function fenetre_modal ($titre, $message)
{

include "fenetre-modal.html";
			?>
				<script type="text/javascript">
						
				function twAlert(sTitre,sTexte) {
				document.getElementById("oTitre").innerHTML = sTitre;
				document.getElementById("oTexte").innerHTML = sTexte;
				location.href = "#oModal";
				}

				twAlert("<?php echo $titre;?>","<?php echo $message;?>");
				</script>
				<?php
			}

?>
