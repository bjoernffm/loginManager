<?
	use LoginManager\Manager;
	
	require_once '../common.inc.php';
	
	$manager = new Manager($session->getVar('userId'));
	
	if (isset($_GET['search']) and !empty(trim($_GET['search']))) {
		$loginList = $manager->getLoginListByKeywords($_GET['search']);
	} else {
		$loginList = $manager->getLoginList();
	}
	
	echo json_encode($loginList);

?>