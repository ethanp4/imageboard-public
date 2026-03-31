<div id='<?php echo $reply->id ?>' class="reply-box">
	<div class="reply-info">
			<?php if(!is_null($reply->image_id)) : ?>
			<a href='/i/<?php echo $reply->image_id ?>' download='<?php echo $reply->filename ?>' title='Download as <?php echo $reply->filename ?>'>⇣</a>
			<?php endif; ?>
			<span id='name'><strong><?php echo $reply->name ?></strong></span>
			<span><?php echo $reply->created->format('Y-m-d H:i:s') ?></span>
			<span>
				<?php $reply_complete_link = $board->path() . "/t/$reply->thread_id#" . $reply->id ?>
				<?php $thread_link = $board->path() . "/t/$reply->thread_id" ?>
				<a href='<?php echo $reply_complete_link ?>'>#</a>
				<a class='quick-reply-link' 
					<?php echo str_contains($_SERVER['REQUEST_URI'], $thread_link) ? 'onclick="return false;"' : '' ?> 
					href='<?php echo "$thread_link?reply=$reply->id" ?>' name='<?php echo $reply->id ?>'>
					<?php echo $reply->id ?>
				</a>
			</span>
				<!-- ADMIN SECTION HERE -->
			<?php if($_SESSION['login_user'] ?? '' === $_ENV['ADMIN_USERNAME']): ?>
				<a href='/delete_post/?id=<?php echo $reply->id ?>&board=<?php echo $board->value ?>'>&nbsp;🗑</a>
			</span>
				<!-- ADMIN SECTION HERE -->
			<?php endif; ?>
			<?php if(!is_null($reply->image_id)): ?>
				<a href='/i/<?php echo $reply->image_id ?>'>
					<img 
						loading='lazy' 
						class="image-preview" 
						title='<?php echo $reply->filename ?>' 
						alt='<?php echo $reply->filename ?>' 
						src='/i/thumb/<?php echo $reply->image_id ?>'
					>
				</a><br>
			<?php else: ?>
			<?php endif; ?>
	</div>
	<p><?php echo convert_replies_to_tags(nl2br($reply->comment ?? ''), $board->path() . "/t/$reply->thread_id") ?></p>
</div>