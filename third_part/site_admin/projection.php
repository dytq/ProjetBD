<html>
	<head>
		<title>Projection</title>
	</head>	
	<body>
		<h2>Recherche :</h2>
		<form method="POST" action="projection.php">
			<table>
				<tr>
				<td>Numero :</td>
				<td><input type="text" name="numero"></td>
				</tr>
				<tr>
				<td>Jour :</td>
				<td><input type="date" name="jour" placeholder="yyyy-mm-dd"></td>
				</tr>
				<tr>
				<td>Heure :</td>
				<td><input type="date" name="heure"placeholder="hh:mm:ss"></td>
				</tr>
				<tr>
				<td>Version :</td>
				<td><input type="radio" name="version" value="all">all
				    <input type="radio" name="version" value="vf" >vf
				    <input type="radio" name="version" value="vo" >vo</td>
				</tr>	
				<tr>
				<td>Nom du film :</td>
				<td><input type="text" name="film"></td>
				</tr>		
				<tr>
				<td>Numéro de salle :</td>
				<td><input type="text" name="salle"></td>
				</tr>		
				<tr>
				<td>Nom du cinéma :</td>
				<td><input type="text" name="cinema"></td>
				</tr>		
			</table>
			<input type="submit" value="rechercher">
			<input type="reset" value="annuler">
		</form>
		<?php
			session_start();
			if(!isset($_SESSION['admin']))  {
				header("Location: se_connecter.php");
				exit();
			}
			
			$link = new mysqli("localhost", "Admin", "admin");
			if($link->connect_errno) {
				die ("erreur connection");
			}
			$link->select_db('Projet') or die("Erreur de selection de la BD: " . $link->error);
			
			/* SURTOUT NE PAS EFFACER */
			/* C'EST LA RECHERCHE */
			$array = array();
			$have = 0;
			
			if(isset($_POST['numero']) and is_numeric($_POST['numero'])) {
				$numero = $_POST['numero'];
				$array[] = "s.num_se_joue = $numero";
				$have++;
			}
			if(isset($_POST['jour'])) {
				$jour = $_POST['jour'];
				$array[] = "s.jour like \"%$jour%\"";
				$have++;
			}
			if(isset($_POST['heure'])) {
				$heure = $_POST['heure'];
				$array[] = "s.heure like \"%$heure%\"";
				$have++;
			}	
			if(isset($_POST['version'])) {
				$version = $_POST['version'];
				$array[] = "s.version = \"$version\" ";
				$have++;
			}
			if(isset($_POST['film'])) {
				$film = $_POST['film'];
				$array[] = "s.num_film NOT IN (SELECT f.num_film FROM Film f WHERE f.nom like \"%$film%\")";
				$have++;
			}
			if(isset($_POST['salle']) and is_numeric($_POST['salle'])) {
				$salle = $_POST['salle'];
				$array[] = "s.num_salle = $salle";
				$have++;
			}
			if(isset($_POST['cinema'])) {
				$cinema = $_POST['cinema'];
				$array[] = "s.nom_du_cinema like \"%$cinema%\"";
				$have++;
			}
			
			if($have == 0) {
				$query = "SELECT * FROM Se_joue_dans;";
			}
			else {
				$query = "SELECT * FROM Se_joue_dans s WHERE ";
				$query = $query . " " . $array[$have-1];
				$have--;
				while($have > 0) {
					$query = $query . " and " . $array[$have-1];
					$have--;
				}
				$query = $query . " ;" ;
			}
			print "$query";
			/* SURTUOT NE PAS EFFACER */
			
			$result = $link->query($query) or die("erreur select");
			
			print "<h2>Résultat :</h2>";
			
			//~ if(isset($_GET['modif'])) {
				//~ $ancien_nom = $_GET['modif'];
				//~ print "erreur modification du cinema $ancien_nom";
			//~ }
			
			print "<table border><tr><th>ID</th><th>Date</th><th>Heure</th><th>Version</th><th>Nom du film</th><th>Numéro de Salle</th><th>Nom du Cinéma</th></tr>";
			$nb_res = 0;
			while($tuple = mysqli_fetch_object($result)) {
				$nb_res++;
				print "
					<tr>
					<form method=\"POST\" >
						<td><input type=\"text\" value=\"$tuple->num_se_joue\" name=\"nom\" placeholder=\"3 - 30 caractères\" readonly></td>
						<td><input type=\"text\" value=\"$tuple->jour\" name=\"compagnie\" placeholder=\"3 - 30 caractères\"></td>
						<td><input type=\"text\" value=\"$tuple->heure\" name=\"ville\"  placeholder=\"3 - 30 caractères\"></td>
						<td><input type=\"text\" value=\"$tuple->version\" size=\"5\"></td>
						<td><input type=\"text\" value=\"$tuple->num_film\" size=\"5\" </td>
						<td><input type=\"text\" value=\"$tuple->num_salle\" size=\"5\" ></td>
						<td><input type=\"text\" value=\"$tuple->nom_du_cinema\" size=\"5\" ></td>
						<td><input type=\"submit\" value=\"modifier\"></td>
					</form>
					<form method=\"POST\" action=\"supprimer_cinema.php\">
						<td><input type=\"text\" value=\"$tuple->nom\" name=\"nom\" hidden>
						<input type=\"submit\" value=\"supprimer\"></td>
					</form>
					</tr>
				";
			}
			print "</table>";
			if($nb_res == 0) {
				print "<h3>Aucun Résultat</h3>";
			}
		?>
	</body>
</html>
