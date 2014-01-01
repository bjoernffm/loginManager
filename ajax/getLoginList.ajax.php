<?

	require_once '../common.inc.php';
	
	if (isset($_GET['search']) and !empty(trim($_GET['search']))) {
		$loginList = $manager->getLoginListByKeywords($_GET['search']);
	} else {
		$loginList = $manager->getLoginList();
	}
	
	echo json_encode($loginList);

?>