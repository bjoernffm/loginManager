<?

	namespace LoginManager;
	use \mysqli;

	class Base {
		public static function getMysqlConnection() {
			global $mysqli;
			
			if (!isset($mysqli) or $mysqli->server_version === null) {
				$mysqli = new mysqli('localhost', 'loginManager', 'aDVhuqhBjYJQ753h', 'test');
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