<?

	use LoginManager\Manager;
	
	require_once '../common.inc.php';
	
	try {
		Manager::updateUser(array(
			'id' => $_GET['id'],
			'name' => $_GET['name'],
			'email' => $_GET['email'],
			'password' => $_GET['password']
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