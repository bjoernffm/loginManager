<?

	namespace LoginManager;
	use \stdClass;
	use \Exception;
	use \Encryption;
	
	require_once 'Base.class.php';
	require_once 'Login.class.php';
	require_once 'Encryption.class.php';

	class Manager extends Base {
		
		private $userId;
		
		public function __construct($userId) {
			$this->userId = $userId;
		}
		
		public function getPasswordById($id) {
			$mysqli = self::getMysqlConnection();
			
			$id = $mysqli->real_escape_string($id);
			
			$result = $mysqli->query('SELECT
											`login_password`
										FROM
											`logins`
										WHERE
											`login_id` = '.$id);
											
			$password = false;
			
			if ($row = $result->fetch_assoc()) {
				$password = $row['login_password'];
			}
			
			return $password;
			
		}
		
		public function getLoginList() {
			$mysqli = self::getMysqlConnection();
			
			$result = $mysqli->query('SELECT
											`logins`.*,
											`user_logins`.`type`
										FROM
											`user_logins`
										LEFT JOIN
											`logins`
										ON
											`logins`.`login_id` = `user_logins`.`login_id`
										WHERE
											`user_id` = '.$this->userId);
											
			$logins = array();
			while ($row = $result->fetch_assoc()) {
				$object = new stdClass();
				$object->id = $row['login_id'];
				$object->user = $row['login_user'];
				$object->password = $row['login_password'];
				$object->location = $row['login_location'];
				$object->description = $row['login_description'];
				
				if (empty($row['login_tags'])) {
					$object->tags = array();
				} else {
					$object->tags = explode(',', $row['login_tags']);
				}
				
				$object->type = $row['type'];
				
				$logins[] = $object;
			}
			
			return $logins;
		}	
		
		public function getLoginListByKeywords($keywords) {
			
			if (empty(trim($keywords)))
				return array();
			
			$mysqli = self::getMysqlConnection();
			
			$keywords = explode(' ', $keywords);
			$prepared = array();
			foreach ($keywords as $keyword) {
				$keyword = $mysqli->real_escape_string($keyword);
				$prepared[] = '`login_user` LIKE "%' . $keyword . '%"';
				$prepared[] = '`login_location` LIKE "%' . $keyword . '%"';
				$prepared[] = '`login_description` LIKE "%' . $keyword . '%"';
				$prepared[] = '`login_tags` LIKE "%' . $keyword . '%"';
			}
			
			$result = $mysqli->query('SELECT
											`logins`.*,
											`user_logins`.`type`
										FROM
											`user_logins`
										LEFT JOIN
											`logins`
										ON
											`logins`.`login_id` = `user_logins`.`login_id`
										WHERE
											`user_id` = ' . $this->userId . ' AND
											(
												' . implode(' OR ', $prepared) . '	
											)');
											
			$logins = array();
			while ($row = $result->fetch_assoc()) {
				$object = new stdClass();
				$object->id = $row['login_id'];
				$object->user = $row['login_user'];
				$object->password = $row['login_password'];
				$object->location = $row['login_location'];
				$object->description = $row['login_description'];
				
				if (empty($row['login_tags'])) {
					$object->tags = array();
				} else {
					$object->tags = explode(',', $row['login_tags']);
				}
				
				$object->type = $row['type'];
				
				$logins[] = $object;
			}
			
			return $logins;
		}

		public function addLogin($array) {
			$user = $array['user'];
			$password = Encryption::encrypt($array['password']);
			$location = $array['location'];
			$description = $array['description'];
			$tags = $array['tags'];
			
			$mysqli = self::getMysqlConnection();
			
			$user = $mysqli->real_escape_string($user);
			$password = $mysqli->real_escape_string($password);
			$location = $mysqli->real_escape_string($location);
			$description = $mysqli->real_escape_string($description);
			$tags = $mysqli->real_escape_string($tags);
			
			$result = $mysqli->query('INSERT INTO
										`logins`
										(
											`login_user`,
											`login_password`,
											`login_location`,
											`login_description`,
											`login_tags`
										) VALUES (
											"' . $user . '",
											"' . $password . '",
											"' . $location . '",
											"' . $description . '",
											"' . $tags . '"
										)');
			if ($result === false)
				throw new Exception($mysqli->error);
								
			$id = $mysqli->insert_id;
			
			$result = $mysqli->query('INSERT INTO
										`user_logins`
										(
											`user_id`,
											`login_id`,
											`type`
										) VALUES (
											' . $this->userId . ',
											' . $id . ',
											"OWNED"
										)');
			if ($result === false)
				throw new Exception($mysqli->error);
		}	
	}

?>