<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/style.css">
	<title>An Error!</title>
</head>
<body>
	<center><h1><?php echo $_SESSION["error_title"] ?? "There was no error actually" ?></h1></center>
	<hr>
	<center><p class='regular-subheading'><?php echo $_SESSION['error_msg'] ?? "Go make one!" ?></p></center>
	<?php include 'view/layouts/global-footer.php'; ?>
</body>
</html>