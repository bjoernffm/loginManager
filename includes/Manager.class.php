<?

	namespace LoginManager;
	use \stdClass;
	
	require_once 'Base.class.php';
	require_once 'Login.class.php';

	class Manager extends Base {
		
		private $userId;
		
		public function __construct($userId) {
			$this->userId = $userId;
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
	}

?>