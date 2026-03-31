<?php 
$thread_op = $thread[0];
$subject = $thread_op->subject;
$comment = $thread_op->comment;
$title = $subject ?? $comment;
$title = mb_strimwidth($title, 0, 35, '...');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/png" href="<?php $_ENV["SITE_ICON"] ?>" />
	<link rel="stylesheet" type="text/css" href="/style.css">
	<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
	<script src='/script.js'></script>
	<title>Viewing <?php echo $title ?></title>

	<meta property="og:title" content="<?php echo $title ?>" />
	<meta property="og:description" content="<?php echo $comment ?>" />
	<meta property="og:url" content="<?php $_ENV["SITE_URL"] ?>" />
	<meta property="og:image" content="<?php $_ENV["SITE_URL"] ?>/i/thumb/<?php echo $thread_op->image_id ?>" />
	<meta property="og:type" content="website" />
	<meta name="theme-color" content="#137a7f" data-react-helmet="true" /></head>
</head>
<body>
	<header>
		<a class='regular-link' href=<?php echo $board->path() ?>>Return</a>
	</header>
	<h1>Viewing thread <?php echo $title ?></h1>

			<?php include 'view/forms/reply-form.php'; ?>

	<?php
		include 'view/user-generated/thread-op.php';
		echo "<hr>";
		for ($i = 1; $i < sizeof($thread); $i++) {
			$reply = $thread[$i];
			include 'view/user-generated/reply.php';
		}
	?>

<?php include 'view/layouts/global-footer.php'; ?>
</body>
</html>