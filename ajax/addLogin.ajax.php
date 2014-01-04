<?

	require_once '../common.inc.php';
	
	try {
		$manager->addLogin(array(
			'user' => 'user',
			'password' => 'password',
			'location' => 'location',
			'description' => 'description',
			'tags' => 'tags'
		));
		
		echo json_encode(array(
			'status' => 200
		));
	} catch (Exception $e) {
		echo json_encode(array(
			'status' => 500,
			'message' => $e->getMessage()
		));
	}
	
	

?>