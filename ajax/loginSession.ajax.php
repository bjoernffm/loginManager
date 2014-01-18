<?
	use LoginManager\Manager;
	
	define("NO_STOP", true);
	
	require_once '../common.inc.php';
	
	try {
		$user = Manager::checkCredentials($_REQUEST['username'], $_REQUEST['password']);
		
		$session->setVar('loggedIn', true);
		$session->setVar('userId', $user['user_id']);
		
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