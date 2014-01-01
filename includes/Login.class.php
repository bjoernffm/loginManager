<?

	namespace LoginManager;
	
	class Login {
		
		private $id;
		private $user;
		private $password;
		private $location;
		private $description;
		private $tags;
		private $type;
		
		public function __construct($object = null) {
			
			if ($object !== null)
				$this->set($object);
			
		}
		
		public function set($object) {
			
			$this->id = $object->id;
			$this->user = $object->user;
			$this->password = $object->password;
			$this->location = $object->location;
			$this->description = $object->description;
			$this->tags = $object->tags;
			$this->type = $object->type;
			
		}
		
		public function getId() {
			return $this->id;
		}
		
		public function getUser() {
			return $this->user;
		}
		
		public function getPassword() {
			return $this->password;
		}
		
		public function getLocation() {
			return $this->location;
		}
		
		public function getDescription() {
			return $this->description;
		}
		
		public function getTags() {
			return $this->tags;
		}
		
		public function getType() {
			return $this->type;
		}
		
	}

?>