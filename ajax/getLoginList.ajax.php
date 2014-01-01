<?

	require_once '../common.inc.php';
	
	$loginList = $manager->getLoginList();
	
	echo json_encode($loginList);

?>