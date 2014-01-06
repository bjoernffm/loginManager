<?

	require_once '../common.inc.php';
	
	try {
		if (!isset($_GET['user']) or empty(trim($_GET['user'])))
			throw new Exception('User field required.', 400);
		
		if (!isset($_GET['password']) or empty(trim($_GET['password'])))
			throw new Exception('Password field required.', 400);
		
		$manager->addLogin(array(
			'user' => $_GET['user'],
			'password' => $_GET['password'],
			'location' => $_GET['location'],
			'description' => $_GET['description'],
			'tags' => $_GET['tags']
		));
		
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