<?php 
class chandb {

	public function __construct(	
		private string $host,
		private string $user,
		private string $password,
		private string $db_name,
		private ?mysqli $conn = null
	) {}

	public function connect() {
		$this->conn = new mysqli(
			$this->host,
			$this->user,
			$this->password,
			$this->db_name);
		return $this->conn;
	}
}
?>