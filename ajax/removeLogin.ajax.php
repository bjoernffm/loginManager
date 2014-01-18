<?
	use LoginManager\Manager;
	
	require_once '../common.inc.php';
	
	try {
		$manager = new Manager($session->getVar('userId'));
		
		$manager->removeLogin($_GET['id']);
		
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