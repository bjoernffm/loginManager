<?

	use LoginManager\Manager;
	
	require_once '../common.inc.php';
	
	try {
		$manager = new Manager($session->getVar('userId'));
		
		$manager->editLogin(array(
			'id' => $_GET['id'],
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