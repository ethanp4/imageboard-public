<?php 
$subject = $thread->subject ?? '';
$fullsubject = $subject;
$body = $thread->comment ?? '';
$fullbody = $body;

$subject = mb_strimwidth($subject, 0, 210, '...');
$body = mb_strimwidth($body, 0, 210, '...');

if (strlen($subject) + strlen($body) >= 220) {
	$subject = mb_strimwidth($subject, 0, 110, '...');
	$body = mb_strimwidth($body, 0, 110, '...');
}
?>
<div class="thread-box">
	<a href='/gn/t/<?php echo $thread->id ?>'>
		<img loading='lazy' class="image-preview" alt='<?php echo $thread->filename ?>' title='<?php echo $thread->filename ?>' src='/i/thumb/<?php echo $thread->image_id ?>'>
	</a>
	<center><div class='faded'><span title='Total replies'><?php echo $thread->reply_count ?></span>/<span title='Media replies'><?php echo $thread->image_count ?></span></div></center>
	<strong title='<?php echo $fullsubject ?>'><?php echo $subject ?></strong>
	<p title='<?php echo $fullbody ?>'><?php echo $body ?></p>
</div>