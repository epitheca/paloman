<?php
function StructureCoordinateurSup($numero, $code, $bd)
{
	//Vérification des droits
	$resultat = $bd->execRequete ("SELECT * FROM structure WHERE code='$code'");
while ($bo = $bd->objetSuivant ($resultat))
{
	 $requete  = "DELETE FROM structure_coordinateurs_temporaire WHERE numero='$numero' AND code_structure='$code'";
	 $resultat2 = $bd->execRequete ($requete);
		}
		}

function AjoutCoordinateur ($code_structure,  $bd)
{
	?>
<form method="post" action="Structure_ajout_coordinateur.php"><br>
<label for="email">Ajouter un coordinateur</label>
					<input  placeholder="Adresse de courriel"  id="email" type="email" name="email" size="30">
					<input type="hidden"  name="structure"  value="<?php echo $code_structure;?>">
					<br>
					<?php form_groupe_avec_tous ("%", "0px", "50%", $bd); ?>
						<input type="submit" value="Ajouter" ><br><br>
						</form>
						<?php
}

?>
