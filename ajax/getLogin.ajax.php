<?

	require_once '../common.inc.php';
	
	
	try {
		if (!isset($_GET['id']) or (0 >= (int) $_GET['id']))
			throw new Exception('No valid record id.', 400);
		
		$login = $manager->getLogin($_GET['id']);
		
		echo json_encode(array(
			'status' => 200,
			'login' => $login
		));
	} catch (Exception $e) {
		echo json_encode(array(
			'status' => (int) $e->getCode(),
			'message' => $e->getMessage()
		));
	}

?>