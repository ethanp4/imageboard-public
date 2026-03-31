<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/png" href="<?php $_ENV["SITE_ICON"] ?>" />
	<link rel="stylesheet" type="text/css" href="/style.css">
	<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
	<script src='/script.js'></script>
	<title><?php echo $board->name(); ?></title>

	<meta property="og:title" content="<?php echo $board->name(); ?>" />
	<meta property="og:description" content="<?php echo $board->name(); ?>" />
	<meta property="og:url" content="<?php $_ENV["SITE_URL"] ?>" />
	<meta property="og:image" content="<?php $_ENV["SITE_ICON"] ?>" />
	<meta property="og:type" content="website" />
	<meta name="theme-color" content="#137a7f" data-react-helmet="true" /></head>

</head>
<body>
<?php include 'view/layouts/board-header.php' ?>
	<?php
	if (sizeof($threads) == 0) {
		echo 'No threads found';
	}
	foreach($threads as $thread) {
		$thread_op = $thread['thread'];
		include 'view/user-generated/thread-op.php';
		$hidden_replies = ($thread_op->reply_count-5) < 0 ? 0 : ($thread_op->reply_count-5);
		$visible_replies = ($thread_op->reply_count >= 5) ? 5 : $thread_op->reply_count;
		echo "<hr>";
		echo "<div class='regular-heading'><a title='currently viewing the $visible_replies most recent replies' class='regular-link' href=/gn/t/$thread_op->id>go to thread with <strong>$hidden_replies</strong> hidden replies -></a></div>";
		foreach($thread['replies'] as $reply) {
			include 'view/user-generated/reply.php';
		}
		echo '<hr><hr><hr>';
	}
	?>
<?php include 'view/layouts/global-footer.php'; ?>
</body>
</html>