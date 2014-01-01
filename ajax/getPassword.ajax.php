<?

	require_once '../common.inc.php';
	
	if (isset($_GET['id']) and !empty(trim($_GET['id']))) {
		$password = $manager->getPasswordById($_GET['id']);
	} else {
		$password = false;
	}
	
	echo json_encode($password);

?>