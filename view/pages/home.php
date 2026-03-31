<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/png" href="<?php $_ENV["SITE_ICON"] ?>" />
	<link rel="stylesheet" type="text/css" href="/style.css">
	<title><?php $_ENV['SITE_NAME'] ?></title>

	<meta property="og:title" content="<?php $_ENV['SITE_NAME'] ?>" />
	<meta property="og:description" content="<?php $_ENV['SITE_NAME'] ?>" />
	<meta property="og:url" content="<?php $_ENV["SITE_URL"] ?>" />
	<meta property="og:image" content="<?php $_ENV["SITE_ICON"] ?>" />
	<meta property="og:type" content="website" />
	<meta name="theme-color" content="#137a7f" data-react-helmet="true" /></head>

</head>
<body>
	<h1>Welcome to <?php $_ENV['SITE_NAME'] ?></h1>
		<details style="display: inline;">
		<summary>Rules</summary>
		<div class="reply-box">
			<ul>
				<li>No being bad</li>
			</ul>
		</div>
	</details>
	<h3 class='choose-a-board-heading'>Choose a board</h3>
	<div class='boards-listing'>
		<p><a class='regular-link' href="/gn">General</a></p>
	</div>
	<?php include 'view/layouts/global-footer.php'; ?>
</body>
</html>