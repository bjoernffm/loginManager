<?

	namespace LoginManager;
	use \mysqli;
	
	require_once '../config.inc.php';

	class Base {
		const COOKIE_PATH = '/loginManager/';
		
		public static function getMysqlConnection() {
			global $mysqli;
			
			if (!isset($mysqli) or $mysqli->server_version === null) {
				$mysqli = new mysqli(MYSQLI_HOST, MYSQLI_USER, MYSQLI_PASSWORD, MYSQLI_DATABASE);
				$mysqli->set_charset('utf8');
			}

			return $mysqli;
		}
		
		public static function generateRandomString($length = 10) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			
			$randomString = array();
			for ($i = 0; $i < $length; $i++) {
				$randomString[] = $characters[rand(0, strlen($characters) - 1)];
			}
			
			return implode('', $randomString);
		}
	}

?>