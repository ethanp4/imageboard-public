<?php 
declare(strict_types = 1);
session_start();
error_reporting(E_ALL);
ini_set('display_errors', "Off");
date_default_timezone_set('UTC'); //db timezone

include 'util.php';

#generate a random id that is embedded into the html and verified when the user posts
if (empty($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//leave the home page as /
if ($path != "/") { $path = rtrim($path, '/'); }


if ($_SESSION['login_user'] ?? '' === $_ENV['ADMIN_USERNAME']) {
	$delete_pattern = "/^\/delete_post[\/]*$/";
	if (preg_match($delete_pattern, $path) && $_SERVER['REQUEST_METHOD'] === 'GET') {
		// echo 'delete post: ' . $_GET['id'];
		ini_set('display_errors', "On");
		include 'controller/admin_controller.php';
		$admin_controller = new admin_controller();
		$admin_controller->delete_post(board::from($_GET['board']), (int)$_GET['id']);
		exit();
	}
}

//match[1] == id
//match[2] == extension
// function for retrieving the "fake" thumbnail paths
$thumb_pattern = '/^\/i\/thumb\/([0-9]{13})(.[A-z]+)$/';
$match = null;
if (preg_match($thumb_pattern, $path, $match) && $_SERVER['REQUEST_METHOD'] === 'GET') {
	//we've only gotten here if it was going to be 404, so now
	//generate a thumbnail and show it, and if that fails
	//then just return the original image
	//i could possibly generate proper thumbnails and save them instead
	$path = "i/$match[1]$match[2]";
	generate_thumbnail($path);
	exit();
}

if ($path === '/' && $_SERVER['REQUEST_METHOD'] === 'GET') {
	cookiecheck();
	include 'view/pages/home.php';
	exit();
}

if ($path === '/submit_post' && $_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($_POST['option'] != $_SESSION['csrf_token']) {
		exit();
	}
	setcookie("last_used_name", htmlspecialchars($_POST['name']) ?? '', time() + (86400 * 30), "/");
	#the other inputs are sanitized in the controller itself
	include 'controller/board_controller.php';
	$board_controller = new board_controller();
	$board_controller->submit_post();
	exit();
}

if ($path === '/gn' && $_SERVER['REQUEST_METHOD'] === 'GET') {
	cookiecheck();
	include 'controller/board_controller.php';
	$board_controller = new board_controller();
	$board_controller->general('default');
	exit();
}

if ($path === '/gn/catalog' && $_SERVER['REQUEST_METHOD'] === 'GET') {
	cookiecheck();
	include 'controller/board_controller.php';
	$board_controller = new board_controller();
	$board_controller->general('catalog');
	exit();
}

$thread_pattern = "/^\/gn\/t\/([0-9]+)[\/]*$/";
$match = null;
if (preg_match($thread_pattern, $path, $match) && $_SERVER['REQUEST_METHOD'] === 'GET') {
	if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
		echo 'Wrong method';
		return;
	}
	cookiecheck();
	include 'controller/board_controller.php';
	$board_controller = new board_controller();
	$board_controller->view_gn_thread($match[1]);
	exit();
}

if ($path === '/login') {
	if ($_SERVER['PHP_AUTH_USER'] === $_ENV['ADMIN_USERNAME']) {
		$_SESSION['login_user'] = $_ENV['ADMIN_USERNAME'];
		echo 'youve been logged in<br>';
	} else {
		echo 'you are not logged in<br>';
	}
	echo "<a href=/><img src=/image.gif></img></a>";
	exit();
	// header('Location: /');
}

http_response_code(404);
include 'view/404.php';

exit();
?>