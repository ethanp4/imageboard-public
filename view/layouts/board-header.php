<header>
	<a class='regular-link' href='/'>[Home page]</a>
	<a class='regular-link' href='<?php echo $board->path(); ?>'>[Regular]</a>
	<a class='regular-link' href='<?php echo $board->path(); ?>/catalog'>[Catalog]</a>
</header>
<h1>Welcome to <?php echo $board->name(); ?></h1>
<?php include 'view/forms/thread-form.php'; ?>
<div class='header-summaries'>
</div>
	
