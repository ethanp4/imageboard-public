<details id='reply-details'>
	<summary>Make a reply</summary>
	<form method="POST" action="/submit_post" enctype="multipart/form-data">
		<table>
			<tr>
					<th><label for="name" >Name: </label></th>
					<td><input value='<?php echo $_COOKIE['last_used_name'] ?? '' ?>' type="text" name="name" placeholder="Anonymous" id="name"></td>
					</tr>
			<tr>
					<th><label for="comment">Comment: </label></th>
					<td><textarea type="text" name="comment" id="comment" cols=25 rows=6></textarea></td>
			</tr>
			<tr>
					<th><label for="file">File: </label></th>
					<td><input type="file" name="file" id="file"></td>
					<!--  accept='image/*' this attribute causes mobile browsers to replace the filename with a big useless number -->
			</tr>
			<tr>
				<th>Submit: </th>
				<td><button type="submit">Post reply</button></td>
			</tr>
		</table>
		<input type="hidden" name="thread-id" value=<?php echo $thread[0]->id ?>>
		<input type="hidden" name="type" value="reply">
		<input type="hidden" name="board" value=<?php echo "$board->value"; ?>>
		<input type='text' class='option' name='option' value=<?php echo $_SESSION['csrf_token']; ?>>
	</form>
</details>