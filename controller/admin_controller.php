<?php
include 'config/chandb.php';
include 'model/posts_model.php';

class admin_controller {
	private posts_model $model;
	
	public function __construct() {
		$db = new chandb($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
		$conn = $db->connect();
		$this->model = new posts_model($conn);
	}

	public function delete_post(board $board, int $id): int {
		$return = $this->model->delete_post($board, $id);
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		return $return;
	}

	public function create_board() {

	}
}

?>