<?php

/**
 * Enum to store the post type for use with db enum
 */
enum post_type: string {
	case thread = 'thread';
	case reply = 'reply';
}

/**
 * Enum that refers to the ORDER BY column to use
 */
enum return_order: string {
	case activity = 'activity';
	case created = 'created';

	public function column(): string {
		return match($this) {
			return_order::activity => 'last_bump',
			return_order::created => 'created'
		};
	}
}

/**
 * Enum to store board -> table name. Additionally
 * name() returns the full title of the board
 */
enum board: string {
	case General = "gn";

	public function table(): string {
		return match($this) {
			board::General => 'posts_gn'
		};
	}

	public function name(): string {
		return match($this) {
			board::General => 'General'
		};
	}

	public function path(): string {
		return match($this) {
			board::General => '/gn'
		};
	}
}

/**
 * Abstract class containing common properties
 */
readonly abstract class post {
	public function __construct(
		//mandatory fields
		public board $board,
		public post_type $type,
		//optional fields
		public ?string $name, 
		public ?string $comment,
		public ?string $filename,
		public ?string $image_id,
		//null when creating
		public ?int $id, 
		public ?DateTime $created
	) {}
}

/**
 * Contains thread specific properties and default type
 */
readonly class thread extends post {
	public function __construct(
		//mandatory fields
		board $board,
		//optional fields
		?string $name,
		?string $comment,
		?string $filename,
		?string $image_id,
		public ?string $subject,
		//omitted when creating (created by db)
		?int $id = null,
		?DateTime $created = null,
		public ?DateTime $last_bump = null,
		//omitted when creating, filled in on query
		public ?int $reply_count = null,
		public ?int $image_count = null,
	) {
		parent::__construct(
			board: $board,
			id: $id,
			name: $name,
			comment: $comment,
			filename: $filename,
			image_id: $image_id,
			created: $created,
			type: post_type::thread
		);
	}
}

/**
 * Additionally thread id and has default type
 */
readonly class reply extends post {
	public function __construct(
		//mandatory fields
		board $board,
		public int $thread_id,
		//optional fields
		?string $name,
		?string $comment,
		?string $filename,
		?string $image_id,
		//omitted when creating
		?int $id = null,
		?DateTime $created = null,
	) {
		parent::__construct(
			board: $board,
			id: $id,
			name: $name,
			comment: $comment,
			filename: $filename,
			image_id: $image_id,
			created: $created,
			type: post_type::reply
		);
	}
}

/**
 * Database related functions dealing with posts
 */
class posts_model {
	private $conn;

	public function __construct(mysqli $conn) {
		$this->conn = $conn;
	}

