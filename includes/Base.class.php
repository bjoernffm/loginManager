<?

	namespace LoginManager;
	use \mysqli;

	class Base {
		const COOKIE_PATH = '/loginManager/';
		
		public static function getMysqlConnection() {
			global $mysqli;
			
			if (!isset($mysqli) or $mysqli->server_version === null) {
				$mysqli = new mysqli('localhost', 'loginManager', 'aDVhuqhBjYJQ753h', 'test');
				$mysqli->set_charset('utf8');
			}

			return $mysqli;
		}
	}

?>