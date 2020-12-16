<?php
function StructureCoordinateurSup($numero, $code, $mode, $bd)
{
	//Vérification des droits
	$resultat = $bd->execRequete ("SELECT * FROM structure WHERE code='$code'");
while ($bo = $bd->objetSuivant ($resultat))
{
	if  ($mode=='temp') $mode="_temporaire";
	else $mode="";
	
	 $requete  = "DELETE FROM structure_coordinateurs$mode WHERE numero='$numero' AND code_structure='$code'";
	 $resultat2 = $bd->execRequete ($requete);
		}
		}

function AjoutCoordinateur ($code_structure,  $bd)
{
	?>
<form method="post" action="Structure_ajout_coordinateur.php"><br>
<label for="email" class="sous-sous-titre">Ajouter un coordinateur</label><br>
					<input  placeholder="Adresse de courriel"  id="email" type="email" name="email" size="30">
					<input type="hidden"  name="structure"  value="<?php echo $code_structure;?>">
					<br>
					<?php form_groupe_avec_tous ("%", "0px", "50%", $bd); ?>
						<input type="submit" value="Ajouter" ><br><br>
						</form>
						<?php
}

function structure_proposition ($code_obs, $bd)
{
	//Mise à 0 du compteur
	$i=0;
	
	//Initialisation du tableau
		 $tableau = [];
		 
	//Recherche des structures
	$res = $bd->execRequete  ("SELECT * FROM structure");
     while ($bo = $bd->objetSuivant ($res))
     {
		 //Vérification de l'existence d'une acceptation ou d'un refus préalable 
		 $res = $bd->execRequete  ("SELECT COUNT(*) AS nombre  FROM structure_observateurs WHERE code_structure=$bo->code AND code_observateurs=$code_obs");
				while ($bv = $bd->objetSuivant ($res))
			 {
				 if($bv->nombre==0) $mode="nouveau";
				 else $mode=$bv->nombre;
			 }
		if ($mode=="nouveau")
		{
					 $i++;
					 $resultat = $bd->execRequete  ("SELECT COUNT(*) as nombre FROM donnees WHERE  
				(obs_1= '$code_obs' 	
				OR obs_2= '$code_obs' 
				OR obs_3= '$code_obs') 
				AND latitude BETWEEN '$bo->y_latitude' AND '$bo->x_latitude' 
				AND longitude BETWEEN '$bo->x_longitude' AND '$bo->y_longitude'");
				 while ($bi = $bd->objetSuivant ($resultat))
				 {
					 $a="$i-nom";
					$tableau_nom[$i] = "$bo->nom";
					$tableau_nombre[$i]="$bi->nombre";
					$tableau_logo[$i]="$bo->fichier_logo";
					$code_structure="$bo->code";
	 }

}

if ($i<>0)
{
    ?>
    <div class='titre'>Vos données pourraient servir !</div>
Vous voyez apparaître ici le nom des associations qui souhaitent pouvoir accéder à vos données. Vous pouvez accepter de leur offrir dans les conditions qu’ils vous proposent dans la charte. À tout moment, vous pouvez arrêter de partager vos données avec cette association dans l'onglet "mon compte". Par contre, cette association aura toujours accès aux données que vous aviez accepté de partager précédemment.    <br><br>
<?php

    for ($j = 1; $i >=$j; $j++) {{
	echo "<div class='flux_donnees_gauche'>";
		echo "<div class='index_structure_logo'>";
		echo "<img src='images/logo/".$tableau_logo[$j]."' width='50'></div>";
		echo "<div class='index_structure_nom'>";
		echo $tableau_nom[$j]. "<br>";
	    echo $tableau_nombre[$j]." données sont concernées pour le moment.<br></div>";
	    echo "<div class='index_structure_nom'>";
	    echo "<a href='un lien'><img src='images/pdf.jpg' width='50' title='Lire la charte'><br>Lire la charte</a></div>";
	    ?>
	    <div class='index_structure_nom'>
	     <form action='Structure_observateurs_acceptation.php' method='post'>
			 <input type='hidden' name='structure' value=<?php echo $code_structure; ?>>
			 <input type='submit' name='acceptation' class='vert' value='accepter'>
			 <input type='submit' name='refus' class='vert' value='refuser'>
		</form>
		</div>
			 <div class='lisere-clair'></div>
	    <?php
	    echo "<div class='spacer'></div>";
	   
    echo "</div><br><br>";
}}}
}}

?>
