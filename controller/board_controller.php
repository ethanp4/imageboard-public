<?php
include 'config/chandb.php';
include 'model/posts_model.php';

class board_controller {
	private posts_model $model;
	
	public function __construct() {
		$db = new chandb($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
		$conn = $db->connect();
		$this->model = new posts_model($conn);
	}

	public function general($mode) {
		switch ($mode) {
			case 'catalog':
				$board = board::General;
				//by default use activity, if its set but invalid then still use activity
				$order = return_order::tryFrom($_GET['display-order'] ?? 'activity') ?? return_order::activity;
				$threads = $this->model->get_board_threads($board, $order);
				if ($threads == 0) {
					include 'view/error.php';
					$_SESSION['error_title'] = 'Some error occured';
					$_SESSION['error_msg'] = 'An error occured getting threads';
					exit();
				}
				include 'view/pages/board-catalog.php';
			break;
			case 'default':
				$board = board::General;
				$threads = $this->model->get_pretty_threads($board);
				if ($threads == 0) {
					$_SESSION['error_title'] = 'Some error occured';
					$_SESSION['error_msg'] = 'An error occured getting threads';
					include 'view/error.php';
					exit();
				}
				include 'view/pages/board-default.php';
			break;
		}
	}


	public function view_gn_thread($thread_id) {
		$board = board::General;
		$thread = $this->model->get_thread_replies($board, $thread_id);
		if ($thread == 0) {
			include 'view/error.php';
			$_SESSION['error_title'] = 'Some error occured';
			$_SESSION['error_msg'] = 'An error occured getting the replies';
			exit();
		}
		if ($thread == []) {
			include 'view/404.php';
			exit();
		}
		include 'view/pages/thread.php';
	}

	public function submit_post() {
		try {
			$board = board::from($_POST['board']);
			$type = post_type::from($_POST['type']);
			$new_post = null;

			$date = new DateTime();
			$milliseconds = $date->format('Uv');
			if (isset($_SESSION['last_post_time'])) {
				$since_last_post = $milliseconds - $_SESSION['last_post_time'];
				if ($since_last_post <= 10000) {
					$_SESSION['error_title'] = 'Rate limited';
					$_SESSION['error_msg'] = 'Please wait ' . ((10000 - $since_last_post) / 1000) . ' more seconds before posting again';
					include 'view/error.php';
					exit();
				}
			}
			$_SESSION['last_post_time'] = $milliseconds;
			//validate image
			if ($_FILES['file']['size'] != 0) {
				// $target_dir = "/userfiles/";
				$valid_extensions = ['png', 'jpg', 'jpeg', 'webp', 'apng'];
				$valid_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
				$target_dir = 'i/';
				$file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				$mime = $finfo->file($_FILES['file']['tmp_name']);
				
				#where this file is going to get saved
				$target_path = $target_dir . $milliseconds . '.' . $file_extension;

				# check that the extension and the mime type are valid
				$check = in_array($file_extension, $valid_extensions) && in_array($mime, $valid_types);
				if ($check !== false) {
					//it is an image!
					$image_id = htmlspecialchars($milliseconds . '.' . $file_extension);
					$filename = htmlspecialchars(basename($_FILES["file"]["name"]));
					move_uploaded_file($_FILES['file']['tmp_name'], $target_path);
				} else {
					//it wasnt an image
					$_SESSION['error_title'] = "Invalid file";
					$_SESSION['error_msg'] = 'Found something strange.., expected image or gif';
					include 'view/error.php';
					exit();
				}
			} else {
				//image not provided
				$image_id = null;
				$filename = null;
			}
			switch ($type) {
				case post_type::reply:
					$new_post = new reply(
						board: $board,
						thread_id: $_POST['thread-id'],
						name: $_POST['name'] !== '' ? htmlspecialchars($_POST['name']) : 'Anonymous',
						comment: $_POST['comment'] !== '' ? htmlspecialchars($_POST['comment']) : null,
						filename: $filename,
						image_id: $image_id,
					);
				break;
				case post_type::thread:
					$new_post = new thread(
						board: $board,
						name: $_POST['name'] !== '' ? htmlspecialchars($_POST['name']) : 'Anonymous',
						subject: $_POST['subject'] !== '' ? htmlspecialchars($_POST['subject']) : null,
						comment: $_POST['comment'] !== '' ? htmlspecialchars($_POST['comment']) : null,
						filename: $filename,
						image_id: $image_id,
					);
				break;
			}
			$res = $this->model->insert_post($new_post);
			if ($res == 0) {
				include 'view/error.php';
				exit();
			}
			//request was good, redirect the user
			error_log(print_r($res, true));
			switch ($new_post->type) {
				case post_type::thread:
					//gotta get the new thread id to make this more useful
					header("Location: ". $new_post->board->path());
				break;
				case post_type::reply:
					header('Location: ' . $new_post->board->path() .'/t/'. $new_post->thread_id . '#bottom');
				break;
			}
		} catch (Throwable $e) {
			$_SESSION['error_title'] = 'Some error occured';
			$_SESSION['error_msg'] = 'An error occured making your post';
			// $_SESSION['error_msg'] ?? 'In line: ' . $e->getLine() . ' ' .$e->getMessage();
			include 'view/error.php';
			exit();
		}
	}

}

?>