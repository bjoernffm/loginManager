<?

	use LoginManager\Session;
	use LoginManager\Manager;

	require_once '../config.inc.php';
	require_once '../includes/Session.class.php';
	require_once '../includes/Manager.class.php';
	
	$session = new Session();
	$session->start();
	
	try {
		$user = Manager::checkForAutologin();
		
		$session->setVar('loggedIn', true);
		$session->setVar('userId', $user['user_id']);
		
		echo json_encode(array(
			'status' => 200
		));
	} catch(Exception $e) {
		echo json_encode(array(
			'status' => (int) $e->getCode(),
			'message' => $e->getMessage()
		));
	}
	
?>