<?

	use LoginManager\Session;

	require_once 'includes/Manager.class.php';
	require_once 'includes/Session.class.php';
	
	$session = new Session();
	$session->start();
	
	if ($session->getVar('loggedIn') != true and NO_STOP != true)
		exit();

?>