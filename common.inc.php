<?

	use LoginManager\Manager;
	use LoginManager\Session;

	require_once 'includes/Manager.class.php';
	require_once 'includes/Session.class.php';
	
	$session = new Session();
	$session->start();
	
	$session->setVar('userId', 1);
	
	$manager = new Manager($session->getVar('userId'));

?>