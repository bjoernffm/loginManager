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
		
		public static function checkCredentials($login, $password) {
			$mysqli = self::getMysqlConnection();
			
			$login = $mysqli->real_escape_string($login);
			$password = $mysqli->real_escape_string($password);
			
			$result = $mysqli->query('SELECT
											*
										FROM
											`users`
										WHERE
											`user_login` =  "' . $login . '" AND
											`user_password` =  "' . $password . '"
										LIMIT 1');
			if ($result->num_rows != 1)
				throw new Exception('User not found', 404);
			
			$row = $result->fetch_assoc();
			return $row;										
			
		}
		
		public function getPasswordById($id) {
			$mysqli = self::getMysqlConnection();
			
			$id = (int) $id;
			
			$result = $mysqli->query('SELECT
											`login_password`
										FROM
											`logins`
										WHERE
											`login_id` = '.$id);
											
			$password = false;
			
			if ($row = $result->fetch_assoc()) {
				$password = trim(Encryption::decrypt($row['login_password']));
			}
			
			return $password;
			
		}
		
		public function getLogin($id) {
			$mysqli = self::getMysqlConnection();
			
			$id = (int) $id;
			
			$result = $mysqli->query('SELECT
											*
										FROM
											`logins`
										WHERE
											`login_id` = ' .$id);
											
			if ($result->num_rows != 1)
				throw new Exception('Record not found.', 404);
									
			$row = $result->fetch_assoc();
					
			$login = new stdClass();
			$login->id = $row['login_id'];
			$login->user = $row['login_user'];
			$login->password = trim(Encryption::decrypt($row['login_password']));
			$login->location = $row['login_location'];
			$login->description = $row['login_description'];
				
			if (empty($row['login_tags'])) {
				$login->tags = array();
			} else {
				$login->tags = explode(',', $row['login_tags']);
			}
		
			return $login;	
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
			
			if (!isset($array['user']) or empty(trim($array['user'])))
				throw new Exception('Missing user element.', 400);
				
			if (!isset($array['password']) or empty(trim($array['password'])))
				throw new Exception('Missing password element.', 400);
			
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
				throw new Exception($mysqli->error, 500);
								
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
				throw new Exception($mysqli->error, 500);
		}	

		public function editLogin($array) {
			
			if (0 >= (int) $array['id'])
				throw new Exception('Missing id element.', 400);
			
			if (!isset($array['user']) or empty(trim($array['user'])))
				throw new Exception('Missing user element.', 400);
				
			if (!isset($array['password']) or empty(trim($array['password'])))
				throw new Exception('Missing password element.', 400);
				
			$id = $array['id'];
			$user = $array['user'];
			$password = Encryption::encrypt($array['password']);
			$location = $array['location'];
			$description = $array['description'];
			$tags = $array['tags'];
			
			$mysqli = self::getMysqlConnection();
			
			$id = (int) $id;
			$user = $mysqli->real_escape_string($user);
			$password = $mysqli->real_escape_string($password);
			$location = $mysqli->real_escape_string($location);
			$description = $mysqli->real_escape_string($description);
			$tags = $mysqli->real_escape_string($tags);
			
			$result = $mysqli->query('UPDATE
											`logins`
										SET
											`login_user` = "' . $user . '",
											`login_password` = "' . $password . '",
											`login_location` = "' . $location . '",
											`login_description` = "' . $description . '",
											`login_tags` = "' . $tags . '"
										WHERE
											`login_id` = ' . $id . '
										LIMIT 1');
			if ($result === false)
				throw new Exception($mysqli->error, 500);
		}

		public function removeLogin($id) {
			
			$id = (int) $id;
			
			if ($id <= 0)
				throw new Exception('Invalid id given. " ' .$id .  '"', 400);
			
			$mysqli = self::getMysqlConnection();

			$result = $mysqli->query('DELETE FROM
											`logins`
										WHERE
											`login_id` = ' . $id . '
										LIMIT 1');
			if ($result === false)
				throw new Exception($mysqli->error, 500);

			$result = $mysqli->query('DELETE FROM
											`user_logins`
										WHERE
											`login_id` = ' . $id);
			if ($result === false)
				throw new Exception($mysqli->error, 500);
		}	
	}

?>