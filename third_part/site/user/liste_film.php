<?php
	print "<html><head><title>Film</title>
	<link rel=\"stylesheet\" href=\"../css/liste.css\">
	</head><body>";
	print "<h1><a href=\"index.php\">Réserve TA Place</a></h1>";
	
	$link = new mysqli("localhost", "Anonyme", "anonyme");
	if($link->connect_errno) {
		    die ("Erreur de connexion : errno: " . $link->errno . " error: "  . $link->error);
	}
	
	$link->select_db('Projet') or die("Erreur de selection de la BD: " . $link->error);
	
	$query = "Select F.*, avg(N.note) as moyenne from Film F, Note N where N.num_film = F.num_film group by F.num_film order by moyenne DESC;";
	$result = $link->query($query) or die("erreur select");
	
	print "
		<h1 name=\"liste\">Liste des Films :</h1>
	";
	print "<div class=\"contenu\">";
	while ($tuple = mysqli_fetch_object($result)){
		print "
				<a href=\"film.php?num_film=$tuple->num_film\">
					<ul class=\"text\">
						<li>$tuple->nom</li>
						<li>Note du film : $tuple->moyenne</li>
					</ul>
				</a>
		";
	}
	print "</div>";
	
	$link->close();
	
	print "</body></html>";
?>
