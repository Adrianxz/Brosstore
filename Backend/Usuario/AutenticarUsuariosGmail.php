
<?php


session_start();

require_once('BD.php');

require_once('AgregarUsuariosGmail.php');

if(isset($_GET['code']))
{
	$token = $user->fetchAccesTokenWithAuthCode($_GET['code']);
	$user->setAccesToken($token['acces_token']);

	$acces = new Google_Service_Oauth2($user);

	$AK = $acces->userinfo->get();
	
}	


?>