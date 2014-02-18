<?

	use LoginManager\Manager;

	require_once '../common.inc.php';

	try {
		if (!isset($_GET['id']))
			throw new Exception('No valid user id given', 400);
		
		if ($_GET['id'] == 'me')
			$_GET['id'] = $session->getVar('userId');
		
		$_GET['id'] = (int) $_GET['id'];
		if ($_GET['id'] <= 0)
			throw new Exception('No valid user id given', 400);
		
		$user = Manager::getUser($_GET['id']);
		
		echo json_encode(array(
			'status' => 200,
			'user' => $user
		));
	} catch (Exception $e) {
		echo json_encode(array(
			'status' => (int) $e->getCode(),
			'message' => $e->getMessage()
		));
	}

?>