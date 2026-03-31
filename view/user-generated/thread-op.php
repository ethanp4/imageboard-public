<div id='<?php echo $thread_op->id ?>' class='reply-box'>
	<div class="thread-info">
	<a href='/i/<?php echo $thread_op->image_id ?>' download='<?php echo $thread_op->filename ?>' title='Download as <?php echo $thread_op->filename ?>'>⇣</a>
	
	<!-- thread subject line, op name, time -->
	<span class='thread-subject'><?php echo $thread_op->subject ?? "" ?></span>
	<span id='name'><strong><?php echo $thread_op->name ?></strong></span>
	<span><?php echo $thread_op->created->format('Y-m-d H:i:s') ?></span>
	
		<!-- the 3 rightmost buttons -->
		<span>
			<?php $thread_complete_link = $board->path() . "/t/$thread_op->id#" . $thread_op->id ?>
			<?php $thread_link = $board->path() . "/t/$thread_op->id" ?>

			<a href='<?php echo $thread_complete_link ?>'>#</a>
			<a class='quick-reply-link' 
				<?php echo str_contains($_SERVER['REQUEST_URI'], $thread_link) ? 'onclick="return false;"' : '' ?> 
				href='<?php echo "$thread_link?reply=$thread_op->id" ?>' name='<?php echo $thread_op->id ?>'>
				<?php echo $thread_op->id ?></a>
		
			<!-- ADMIN SECTION HERE -->
			<?php if($_SESSION['login_user'] ?? '' === $_ENV['ADMIN_USERNAME']): ?>
				<a href='/delete_post/?id=<?php echo $thread_op->id ?>&board=<?php echo $board->value ?>'>🗑</a>
			<?php endif; ?>
			<!-- ADMIN SECTION HERE -->
		</span>

		<!-- thread image -->
		<a href='/i/<?php echo $thread_op->image_id ?>' >
			<img 
				loading='lazy' 
				class='thread-image' 
				title='<?php echo $thread_op->filename?>' 
				alt='<?php echo $thread_op->filename?>' 
				src='/i/thumb/<?php echo $thread_op->image_id ?>'
			>
		</a>
		
	<!-- text content -->
	</div>
		<div>
			<p>
				<?php echo $thread_op->comment == '' ? '<span class="faded">No text content<span>' : nl2br($thread_op->comment) ?>
			</p>
		</div>
	</div>