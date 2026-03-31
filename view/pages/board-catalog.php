<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/style.css">
	<link rel="icon" type="image/png" href="<?php $_ENV["SITE_ICON"] ?>" />
	<title><?php echo $board->name(); ?></title>

	<meta property="og:title" content="<?php echo $board->name(); ?> catalog" />
	<meta property="og:description" content="<?php echo $board->name(); ?> catalog" />
	<meta property="og:url" content="<?php $_ENV["SITE_URL"] ?>" />
	<meta property="og:image" content="<?php $_ENV["SITE_ICON"] ?>" />
	<meta property="og:type" content="website" />
	<meta name="theme-color" content="#137a7f" data-react-helmet="true" />
</head>

<body>
	<?php include 'view/layouts/board-header.php' ?>
	<form class='regular-heading display-order-form' method='GET' action='/gn/catalog'>
		Display order:
		<select name='display-order' onchange="submit()">
			<option <?php echo ($_GET['display-order'] ?? '') == 'activity' ? 'selected' : '' ?> value='activity'>Latest reply
				first</option>
			<option <?php echo ($_GET['display-order'] ?? '') == 'created' ? 'selected' : '' ?> value='created'>Newest first
			</option>
		</select>
	</form>
	<div class="thread-container">
		<?php
		if (sizeof($threads) == 0) {
			echo 'No threads found';
		}
		foreach ($threads as $thread) {
			include 'view/user-generated/thread-preview.php';
		}
		?>
	</div>
	<?php include 'view/layouts/global-footer.php'; ?>
</body>

</html>