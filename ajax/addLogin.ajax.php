<?

	require_once '../common.inc.php';
	
	try {
		$manager = new Manager($session->getVar('userId'));
		
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