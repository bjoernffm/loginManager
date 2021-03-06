<?

	use LoginManager\Manager;
	use LoginManager\Session;

	require_once 'version.inc.php';
	require_once 'config.inc.php';
	require_once 'includes/Session.class.php';
	require_once 'includes/Manager.class.php';
	
	$session = new Session();
	$session->start();

	if ($session->getVar('loggedIn') !== true)
		exit();

?>