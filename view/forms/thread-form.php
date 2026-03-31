<details id='thread-details'>
	<summary>Make a thread</summary>
	<form method="POST" action="/submit_post" enctype="multipart/form-data">
		<table>
			<tr>
				<th><label for="name">Name: </label></th>
				<td><input value='<?php echo $_COOKIE['last_used_name'] ?? '' ?>' type="text" name="name" placeholder="Anonymous" id="name"></td>
			</tr>
			<tr>
				<th><label for="subject">Subject: </label></th>
				<td><input type="text" name="subject" id="subject" size=25></td>
			</tr>
			<tr>
				<th><label for="comment">Comment: </label></th>
				<td><textarea type="text" name="comment" id="comment" cols=25 rows=6></textarea></td>
			</tr>
			<tr>
				<th><label for="file">File: </label></th>
				<td><input type="file" name="file" id="file"></td>
				<!--  accept='image/*' -->
			</tr>
			<tr>
				<th>Submit: </th>
				<td><button type="submit">Make thread</button></td>
			</tr>
		</table>
	<input type="hidden" name="type" value="thread">
	<input type="hidden" name="board" value=<?php echo "$board->value"; ?>>
	<input type='text' class='option' name='option' value=<?php echo $_SESSION['csrf_token']; ?>>
	</form>
</details>