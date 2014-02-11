<?

	use LoginManager\Session;

	require_once '../config.inc.php';
	require_once '../includes/Session.class.php';
	
	$session = new Session();
	$session->start();
	
	if ($session->getVar('loggedIn') == true) {
		echo json_encode(array('loggedIn' => true));
	} else {
		echo json_encode(array('loggedIn' => false));
	}
	
?>