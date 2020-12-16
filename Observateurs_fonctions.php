<?php

//Fonction pour la recherche et l'affichage des observateurs ayant accepté l'association.
function observateursAssocies ($code_obs, $bd)
{
   //déclaration de la variable pour compter les associés
   $i=0; 
   
   //déclarration de la variable pour la première occurence 
   $premiere=1;
    
    //déclaration de la variable pour la liste;
    $liste="";

    $requete=$bd->execRequete ("SELECT * FROM observateurs WHERE code_obs = '$code_obs'");
	while ($bo = $bd->objetSuivant ($requete))
	{
		while ($i<20)
		{
			$i++;
			$association = "association_$i";
			if ($bo->$association==null) $association = "0";
			else
			{
				$association = $bo->$association;
				//Recherche du nom de l'observateur
				$obs = Chercheobservateursaveccode ($association, $bd, $format=FORMAT_OBJET);
				//Ajout du titre car c'est la première occurence
					$premiere=$premiere++;
					if ($premiere==1) 	echo "<br><br><u>Vous avez autorisé les observateurs suivants à vous associer à leurs données :</u><br>";
					echo "<br> $obs->prenom $obs->nom";
			}
		}
	}
}

function observateursAssociesReverse ($code_obs)
{
?>
  	<div class='sous-sous-titre'>Effectuer une demande d'association à un observateur.</div>
  	<form method="post" action="Observateurs_demande.php">
	<input type="hidden"  name="code_obs"  value="<?php echo $code_obs?>">
	<input type="email"  name="email"  placeholder="Saisissez l'adresse" required >
	<input type="submit" name="valider" value="Valider">
	</form>
<?php
}