	/**
	 * Insert a post (after validation) into the db. 
	 * Returns 1 on success, 0 on error, and sets $_SESSION["error_msg"]
	 * @return int
	 */
	public function insert_post(reply|thread $post): int {
		try {
			switch (get_class($post)) {
				case 'reply':
					$stmt = $this->conn->prepare(
						"INSERT INTO {$post->board->table()} (thread_id, type, name, comment, image_id, filename, ip)
						VALUES (?, ?, ?, ?, ?, ?, ?)");
					$result = $stmt->execute([
						$post->thread_id, 
						$post->type->value, 
						$post->name,
						$post->comment, 
						$post->image_id, 
						$post->filename,
						$_SERVER['HTTP_X_FORWARDED_FOR']
					]);
					$stmt->close();
					//update thread last bump if it was a reply (cant make a trigger since its self referential or something)
					if ($post->type == post_type::reply) {
						$stmt = $this->conn->prepare(
							"UPDATE {$post->board->table()} SET last_bump = CURRENT_TIMESTAMP() WHERE id = ?"
						);
						$result = $stmt->execute([$post->thread_id]);
						$stmt->close();
					} 
				break;
				case 'thread':
					$stmt = $this->conn->prepare(
						"INSERT INTO {$post->board->table()} (type, name, subject, comment, image_id, filename, ip)
						VALUES (?, ?, ?, ?, ?, ?, ?)");
					$result = $stmt->execute([
						$post->type->value, 
						$post->name, 
						$post->subject, 
						$post->comment, 
						$post->image_id, 
						$post->filename,
						$_SERVER['HTTP_X_FORWARDED_FOR']
					]);
					$stmt->close();
				break;
			}
			$new_id = $this->conn->insert_id;
			// error_log(print_r($this->conn, true));
			return $result; //returns the new id on success
		} catch (Throwable $e) {
			http_response_code(400);
			$_SESSION["error_title"] = "User input error";
			if ($this->conn->errno == 4025) { //constraint error code
				switch ($post->type) {
					case post_type::thread:
						$_SESSION["error_msg"] = 'Threads require at least an image and one text field';
						break;
					case post_type::reply:
						$_SESSION["error_msg"] = 'Replies require at least text or an image';
						break;
					}
			} else {
				//idk what this would be
				http_response_code(500);
				//generic message is set by the calling function
				// $_SESSION['error_msg'] = 'On line: ' . $e->getLine() . " " . $e->getMessage();
				$_SESSION['error_msg'] = 'Internal server error';
			}
			return 0;
		}
	}
	/**
	 * Get the threads associated with a board
	 * @return post[]
	 */
	public function get_board_threads(board $board, return_order $order): array|int {
		try {
			$stmt = $this->conn->query(
				"SELECT t.*, s.reply_count, s.image_count
				FROM {$board->table()} t
				LEFT JOIN thread_stats s ON t.id = s.thread_id
				WHERE t.type = 'thread'
				ORDER BY t.{$order->column()} DESC
				LIMIT 50;");
			$ret = [];
			while($row = $stmt->fetch_assoc()) {
				$ret[] = new thread(
					board: $board,
					id: $row['id'],
					subject: $row['subject'],
					comment: $row["comment"],
					filename: $row['filename'],
					image_id: $row["image_id"],
					created: DateTime::createFromFormat('Y-m-d H:i:s', $row["created"])->setTimezone($_SESSION['user_timezone'] ?? new DateTimeZone('UTC')),
					last_bump: DateTime::createFromFormat('Y-m-d H:i:s', $row["last_bump"])->setTimezone($_SESSION['user_timezone'] ?? new DateTimeZone('UTC')),
					reply_count: $row["reply_count"],
					image_count: $row['image_count'],
					name: $row["name"],
				);
			}
			return $ret;
		} catch (Throwable $e) {
			// $_SESSION['error_title'] = "Sql error";
			// $_SESSION['error_msg'] = 'In line: ' . $e->getLine() . ' ' . $e->getMessage();
			return 0;
		}
	}

	/**
	 * @return post[]
	 */
	public function get_pretty_threads(board $board): array|int {
		try {
			//fetch each thread, then for each thread fetch
			//their 5 newest replies
			//create a 2d array of posts for this to return
			$ret = [];
			$threads = $this->get_board_threads($board, return_order::activity);
			foreach ($threads as $thread) {
				$stmt = $this->conn->prepare(
					"SELECT * FROM {$board->table()} WHERE type = 'reply' AND thread_id = ? ORDER BY created DESC LIMIT 5"
				);
				$stmt->execute([$thread->id]);
				$result = $stmt->get_result();
				$subret = []; //subarray of 5 replies
				while($row = $result->fetch_assoc()) {
					$subret[] = new reply(
						board: $board,
						id: $row['id'],
						thread_id: $row['thread_id'],
						comment: $row['comment'],
						filename: $row['filename'],
						image_id: $row['image_id'],
						created: DateTime::createFromFormat('Y-m-d H:i:s', $row["created"])->setTimezone($_SESSION['user_timezone'] ?? new DateTimeZone('UTC')),
						name: $row['name']
					);
				}
				$ret[] = [
					'thread' => $thread,
					'replies' => array_reverse($subret)
				];
			}
			return $ret;
		} catch (Throwable $e) {
			// $_SESSION['error_title'] = "Sql error";
			// $_SESSION['error_msg'] = 'On line: ' . $e->getLine() . ' ' . $e->getMessage();
			return 0;
		}
	}

	/**
	 * Get the replies to a thread by board and thread_id
	 * @return post[]
	 */
	public function get_thread_replies(board $board, int $thread_id): array|int {
		try {
			$ret = [];
			//get the thread itself
			$stmt = $this->conn->prepare(
				"SELECT t.*, s.reply_count, s.image_count
				FROM {$board->table()} t
				LEFT JOIN thread_stats s ON t.id = s.thread_id
				WHERE type = 'thread' AND id = ?"
			);
			$stmt->bind_param('i', $thread_id);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {
				$ret[] = new thread(
					board: $board,
					id: $row['id'],
					subject: $row['subject'],
					comment: $row['comment'],
					filename: $row['filename'],
					image_id: $row['image_id'],
					created: DateTime::createFromFormat('Y-m-d H:i:s', $row["created"])->setTimezone($_SESSION['user_timezone'] ?? new DateTimeZone('UTC')),
					last_bump: DateTime::createFromFormat('Y-m-d H:i:s', $row["last_bump"])->setTimezone($_SESSION['user_timezone'] ?? new DateTimeZone('UTC')),
					reply_count: $row['reply_count'],
					image_count: $row['image_count'],
					name: $row['name']
				);
			}
			//now get the replies
			$stmt = $this->conn->prepare(
				"SELECT * FROM {$board->table()} WHERE type = 'reply' AND thread_id = ? ORDER BY created ASC"
			);
			$stmt->bind_param('i', $thread_id);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_assoc()) {
				$ret[] = new reply(
					board: $board,
					id: $row['id'],
					thread_id: $row['thread_id'],
					comment: $row['comment'],
					filename: $row['filename'],
					image_id: $row['image_id'],
					created: DateTime::createFromFormat('Y-m-d H:i:s', $row["created"])->setTimezone($_SESSION['user_timezone'] ?? new DateTimeZone('UTC')),
					name: $row['name']
				);
			}
			return $ret;
		} catch (Throwable $e) {
			// $_SESSION['error_title'] = "Sql error";
			return 0;
		}
	}

	public function delete_post(board $board, int $id) {
		try {
			$stmt = $this->conn->prepare(
				"SELECT image_id FROM {$board->table()} WHERE id = ?"
			);
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$result = $stmt->get_result()->fetch_assoc()['image_id'];
			$stmt->close();
			if ($result != null) {
				unlink('i/' . $result);
			}
			//mysql will cascade delete posts, but only the one image is deleted here
			$stmt = $this->conn->prepare(
				"DELETE FROM {$board->table()} WHERE id = ?"
			);
			$stmt->bind_param('i', $id);
			$result = $stmt->execute();
			$stmt->close();
			return $result;
		} catch (Throwable $e) {
			return 0;
		}
	}
}
?>