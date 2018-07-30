<?php
require('/root/site/telegram/function.php');
require('/root/site/telegram/config.php');

while (true) {
	$tlk = tlk(8009521);
	if (!empty($tlk)) {
		sendMsg([
			'bot' => 'ais3',
			'chat_id' => '@ais3_2018',
			'text' => $tlk,
			'parse_mode' => 'HTML'
		]);
	}

	$tlk = tlk(87446);
	if (!empty($tlk)) {
		sendMsg([
			'bot' => 'ais3',
			'chat_id' => -1001054375580,
			'text' => $tlk,
			'parse_mode' => 'HTML'
		]);
	}
}

function tlk($tlkId) {
	$max_id = file_get_contents('/temp/ais3-tlk-id-' . $tlkId);
	$msg = file_get_contents("https://tlk.io/api/chats/$tlkId/messages");
	$msg = json_decode($msg, true);

	$text = "";

	foreach ($msg as $msg) {
		if ($msg['id'] <= $max_id)
			continue;
		else
			$max_id = $msg['id'];

		$name = $msg['nickname'];
		$name = enHTML($name);

		if (substr($name, -5) == ' (TG)')
			continue;

		$body = $msg['body'];
		$body = preg_replace('#<a href="(.*?)" .*?>.*?</a>#s', '\1', $body);
		$body = preg_replace('#<strong>(.*?)</strong>#', '<b>\1</b>', $body);
		$body = preg_replace('#<em>(.*?)</em>#', '<i>\1</i>', $body);
		$body = preg_replace('#<pre><code>(.*?)</code></pre>#', '<code>\1</code>', $body);
		$body = preg_replace('#<pre<code>(.*?)</code></pre>#s', '<pre>\1</pre>', $body);

		$body = preg_replace('#</?((?!\b(b|i|code|pre)\b)[a-z]*?)>#', '', $body);

		$text .= '<b>' . $name . '</b>: ' . $body . "\n\n";
	}

	if (!empty($text))
		file_put_contents('/temp/ais3-tlk-id-' . $tlkId, $max_id);

	return $text;
}
