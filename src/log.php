<?php

	if (isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) { // PERMET D'ALLEGER LE SERVER. CE CODE NE S'EXECUTE PAS TT LE TEMPS, MAIS UNIQUEMENT QUAND LA SESSION N'EST PAS ACTIVE. SI ON REDEMARRE L'ORDI, ON A LE COOKIE ET PAS LA SESSION -> IL ME CONNECTE. MAIS UNE FOIS QUE J'AI LA SESSION CE CODE NE S'EXECUTERA PLUS.
		
		// VARIABLE
		$secret = htmlspecialchars($_COOKIE['auth']);

		// VERIFICATION
		require('src/connect.php');

		// EXISTE UN ET UN SEUL COMPTE DONT SECRET CORRESPOND A $SECRET ?
		$req = $db->prepare('SELECT COUNT(*)
							 AS x
			    			 FROM user
			    			 WHERE secret = ?');

		$req->execute(array($secret));

		while ($data = $req->fetch()) {
			
			if ($data['x'] == 1) {
				
				// RECUPERE TOUTES SES INFORMATIONS
				$reqUser = $db->prepare('SELECT *
										 FROM user
										 WHERE secret = ?');

				$reqUser->execute(array($secret));

				while ($userAccount = $reqUser->fetch()) {
				
					$_SESSION['connect'] = 1;
					$_SESSION['email']	 = $userAccount['email'];

				}

			}
		}

	}

?>