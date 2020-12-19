<?php

	session_start();

	require('src/log.php');

	if (!empty($_POST['email']) && !empty($_POST['password'])) {

		require('src/connect.php');
		
		// VARIABLES
		$email    = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);
		$password = "gt5".sha1($password."659")."54";


		// VERIFICATIONS
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			
			header('location: index.php?error=1&message=L\'adresse email saisie n\'est pas valide.');
			exit();
		}


		// EMAIL EXISTANT
		$req = $db->prepare('SELECT COUNT(*)
							 AS x
							 FROM user
							 WHERE email = ?');

		$req->execute(array($email));

		while ($data = $req->fetch()) {
			
			if ($data['x'] == 0 ) {
				
				header('location: index.php?error=1&message=Impossible de vous authentifier correctement.');
				exit();
			}

		}

		// MOT DE PASSE CORRECT
		$req = $db->prepare('SELECT *
							 FROM user
							 WHERE email = ?');

		$req->execute(array($email));

		while ($data = $req->fetch()) {
			
			if ($password == $data['password'] && $data['bloqued'] == 0) {
				
				$_SESSION['connect'] = 1;
				$_SESSION['email']	 = $data['email'];

				if (isset($_POST['auto'])) {

					setcookie('auth', $data['secret'], time() + 365*24*3600, '/', null, false, true);
				}

				header('location: index.php?success=1');
				exit();
			} else {

				header('location: index.php?error=1&message=Impossible de vous authentifier correctement.');
				exit();
			}
		}

	}


?>



<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="desig/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">

				<?php if (isset($_SESSION['connect'])) { ?>
					
					<h1>Bonjour !</h1>
					<?php 

						if (isset($_GET['success'])) {
							echo '<div class="alert success">Vous êtes maintenant connecté.</div>';
						}
					?>
					<p>Que voulez-vous regarder aujourd'hui?</p>
					<small><a href="logout.php">Déconnection</a></small>

				<?php } else { ?>
						
					<h1>S'identifier</h1>

					<?php 

						if (isset($_GET['error'])) {
							
							if (isset($_GET['message'])) {
								echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
							}

						}

					?>

					<form method="post" action="index.php">
						<input type="email" name="email" placeholder="Votre adresse email" required />
						<input type="password" name="password" placeholder="Mot de passe" required />
						<button type="submit">S'identifier</button>
						<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
					</form>
				

					<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
				<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>