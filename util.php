<?php

function cookiecheck()
{
	//if cookie is available and timezone isnt set
	if (isset($_COOKIE['user_timezone']) && !isset($_SESSION['user_timezone'])) {
		try {
			$_SESSION['user_timezone'] = new DateTimeZone($_COOKIE['user_timezone']);
		} catch (Throwable) {
			//cookie is bad so unset it
			unset($_COOKIE['user_timezone']);
			// include 'view/js/set-timezone-cookie.php';
		}
	} else if (!isset($_COOKIE['user_timezone']) && !isset($_SESSION['user_timezone'])) {
		// include 'view/js/set-timezone-cookie.php';
		// do nothing, js will add the cookie hopefully
	}
}

//none of this formatting is done in thread ops, idk why it wasnt working
//can fix later
function convert_replies_to_tags(string $input, string $path = ''): string
{
	//OPs cant have these types of replies yet
	if ($path !== '') {
		$reply_pattern = '/&gt;&gt;(\d+)/';
		$reply_replacement = '<a href="' . $path . '#$1">&gt;&gt;$1</a>';
		$res = preg_replace($reply_pattern, $reply_replacement, $input);
	}

	$quote_pattern = '/(^|\s)&gt;([^\n]+)/';
	$quote_replacement = '$1<span class="quote">&gt;$2</span>';
	$res = preg_replace($quote_pattern, $quote_replacement, $res);

	$regular_link_pattern = '/\bhttps?:\/\/[^\s<>"\']+/';
	$regular_link_replacement = '<a href="$0">$0</a>';
	$res = preg_replace($regular_link_pattern, $regular_link_replacement, $res);
	return $res;
}

function generate_thumbnail(string $original_path)
{
	$date = new DateTime();
	$startms = $date->format('Uv');

	try {
		if (str_ends_with($original_path, '.gif')) {
			throw new Exception('Unsupported file format');
		}
		$imagick = new Imagick(realpath($original_path));
		$imagick->thumbnailImage(
			rows: 400,
			columns: false
		);
		$imagick->autoOrient();
		header("Content-Type: image/jpg");
		echo $imagick->getImageBlob();
	} catch (Throwable $e) {
		//if an exception is thrown then give the user
		//the original image
		//if the image doesnt exist then it goes to 404
		$fp = fopen($original_path, 'rb');
		header("Content-Type: image/jpg");
		fpassthru($fp);
		fclose($fp);
	}

	$date = new DateTime();
	$endms = $date->format('Uv');
	$time_taken = $endms - $startms;
	error_log("Thumbnail operation took $time_taken ms");
}