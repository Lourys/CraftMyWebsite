<?php

/*

	AJAX POST admin/page/newsletter.php

*/


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require('include/phpmailer/Exception.php');
require('include/phpmailer/PHPMailer.php');
require('include/phpmailer/SMTP.php');

echo '[DIV]'; // pour séparer les erreurs php des valeurs que l'on veut retourner

if($_Joueur_['rang'] == 1 || $_PGrades_['PermsPanel']['news']['actions']['addNews']) {
	if($_POST['data'] == 0) {
		$_Serveur_['Mail']['Memoire'] = $_POST['Memoire'];
		$_Serveur_['Mail']['last'] = date_timestamp_get(date_create());
		if($_POST['Memoire'] == 1)
		{
			$_Serveur_['Mail']['reply'] = $_POST['reply'];
			$_Serveur_['Mail']['from'] = $_POST['from'];
			
			$_Serveur_['SMTP']['CheckSmtp'] = $_POST['CheckSmtp'];
			if($_POST['CheckSmtp'] == 1)
			{
				$_Serveur_['SMTP']['Host'] = $_POST['host'];
				$_Serveur_['SMTP']['Username'] = $_POST['username'];
				$_Serveur_['SMTP']['Password'] = $_POST['password'];
				$_Serveur_['SMTP']['Port'] = $_POST['port'];
				$_Serveur_['SMTP']['Protocol'] = $_POST['protocol'];
				$_Serveur_['SMTP']['CheckSmtp'] = $_POST['CheckSmtp'];
			}
		}
		$ecriture = new Ecrire('modele/config/config.yml', $_Serveur_);
		echo $_Serveur_['Mail']['last'];
	} else {
		try {
			$mail = new PHPMailer(true);
			if($_POST['CheckSmtp'] == 1)
			{
				$mail->isSMTP();
				$mail->Host = htmlspecialchars($_POST['host']);
				$mail->SMTPAuth = true;
				$mail->Username = htmlspecialchars($_POST['username']);
				$mail->Password = htmlspecialchars($_POST['password']);
				$mail->SMTPSecure = $_POST['protocol'];
				$mail->Port = $_POST['port'];
			}
			else
			{
				$mail->IsMail();
			}
			$mail->From = $_POST["from"];
			$mail->FromName = $_Serveur_['General']['name'];
			$mail->addAddress($_POST['email']);   
			$mail->addReplyTo($_POST["reply"], $_Serveur_['General']['name']);
			$mail->isHTML(true);                               
			$mail->Subject = htmlspecialchars($_POST["sujet"]);
			$mail->Body    = $_POST["contenu"];
			$mail->AltBody = strip_tags($_POST["contenu"]);
			if(!$mail->send()) {
				echo 'Erreur: ' . $mail->ErrorInfo;
			} else {
				echo 'sucess';
			}
		}
		catch (Exception $e)
		{
			echo 'FATAL ERREUR: '.  $e->getMessage();
			if(strpos($e->getMessage(), 'connect() failed')) {
				echo '\r\n Vos logins sont incorectes';
			} else if(strpos( $e->getMessage(), 'instantiate mail function')) {
				echo "\r\n Vous n'avez pas acces à cette fonction sur votre hébergeur, dirigez vous plutôt vers les mails SMTP.";
			}
		}
	}
} else {
	echo 'Permission refusé.';
}
?>
