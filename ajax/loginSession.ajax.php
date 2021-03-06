<?

	use LoginManager\Manager;
	use LoginManager\Session;

	require_once '../config.inc.php';
	require_once '../includes/Manager.class.php';
	require_once '../includes/Session.class.php';
	
	$session = new Session();
	$session->start();
	
	try {
		$user = Manager::checkUserCredentials($_REQUEST['username'], $_REQUEST['password']);
		
		$session->setVar('loggedIn', true);
		$session->setVar('userId', $user['user_id']);

		if ($_REQUEST['autologin'] == "true") {
			$manager = new Manager($user['user_id']);
			$manager->addAutologin();
		}

		echo json_encode(array(
			'status' => 200
		));
	} catch (Exception $e) {
		echo json_encode(array(
			'status' => (int) $e->getCode(),
			'message' => $e->getMessage()
		));
	}
	
?>