function observateursStuctureAcceptee ($code_obs,$bd)
{
	//Compteur $i à 0
	$i=0;

	//Recherche de l'existence d'une acceptation en cours
	$res = $bd->execRequete  ("SELECT COUNT(*) AS nombre FROM structure_observateurs WHERE mode='accepte' AND code_observateurs=$code_obs");
		while ($bo = $bd->objetSuivant ($res))
			{
				 if ($bo->nombre>0)
				{
					$res = $bd->execRequete  ("SELECT * FROM structure_observateurs WHERE mode='accepte' AND code_observateurs=$code_obs");
					while ($bl = $bd->objetSuivant ($res))
					{ 
					$i++;
					$resultat = $bd->execRequete  ("SELECT COUNT(*) as nombre FROM donnees, structure WHERE  
					(donnees.obs_1= '$code_obs' 	
					OR donnees.obs_2= '$code_obs' 
					OR donnees.obs_3= '$code_obs') 
					AND donnees.latitude BETWEEN structure.y_latitude AND structure.x_latitude 
					AND donnees.longitude BETWEEN structure.x_longitude AND structure.y_longitude 
					AND structure.code LIKE '$bl->code_structure'");
				 	while ($bi = $bd->objetSuivant ($resultat))
					$tableau_nombre[$i]=$bi->nombre;
				 				 
				 	$resultat = $bd->execRequete  ("SELECT * FROM structure WHERE code LIKE '$bl->code_structure'");
				 	while ($bj = $bd->objetSuivant ($resultat))
						{
						$tableau_nom[$i] = "$bj->nom";
						$tableau_logo[$i]="$bj->fichier_logo";
						$code_structure="$bj->code";
						}
					}
				}	
				 
				?>
				<div class='sous-titre'>Association avec des structures</div>
				<?php
				if ($i==0) echo "<div class='sous-sous-titre'>Aucune structure n'a accès à vos données nouvellement saisies. </div>";
				else echo "<div class='sous-sous-titre'>Structures qui ont actuellement accés à vos données. </div>";
				
				for ($j = 1; $i >=$j; $j++) 
				{
					{
					echo "<div class='flux_donnees_gauche'>";
					echo "<div class='index_structure_logo'>";
					echo "<img src='images/logo/".$tableau_logo[$j]."' width='50'></div>";
					echo "<div class='index_structure_nom'>";
					echo $tableau_nom[$j]. "<br>";
					echo $tableau_nombre[$j]." données sont partagées.<br></div>";
					echo "<div class='index_structure_nom'>";
					echo "<a href='un lien'><img src='images/pdf.jpg' width='50' title='Lire la charte'><br>Lire la charte</a></div>";
				?>
					<div class='index_structure_nom'>
						<form action='Structure_observateurs_acceptation.php' method='post'>
						<input type='hidden' name='code_structure' value=<?php echo $code_structure;?>>
						<input type='submit' name='stop' class='vert' value='Arrêter'>
						</form>
					</div>
					<div class='lisere-clair'></div>
				<?php
					echo "<div class='spacer'></div>";
					echo "</div><br><br>";
					}
				}
			}	 
}

function observateursStuctureStoppee ($code_obs,$bd)
{
	//Compteur $i à 0
	$i=0;

	//Recherche de l'existence d'une acceptation en cours
	$res = $bd->execRequete  ("SELECT COUNT(*) AS nombre FROM structure_observateurs WHERE mode='stop' AND code_observateurs=$code_obs");
		while ($bo = $bd->objetSuivant ($res))
			{
				 if ($bo->nombre>0)
				{
					$res = $bd->execRequete  ("SELECT * FROM structure_observateurs WHERE mode='stop' AND code_observateurs=$code_obs");
					while ($bl = $bd->objetSuivant ($res))
					{ 
					$i++;
					$resultat = $bd->execRequete  ("SELECT COUNT(*) as nombre FROM donnees, structure WHERE  
					(donnees.obs_1= '$code_obs' 	
					OR donnees.obs_2= '$code_obs' 
					OR donnees.obs_3= '$code_obs') 
					AND donnees.latitude BETWEEN structure.y_latitude AND structure.x_latitude 
					AND donnees.longitude BETWEEN structure.x_longitude AND structure.y_longitude 
					AND structure.code LIKE '$bl->code_structure'");
				 	while ($bi = $bd->objetSuivant ($resultat))
					$tableau_nombre[$i]=$bi->nombre;
				 				 
				 	$resultat = $bd->execRequete  ("SELECT * FROM structure WHERE code LIKE '$bl->code_structure'");
				 	while ($bj = $bd->objetSuivant ($resultat))
						{
						$tableau_nom[$i] = "$bj->nom";
						$tableau_logo[$i]="$bj->fichier_logo";
						$code_structure="$bj->code";
						}
					}
				}	
				 
				if ($i==0) echo "<div class='sous-sous-titre'>Aucune structure n'a accès à vos données nouvellement saisies. </div>";
				else echo "<div class='sous-sous-titre'>Structures qui avaient accés à vos données. </div>";
				
				for ($j = 1; $i >=$j; $j++) 
				{
					{
					echo "<div class='flux_donnees_gauche'>";
					echo "<div class='index_structure_logo'>";
					echo "<img src='images/logo/".$tableau_logo[$j]."' width='50'></div>";
					echo "<div class='index_structure_nom'>";
					echo $tableau_nom[$j]. "<br>";
					echo $tableau_nombre[$j]." données ont été partagées.<br></div>";
					echo "<div class='index_structure_nom'>";
					echo "<a href='un lien'><img src='images/pdf.jpg' width='50' title='Lire la charte'><br>Lire la charte</a></div>";
				?>
					<div class='index_structure_nom'>
						<form action='Structure_observateurs_acceptation.php' method='post'>
						<input type='hidden' name='code_structure' value=<?php echo $code_structure;?>>
						<input type='submit' name='reprendre' class='vert' value='Reprendre'>
						</form>
					</div>
					<div class='lisere-clair'></div>
				<?php
					echo "<div class='spacer'></div>";
					echo "</div><br><br>";
					}
				}
			}	 
}
?>
