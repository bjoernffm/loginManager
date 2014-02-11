<?

	use LoginManager\Manager;

	require_once '../common.inc.php';

	try {
		$manager = new Manager($session->getVar('userId'));
	
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