<?

	define("NO_STOP", true);
	
	require_once '../common.inc.php';
	
	try {
		
		
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